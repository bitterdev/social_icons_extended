<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Bitter\SocialIconsExtended\Entity\SocialIcon;
use Concrete\Core\Application\Application;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Importer;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Http\Client\Client;
use Zend\Http\Request;
use Zend\Http\Response;

class FontGenerator
{
    protected $app;
    protected $entityManager;
    protected $client;
    /** @var \Concrete\Core\File\Service\File */
    protected $fileHelper;
    /** @var \Concrete\Core\Utility\Service\Identifier */
    protected $idHelper;
    /** @var \Concrete\Core\Config\Repository\Repository */
    protected $config;
    protected $importer;
    protected $db;
    /** @var \Stash\Pool */
    protected $expensiveCache;

    public function __construct(Application $app, EntityManager $entityManager, Client $client, Importer $importer, Connection $db)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->fileHelper = $this->app->make("helper/file");
        $this->idHelper = $this->app->make('helper/validation/identifier');
        $this->config = $this->app->make("config");
        $this->expensiveCache = $this->app->make('cache/expensive');
        $this->importer = $importer;
        $this->db = $db;
    }

    public function generateFonts()
    {

        /*
         * Remove old fonts
         */

        foreach ($this->db->fetchAll("SELECT fID FROM SocialIconExtendedFonts") as $row) {
            /** @var $file File */
            $file = \Concrete\Core\File\File::getByID($row["fID"]);

            if ($file instanceof File) {
                try {
                    $file->delete();
                } catch (\Exception $err) {
                    // Error while removing file...
                }
            } else {
                // The file was manually removed from file manager
            }
        }

        $this->db->executeQuery("TRUNCATE TABLE SocialIconExtendedFonts");

        /*
         * On a first try i was generating a SVG font contains all glyphs with:
         * https://github.com/madeyourday/SVG-Icon-Font-Generator
         *
         * But SVG font is only working for LEGACY iOS Safari + old Chrome version.
         *
         * There is no native PHP library for generating/converting to TTF or WOFF fonts.
         *
         * EOT also not. But anyway. EOT file format is just for legacy IE (version <= 10).
         *
         * All i found after research was a good PHP parser library with read only functionality:
         * https://github.com/PhenX/php-font-lib
         *
         * Because there was no good solution i decided to choose a online service
         * for font conversion. Fontello is a cost-free and well-established service for that.
         *
         * Alternatively, it would even be possible to use a server-side CLI application.
         * But that would require, on the one hand, that all users have full server access and
         * know how to install new software.
         */

        $glyphs = [];

        /** @var $socialIcons SocialIcon[] */
        $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findAll();

        $ascent = 850;
        $descent = -150;
        $unitsPerEm = 1000;
        $width = 1000;

        foreach ($socialIcons as $socialIcon) {

            if ($socialIcon->getIcon() instanceof File) {

                // Get the approved version of file object
                $iconFileVersion = $socialIcon->getIcon()->getApprovedVersion();

                if ($iconFileVersion instanceof Version) {
                    if (strtolower($iconFileVersion->getExtension()) === "svg") {
                        $svgDoc = new SvgDocument($iconFileVersion->getFileContents());

                        $viewBox = $svgDoc->getViewBox();

                        $glyphs[] = [
                            'uid' => md5($socialIcon->getId()),
                            'css' => $iconFileVersion->getFileName(),
                            'code' => $socialIcon->getUnicodeCode(),
                            'src' => 'custom_icons',
                            'selected' => true,
                            'svg' => [
                                'path' => $svgDoc->getPath($unitsPerEm / $viewBox["height"], null, 'none', true, 0, $descent), // get composed single path of SVG
                                'width' => $width
                            ],
                            'search' => [
                                $iconFileVersion->getFileName()
                            ]
                        ];

                        /*
                         * Appendix:
                         * Special thanks at Martin Auswöger <martin@madeyourday.co> for the
                         * SVG Document Parser class which is required to build one single path.
                         */
                    }
                }
            }
        }

        if (count($glyphs) > 0) {
            /*
             * Generate config array
             */

            $config = [
                'name' => 'SocialIconsExtended',
                'css_prefix_text' => '',
                'css_use_suffix' => false,
                'hinting' => true,
                'units_per_em' => $unitsPerEm,
                'ascent' => $ascent,
                'glyphs' => $glyphs
            ];

            // First i wanted to use \Concrete\Core\Http\Service\Json class for json encoding, but it's deprecated.
            $jsonConfig = json_encode($config);

            /**
             * Post config to server
             *
             * @docs: https://github.com/fontello/fontello#developers-api
             */

            $request = new Request();

            $request->setUri("https://fontello.com");

            $request->setMethod(Request::METHOD_POST);

            $request->getFiles()->set(
                "config",
                [
                    'formname' => "config",
                    'filename' => "config",
                    'ctype' => "application/json",
                    'data' => $jsonConfig
                ]
            );

            $response = $this->client->send($request);

            if ($response->getStatusCode() == Response::STATUS_CODE_200) {
                $sessionId = $response->getBody();
            } else {
                throw new \Exception($response->getBody());
            }

            /*
             * Download ZIP archive from server
             */

            $request = new Request();

            $request->setUri(sprintf("https://fontello.com/%s/get", $sessionId));

            $response = $this->client->send($request);

            $zipData = $response->getBody();

            /*
             * Save ZIP archive to temporary directory
             */

            $zipFileName = sprintf(
                "%s/%s.zip",
                $this->fileHelper->getTemporaryDirectory(),
                $this->idHelper->getString(8)
            );

            $this->fileHelper->append($zipFileName, $zipData);

            /*
             * Extract the ZIP archive
             */

            $extractedFiles = [];

            $tempDir = sprintf(
                "%s/%s/",
                $this->fileHelper->getTemporaryDirectory(),
                $this->idHelper->getString(8)
            );

            $zipFile = new \ZipArchive();

            if ($zipFile->open($zipFileName)) {
                if ($zipFile->extractTo($tempDir)) {
                    $extractedFiles = $this->fileHelper->getDirectoryContents($tempDir, [], true);
                }

                $zipFile->close();
            }

            /*
             * Update allowed file extensions
             */

            $fontFileExtensions = ["eot", "svg", "ttf", "woff", "woff2"];

            $allowedFileExtensions = explode(";", $this->config->get('concrete.upload.extensions'));

            foreach ($fontFileExtensions as $fontFileExtension) {
                if (!in_array("*." . $fontFileExtension, $allowedFileExtensions)) {
                    $allowedFileExtensions[] = "*." . $fontFileExtension;
                }
            }

            $this->config->save('concrete.upload.extensions', implode(";", $allowedFileExtensions));

            /*
             * Update fonts
             */

            foreach ($extractedFiles as $extractedFile) {
                $pathParts = pathinfo($extractedFile);

                if (in_array($pathParts["extension"], $fontFileExtensions)) {
                    $fileVersion = $this->importer->import($extractedFile);

                    if ($fileVersion instanceof Version) {
                        $this->db->insert("SocialIconExtendedFonts", [
                            "fontFormat" => $pathParts["extension"],
                            "fID" => $fileVersion->getFile()->getFileID()
                        ]);
                    } else {
                        // Error while importing the file
                    }
                }
            }

            /*
             * Cleanup
             */

            $this->fileHelper->removeAll($tempDir);

            // Thanks to mnakalay for single file deletion core wrapper code:
            $pathInfo = pathinfo($zipFileName);
            $local = new Local($pathInfo["dirname"]);
            $fs = new Filesystem($local);
            $fs->delete($pathInfo["basename"]);

            /*
             * Clear caches
             */

            $this->expensiveCache->getItem("SocialIconsExtended/CssCache")->clear();

            /*
             * Update config
             */

            /** @var $socialIcons SocialIcon[] */
            $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findAll();

            $additionalServices = [];

            foreach ($socialIcons as $socialIcon) {
                $additionalServices[] = [
                    $socialIcon->getHandle(),
                    $socialIcon->getName(),
                    $socialIcon->getFontAwesomeHandle()
                ];
            }

            $this->config->save('concrete.social.additional_services', $additionalServices);

        } else {
            $this->db->executeQuery("TRUNCATE TABLE SocialIconExtendedFonts");

            /*
             * Clear caches
             */

            $this->expensiveCache->getItem("SocialIconsExtended/CssCache")->clear();
        }

    }

}
