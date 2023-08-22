<?php

/**
 * @project:   Social Icons Extended
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2018 Fabian Bitter
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="btn-group">
    <a href="<?php echo $url; ?>" class="btn btn-primary">
        <i class="fa fa-<?php echo $faIconClass; ?>"></i> <?php echo $label; ?>
    </a>
</div>