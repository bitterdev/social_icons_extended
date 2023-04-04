<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

/** @var $packageService \Concrete\Core\Package\PackageService */
$packageService = $app->make(\Concrete\Core\Package\PackageService::class);

/** @var $pkg \Concrete\Core\Package\Package */
$pkg = $packageService->getByHandle($packageHandle);

?>

<?php if (date("Y") <= 2019 && is_object($pkg) && !$pkg->getConfig()->get('reminder.hide')): ?>
    <div class="alert alert-info alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="hideAlert();">&times;</a>

        <?php echo t("Rate this add-on on concrete5.org and as a thank-you gift can choose another add-on of mine up to $35. This offer may only be claimed once. Click <a href=\"%s\" class=\"alert-link\"> here </a> to rate now.", $rateUrl); ?>
    </div>

    <script>
        var hideAlert = function() {
            $.get("<?php echo URL::to("/bitter/" . $pkg->getPackageHandle() . "/reminder/hide"); ?>");
        };
    </script>
<?php endif; ?>