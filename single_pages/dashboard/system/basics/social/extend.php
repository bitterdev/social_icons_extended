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

/** @var $socialIcons \Bitter\SocialIconsExtended\Entity\SocialIcon[] */

?>

<?php \Concrete\Core\View\View::element('/dashboard/license_check', array("packageHandle" => "social_icons_extended"), 'social_icons_extended'); ?>

<?php if (count($socialIcons) == 0): ?>
    <div class="alert alert-warning">
        <?php echo t("This page is blank because you have not defined any additional social links yet. Click on the »Add« button in the upper right corner of the toolbar to create a new link."); ?>
    </div>
<?php else: ?>
    <table class="ccm-search-results-table">
        <thead>
            <tr>
                <th colspan="2">
                    <a href="javascript:void(0);">
                        <?php echo t("Name"); ?>
                    </a>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($socialIcons as $socialIcon): ?>
                <tr>
                    <td>
                        <?php echo $socialIcon->getName(); ?>
                    </td>

                    <td>
                        <div class="pull-right">
                            <a href="<?php echo $this->action("edit", $socialIcon->getId()); ?>" class="btn btn-default">
                                <i class="fa fa-pencil"></i> <?php echo t("Edit"); ?>
                            </a>

                            <a href="<?php echo $this->action("remove", $socialIcon->getId()); ?>" class="btn btn-danger"
                               onclick="return confirm('<?php echo h(t("Are you sure?")); ?>');">
                                <i class="fa fa-trash-o"></i> <?php echo t("Remove"); ?>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php \Concrete\Core\View\View::element('/dashboard/did_you_know', array("packageHandle" => "social_icons_extended"), 'social_icons_extended'); ?>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?php echo URL::to("/dashboard/system/basics/social"); ?>" class="btn btn-default">
            <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
        </a>
    </div>
</div>
