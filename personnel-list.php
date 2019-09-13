<?php

require __DIR__ . '/vendor/autoload.php';

use JoseMartos\PersonnelList\Activator;
use JoseMartos\PersonnelList\Deactivator;
use JoseMartos\PersonnelList\PersonnelList;

/**
 * Plugin Name: Personnel List
 * Plugin URI: https://developer.wordpress.org/plugins
 * Description: WordPress plugin to handle the list of members within a company.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: Jose Martos
 * Author URI: http://www.martosjose.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: personnel-list
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PERSONNEL_LIST_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in src/Activator.php
 */
function activate_personnel_list()
{
	$activator = new Activator();
	$activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in src/Deactivator.php
 */
function deactivate_personnel_list()
{
	$deactivator = new Deactivator();
	$deactivator->deactivate();
}

register_activation_hook(__FILE__, 'activate_personnel_list');
register_deactivation_hook(__FILE__, 'deactivate_personnel_list');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_personnel_list()
{
	$plugin = new PersonnelList();
	$plugin->run();
}
run_personnel_list();
