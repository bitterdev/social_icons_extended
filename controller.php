<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Concrete\Package\SocialIconsExtended;

use Bitter\SocialIconsExtended\Installer\Installer;
use Bitter\SocialIconsExtended\Provider\ServiceProvider;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected $pkgHandle = 'social_icons_extended';
    protected $pkgVersion = '1.5.0';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/SocialIconsExtended' => 'Bitter\SocialIconsExtended',
    ];

    public function getPackageName()
    {
        return t('Social Icons Extended');
    }

    public function getPackageDescription()
    {
        return t('Extend the social services on your site by any other social service. You can add an SVG icon for each defined service.');
    }

    public function on_start()
    {
        /** @var $serviceProvider ServiceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function install()
    {
        if (!class_exists('\ZipArchive', false)) {
            throw new \Exception(t("ZipArchive is required."));
        }

        $pkg = parent::install();

        /** @var $installer Installer */
        $installer = $this->app->make(Installer::class);

        $installer->install($pkg);
    }
}
