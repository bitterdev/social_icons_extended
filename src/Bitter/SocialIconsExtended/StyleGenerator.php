<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended;

use Bitter\SocialIconsExtended\Entity\SocialIcon;
use Concrete\Core\Application\Application;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Site\Service;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class StyleGenerator
{

    protected $app;
    protected $entityManager;
    /** @var \Stash\Pool */
    protected $expensiveCache;
    protected $db;

    public function __construct(Application $app, EntityManager $entityManager, Connection $db)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        $this->expensiveCache = $this->app->make('cache/expensive');
        $this->db = $db;
    }

    /**
     *
     * @return string
     */
    public function generateCSS()
    {
        /*
         * Check if CSS is already cached?
         */
        /** @var Service $siteService */
        $siteService = $this->app->make(Service::class);
        $site = $siteService->getSite();

        $cacheObject = $this->expensiveCache->getItem("SocialIconsExtended/CssCache/" . $site->getSiteID());

        if ($cacheObject->isMiss()) {

            /** @var $socialIcons SocialIcon[] */
            $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findBy(["site" => $site]);

            /*
             * Generate the CSS
             */

            if (count($socialIcons) > 0) {

                $fontTypeMapping = [
                    "eot" => 'embedded-opentype',
                    "woff2" => 'woff2',
                    "woff" => 'woff',
                    "ttf" => 'truetype',
                    "svg" => 'svg'
                ];

                $fontCss = "";

                foreach ($this->db->fetchAll("SELECT fID, fontFormat FROM SocialIconExtendedFonts") as $row) {
                    /** @var $file File */
                    $file = \Concrete\Core\File\File::getByID($row["fID"]);

                    if ($file instanceof File) {
                        $fileVersion = $file->getApprovedVersion();

                        if ($fileVersion instanceof Version) {
                            $fontCss .= sprintf(
                                "%surl('%s') format('%s')",
                                (strlen($fontCss) > 0 ? ",\n  " : ""),
                                $fileVersion->getURL(),
                                $fontTypeMapping[$row["fontFormat"]]
                            );
                        }
                    }
                }

                $css = sprintf(
                    "@font-face {\n" .
                    "  font-family: \"SocialIconsExtended\";\n" .
                    "  src: %s;\n" .
                    "}\n\n",

                    $fontCss
                );

                foreach ($socialIcons as $socialIcon) {
                    $css .= sprintf(
                        ".fa.fa-%s::before {\n" .
                        "  font-family: 'SocialIconsExtended';\n" .
                        "  speak: none;\n" .
                        "  font-style: normal;\n" .
                        "  font-weight: normal;\n" .
                        "  font-variant: normal;\n" .
                        "  text-transform: none;\n" .
                        "  line-height: 1;\n" .
                        "  -webkit-font-smoothing: antialiased;\n" .
                        "  -moz-osx-font-smoothing: grayscale;\n" .
                        "  content: \"\\%s\";\n" .
                        "}\n\n",

                        $socialIcon->getFontAwesomeHandle(),
                        dechex($socialIcon->getUnicodeCode())
                    );
                }

            } else {
                $css = "";
            }

            /*
             * Store generated CSS in cache
             */

            $cacheObject->set($css);

            $this->expensiveCache->save($cacheObject);

        } else {
            /*
             * Retrieve CSS font from cache
             */
            $css = $cacheObject->get();
        }

        return $css;
    }
}