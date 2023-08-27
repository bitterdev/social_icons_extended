<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

/** @var $socialIcon \Bitter\SocialIconsExtended\Entity\SocialIcon */
/** @var $form \Concrete\Core\Form\Service\Form */
/** @var $token \Concrete\Core\Validation\CSRF\Token */

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

/** @var $fileManager \Concrete\Core\Application\Service\FileManager */
$fileManager = $app->make('helper/concrete/file_manager');

?>
<div class="ccm-dashboard-header-buttons">
    <?php \Concrete\Core\View\View::element("dashboard/help", [], "social_icons_extended"); ?>
</div>

<form action="#" method="post">
    <?php echo $token->output('save_icon'); ?>

    <div class="form-group">
        <?php echo $form->label("handle", t("Handle")); ?>
        <?php echo $form->text("handle", $socialIcon->getHandle(), ["class" => "form-control", "maxlength" => 255]); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("name", t("Name")); ?>
        <?php echo $form->text("name", $socialIcon->getName(), ["class" => "form-control", "maxlength" => 255]); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("icon", t("SVG Icon")); ?>
        <?php echo $fileManager->image("icon","icon", t("Choose file"), $socialIcon->getIcon()); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo \Concrete\Core\Support\Facade\Url::to("/dashboard/system/basics/social/extend"); ?>" class="btn btn-secondary">
                <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
            </a>

            <div class="float-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </div>
</form>
