<?php

namespace JoseMartos\PersonnelList;

use JoseMartos\PersonnelList\Loader;
use JoseMartos\PersonnelList\Languages;
use JoseMartos\PersonnelList\Controllers\Persons;
use JoseMartos\PersonnelList\Admin\Admin;
use JoseMartos\PersonnelList\Block\Block;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * gutenberg block hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    JoseMartos/PersonnelList
 * 
 */
class PersonnelList
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the gutenberg block.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('PERSONNEL_LIST_VERSION')) {
			$this->version = PERSONNEL_LIST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'personnel-list';
		// Orchestrates the hooks of the plugin
		$this->loader = new Loader();

		$this->set_locale();
		$this->set_api();
		$this->define_admin_hooks();
		$this->define_block_hooks();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Personnel_List_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Languages();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function set_api()
	{
		$api_controller = new Persons($this->get_plugin_name());

		$this->loader->add_action('rest_api_init', $api_controller, 'register_routes');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'register_menu');
		$this->loader->add_action('init', $plugin_admin, 'person_cpt');
	}

	/**
	 * Register all of the hooks related to the gutenberg block functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_block_hooks()
	{
		$plugin_block = new Block($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_footer', $plugin_block, 'render_modal');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_block, 'enqueue_scripts');
		$this->loader->add_action('init', $plugin_block, 'register_block');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
