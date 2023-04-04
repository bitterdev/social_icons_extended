<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended\Listeners\IconDelete;

use Bitter\SocialIconsExtended\Entity\SocialIcon;
use Bitter\SocialIconsExtended\FontGenerator;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Concrete\Core\Support\Facade\Application;

class RemoveIconFile
{
    protected $app;
    /** @var Connection */
    protected $db;
    /** @var FontGenerator */
    protected $fontGenerator;

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
        $this->db = $this->app->make(Connection::class);
        $this->fontGenerator = $this->app->make(FontGenerator::class);
    }

    public function postRemove(SocialIcon $socialIcon)
    {
        /*
         * Remove social icons that are no more available to avoid conflicts with concrete5
         */

        $this->db->executeQuery("DELETE btsl.* FROM btSocialLinks AS btsl LEFT JOIN SocialLinks AS sl ON (sl.`slID` = btsl.slID) WHERE sl.ssHandle = ?", [$socialIcon->getHandle()]);

        /*
         * Remove associated social links entries
         */

        $this->db->executeQuery("DELETE FROM SocialLinks WHERE ssHandle = ?", [$socialIcon->getHandle()]);

        /*
         * Generate the fonts
         */

        $this->fontGenerator->generateFonts();
    }

    public function postPersist()
    {
        /*
         * Generate the fonts
         */

        $this->fontGenerator->generateFonts();
    }

    public function preUpdate(SocialIcon $socialIcon, PreUpdateEventArgs $args)
    {
        $changeArray = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($args->getObject());

        if (is_array($changeArray) && is_array($changeArray["handle"]) && count($changeArray["handle"]) === 2) {
            $oldHandle = $changeArray["handle"][0];
            $newHandle = $changeArray["handle"][1];

            /*
             * Update social icons
             */

            $this->db->executeQuery("UPDATE SocialLinks SET ssHandle = ? WHERE ssHandle = ?", [$newHandle, $oldHandle]);
        }

    }

    public function postUpdate()
    {
        /*
         * Generate the fonts
         */

        $this->fontGenerator->generateFonts();
    }
}