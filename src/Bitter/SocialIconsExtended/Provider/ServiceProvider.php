<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended\Provider;

use Bitter\SocialIconsExtended\RouteList;
use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Site\Service;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;
use Bitter\SocialIconsExtended\StyleGenerator;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Http\Response;
use Bitter\SocialIconsExtended\Entity\SocialIcon;
use Concrete\Core\Entity\File\Version;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $dispatcher;
    /** @var RouterInterface */
    protected $router;
    protected $entityManager;
    /** @var \Concrete\Core\Package\Package */
    protected $pkg;

    public function __construct(Application $app, EventDispatcherInterface $dispatcher, EntityManager $entityManager, PackageService $packageService)
    {
        $this->app = $app;
        $this->dispatcher = $dispatcher;
        $this->router = $this->app->make(RouterInterface::class);
        $this->entityManager = $entityManager;
        $this->pkg = $packageService->getByHandle("social_icons_extended");
    }

    public function register()
    {
        $this->dispatcher->addListener('on_before_dispatch', function () {
            $view = View::getInstance();

            /** @var Service $siteService */
            $siteService = $this->app->make(Service::class);
            $site = $siteService->getSite();
            /** @var Repository $config */
            /** @noinspection PhpUnhandledExceptionInspection */
            $config = $this->app->make(Repository::class);

            /** @var $socialIcons SocialIcon[] */
            $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findBy(["site" => $site]);

            $items = [];

            $additionalServices = $config->get("concrete.social.additional_services", []);

            foreach ($socialIcons as $socialIcon) {
                if ($socialIcon->getIcon() instanceof File) {
                    // Get the approved version of file object
                    $iconFileVersion = $socialIcon->getIcon()->getApprovedVersion();

                    if ($iconFileVersion instanceof Version) {
                        if (strtolower($iconFileVersion->getExtension()) === "svg") {
                            $items[] = [
                                "id" => $socialIcon->getId(),
                                "handle" => $socialIcon->getHandle()
                            ];

                            $additionalServices[] = [
                                $socialIcon->getHandle(),
                                $socialIcon->getName(),
                                "custom-icon fa fa-" . $socialIcon->getFontAwesomeHandle()
                            ];
                        }
                    }
                }
            }

            $config->set("concrete.social.additional_services", $additionalServices);

            if (count($items) > 0) {
                sort($items);

                $hash = crc32(serialize($items));

                $view->addHeaderItem(
                    sprintf(
                        "<link rel=\"stylesheet\" href=\"%s\">",
                        h(Url::to("/bitter/social-icons-extended.css")->setQuery(["ts" => $hash]))
                    )
                );
            }
        });

        /*
         * Route for CSS
         */

        $this->router->register("/bitter/social-icons-extended.css", function () {
            /** @var $styleGenerator StyleGenerator */
            $styleGenerator = $this->app->make(StyleGenerator::class);

            return new Response(
                $styleGenerator->generateCSS(),
                200,
                [
                    "Content-Type" => "text/css"
                ]
            );
        });

        $list = new RouteList();
        $list->loadRoutes($this->router);
    }

}
