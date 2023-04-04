<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

View::element('/dashboard/help', null, 'social_icons_extended');
View::element('/dashboard/reminder', array("packageHandle" => "social_icons_extended", "rateUrl" => "https://www.concrete5.org/marketplace/addons/social-icons-extended/reviews"), 'social_icons_extended');

/** @var $socialIcon \Bitter\SocialIconsExtended\Entity\SocialIcon */
/** @var $form \Concrete\Core\Form\Service\Form */
/** @var $token \Concrete\Core\Validation\CSRF\Token */

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

/** @var $fileManager \Concrete\Core\Application\Service\FileManager */
$fileManager = $app->make('helper/concrete/file_manager');

?>

<?php \Concrete\Core\View\View::element('/dashboard/license_check', array("packageHandle" => "social_icons_extended"), 'social_icons_extended'); ?>


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

    <?php \Concrete\Core\View\View::element('/dashboard/did_you_know', array("packageHandle" => "social_icons_extended"), 'social_icons_extended'); ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo URL::to("/dashboard/system/basics/social/extend"); ?>" class="btn btn-default">
                <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
            </a>

            <div class="pull-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i> <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </div>
</form>
