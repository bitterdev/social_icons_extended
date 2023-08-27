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
use Concrete\Core\Package\PackageService;
use Concrete\Core\Site\Service;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;
use Bitter\SocialIconsExtended\StyleGenerator;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Http\Response;
use Bitter\SocialIconsExtended\Entity\SocialIcon;
use Concrete\Core\Entity\File\Version;

class ServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $dispatcher;
    /** @var RouterInterface */
    protected $router;
    protected $entityManager;
    /** @var \Concrete\Core\Package\Package */
    protected $pkg;

    public function __construct(Application $app, EventDispatcher $dispatcher, EntityManager $entityManager, PackageService $packageService)
    {
        $this->app = $app;
        $this->dispatcher = $dispatcher;
        $this->router = $this->app->make(RouterInterface::class);
        $this->entityManager = $entityManager;
        $this->pkg = $packageService->getByHandle("social_icons_extended");
    }

    public function register()
    {
        /*
         * Inject Code
         */

        $this->dispatcher->addListener('on_before_render', function () {
            $view = View::getInstance();

            $items = [];

            /** @var Service $siteService */
            $siteService = $this->app->make(Service::class);
            $site = $siteService->getSite();

            /** @var $socialIcons SocialIcon[] */
            $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findBy(["site" => $site]);

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
                        }
                    }
                }
            }

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

        /**
         * Hook file delete event
         */

        $this->dispatcher->addListener('on_file_delete', function ($argument) {
            /** @var $argument \Concrete\Core\File\Event\DeleteFile */

            /** @var $entityManager EntityManager */
            $entityManager = $this->app->make(EntityManager::class);

            $file = $argument->getFileObject();

            if ($file instanceof File) {
                /*
                 * Check if there is are icon entities associated with the file
                 */

                /** @var $associatedIconEntities SocialIcon[] */
                $associatedIconEntities = $entityManager->getRepository(SocialIcon::class)->findBy(["icon" => $file]);

                if (count($associatedIconEntities) > 0) {
                    foreach ($associatedIconEntities as $associatedIconEntity) {
                        if ($associatedIconEntity instanceof SocialIcon) {
                            // If yes - delete
                            $entityManager->remove($associatedIconEntity);
                            $entityManager->flush();
                        }
                    }
                }
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
