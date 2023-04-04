<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\SocialIconsExtended\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\EntityListeners({"\Bitter\SocialIconsExtended\Listeners\IconDelete\RemoveIconFile"})
 * @ORM\Table(name="SocialIcon")
 */
class SocialIcon
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\File\File")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID", onDelete="CASCADE")
     *
     * @var \Concrete\Core\Entity\File\File|null
     */
    protected $icon = null;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $handle = '';

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $name = '';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param \Concrete\Core\Entity\File\File|null $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUnicodeCode()
    {
        return hexdec('e001') + $this->getId();
    }

    /**
     * @return string
     */
    public function getFontAwesomeHandle()
    {
        return str_replace("_", "-", $this->handle);
    }

}
