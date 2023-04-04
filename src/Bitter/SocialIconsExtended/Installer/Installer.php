<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended\Installer;

use Concrete\Core\Page\Single;

class Installer
{
    /**
     * @param \Concrete\Core\Entity\Package $pkg
     */
    public function install($pkg)
    {
        $dashboardPage = Single::add("/dashboard/system/basics/social/extend", $pkg);

        if ($dashboardPage) {
            $dashboardPage->update([
                'cName' => t("Extend")
            ]);
        }
    }
}