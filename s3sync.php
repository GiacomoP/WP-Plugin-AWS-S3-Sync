<?php

/**
 * Plugin Name: Amazon S3 Sync
 * Description: A plugin to sync files on Amazon S3
 * Version: 1.1
 * Author: Giacomo Persichini
 * Author URI: http://giacomo.pw
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.html
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Composer
require_once 'vendor/autoload.php';

use S3\App;

App::run();

register_activation_hook(__FILE__, ['\S3\App', 'runActivation']);
register_deactivation_hook(__FILE__, ['\S3\App', 'runDeactivation']);