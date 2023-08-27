<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

namespace Concrete\Package\SocialIconsExtended\Controller\SinglePage\Dashboard\System\Basics\Social;

use Bitter\SocialIconsExtended\Controllers\HeaderController;
use Bitter\SocialIconsExtended\Entity\SocialIcon;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Extend extends DashboardSitePageController
{
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ResponseFactoryInterface */
    protected $responseFactory;
    /** @var Request */
    protected $request;
    /** @var \Concrete\Core\Utility\Service\Validation\Strings */
    protected $stringValidator;

    public function on_start()
    {
        parent::on_start();

        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $this->request = $this->app->make(Request::class);
        $this->stringValidator = $this->app->make("helper/validation/strings");
    }

    /**
     * @param bool $isNewIcon
     * @return bool
     */
    private function validate($isNewIcon = false)
    {
        $handle = $this->request->request->get("handle");
        $name = $this->request->request->get("name");
        $iconFileId = $this->request->request->get("icon");

        if (strlen($handle) === 0) {
            $this->error->add(t("You have to enter a handle."));
        }

        if (!$this->stringValidator->handle($handle)) {
            $this->error->add(t("The entered handle is invalid."));
        }

        $iconEntity = $this->entityManager->getRepository(SocialIcon::class)->findOneBy(["handle" => $handle]);

        if ($isNewIcon && $iconEntity instanceof SocialIcon) {
            $this->error->add(t("The entered handle is already in use."));
        }

        if (strlen($name) === 0) {
            $this->error->add(t("You have to enter a name."));
        }

        /** @var $file \Concrete\Core\Entity\File\File */
        $file = File::getByID($iconFileId);

        if ($file instanceof \Concrete\Core\Entity\File\File) {
            $fileVersion = $file->getApprovedVersion();

            if ($fileVersion instanceof Version) {
                if (!strtolower($fileVersion->getExtension()) === "svg") {
                    $this->error->add(t("Invalid file format. Only SVG files are allowed."));
                }
            } else {
                $this->error->add(t("The selected file has no approved version."));
            }
        } else {
            $this->error->add(t("You have to select a icon file."));
        }

        if (($connection = @fsockopen("fontello.com", 80)) === false){
            $this->error->add(t("You need an active internet connection."));
        } else{
            fclose($connection);
        }

        return !$this->error->has();
    }

    public function add()
    {
        $socialIcon = new SocialIcon();

        if ($this->token->validate("save_icon") && $this->validate(true)) {

            /*
             * Update Entry
             */

            $socialIcon->setSite($this->getSite());
            $socialIcon->setName($this->request->request->get("name"));
            $socialIcon->setHandle($this->request->request->get("handle"));
            $socialIcon->setIcon(File::getByID($this->request->request->get("icon")));

            $this->entityManager->persist($socialIcon);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/system/basics/social/extend"), Response::HTTP_FOUND);
        }

        $this->set("socialIcon", $socialIcon);

        $this->render("/dashboard/system/basics/social/extend/edit");
    }

    public function remove($id = null)
    {
        /** @var $socialIcon SocialIcon */
        $socialIcon = $this->entityManager->getRepository(SocialIcon::class)->findOneBy(["id" => $id]);

        if ($socialIcon instanceof SocialIcon) {
            $this->entityManager->remove($socialIcon);
            $this->entityManager->flush();

        }

        return $this->responseFactory->redirect(Url::to("/dashboard/system/basics/social/extend"), Response::HTTP_FOUND);
    }

    public function edit($id = null)
    {
        /** @var $socialIcon SocialIcon */
        $socialIcon = $this->entityManager->getRepository(SocialIcon::class)->findOneBy(["id" => $id]);

        if ($socialIcon instanceof SocialIcon) {
            if ($this->token->validate("save_icon") && $this->validate(false)) {

                /*
                 * Update Entry
                 */

                $socialIcon->setSite($this->getSite());
                $socialIcon->setName($this->request->request->get("name"));
                $socialIcon->setHandle($this->request->request->get("handle"));
                $socialIcon->setIcon(File::getByID($this->request->request->get("icon")));

                $this->entityManager->persist($socialIcon);
                $this->entityManager->flush();


                return $this->responseFactory->redirect(Url::to("/dashboard/system/basics/social/extend"), Response::HTTP_FOUND);
            }

            $this->set("socialIcon", $socialIcon);

            $this->render("/dashboard/system/basics/social/extend/edit");
        } else {
            return $this->responseFactory->redirect(Url::to("/dashboard/system/basics/social/extend"), Response::HTTP_FOUND);
        }
    }

    public function view()
    {
        /** @var $socialIcons SocialIcon[] */
        $socialIcons = $this->entityManager->getRepository(SocialIcon::class)->findBy(["site" => $this->getSite()]);

        $header = new HeaderController();

        $header->setUrl($this->action("/add"));

        $this->set('headerMenu', $header);
        $this->set("socialIcons", $socialIcons);
    }

}
