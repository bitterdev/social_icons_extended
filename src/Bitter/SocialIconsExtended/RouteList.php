<?php

/**
 * @project:   BitterTheme
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\SocialIconsExtended\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/social_icons_extended')
            ->routes('dialogs/support.php', 'social_icons_extended');
    }
}