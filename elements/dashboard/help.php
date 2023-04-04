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

$app->make('help')->display(t("If you need support please click <a href=\"%s\">here</a>.", "https://bitbucket.org/fabianbitter/social_icons_extended/issues/new"));