<?php

/**
 * Plugin Update Checker Library 5.4
 * http://w-shadow.com/
 *
 * Copyright 2024 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/load-v5p4.php';

require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
  'https://github.com/Media-Maven-Tlv/lego-eilat-plugin',
  __FILE__,
  'woocommerce-eilat-mode'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('your-token-here');
