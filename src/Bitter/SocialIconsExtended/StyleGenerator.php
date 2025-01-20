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
use Concrete\Core\Site\Service;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class StyleGenerator
{

    protected $app;
    protected $entityManager;
    protected $expensiveCache;
    protected $db;

    public function __construct(Application $app, EntityManager $entityManager, Connection $db)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->expensiveCache = $this->app->make('cache/expensive');
        $this->db = $db;
    }

    public function generateCSS(): string
    {
        /*
         * Check if CSS is already cached?
         */
        /** @var Service $siteService */
        /** @noinspection PhpUnhandledExceptionInspection */
        $siteService = $this->app->make(Service::class);
        $site = $siteService->getSite();

        $cacheObject = $this->expensiveCache->getItem("SocialIconsExtended/CssCache/" . $site->getSiteID());

        if ($cacheObject->isMiss() || true) {

            /** @var $socialIcons SocialIcon[] */
            /** @noinspection PhpUnhandledExceptionInspection */
            $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findBy(["site" => $site]);

            $css = "";

            /*
             * Generate the CSS
             */

            if (count($socialIcons) > 0) {
                foreach ($socialIcons as $socialIcon) {
                    $css .= sprintf(
                        ".fa.fa-%s {\n" .
                        "position: relative;\n" .
                        "display: inline-block;\n" .
                        "width: 16px;\n" .
                        "height: 16px;\n" .
                        "}\n\n" .
                        ".fa.fa-%s::before {\n" .
                        "content: \"\";\n" .
                        "position: absolute;\n" .
                        "background-color: var(--bs-body-color);\n" .
                        "-webkit-mask-image: url(%s);\n" .
                        "mask-image: url(%s);\n" .
                        "mask-repeat: no-repeat;\n" .
                        "mask-position: center;\n" .
                        "mask-size: cover;\n" .
                        "width: 16px;\n" .
                        "height: 16px;\n" .
                        "}\n\n",

                        $socialIcon->getFontAwesomeHandle(),
                        $socialIcon->getFontAwesomeHandle(),
                        h($socialIcon->getIcon()->getApprovedVersion()->getURL()),
                        h($socialIcon->getIcon()->getApprovedVersion()->getURL())
                    );
                }

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