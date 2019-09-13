<?php

namespace JoseMartos\PersonnelList\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    JoseMartos\PersonnelList\Admin
 * 
 */
class Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name . '-admin-css', plugins_url('../frontend/dist/admin.css', dirname(__FILE__)), array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name . '-admin-js', plugins_url('../frontend/dist/admin.js', dirname(__FILE__)), array(), $this->version, true);

		wp_localize_script(
			$this->plugin_name . '-admin-js',
			'wpApiSettings',
			array(
				'nonce'   => wp_create_nonce('wp_rest'),
				'url'	  => rest_url($this->plugin_name . '/v1'),
			)
		);
	}

	/**
	 * Admin root element
	 */
	public function admin_layout()
	{
		echo '<div class="wrap"><div id="personnel-list-admin"></div></div>';
	}

	/**
	 * Register the menu for the sidebar.
	 *
	 * @since    1.0.0
	 */
	public function register_menu()
	{
		add_menu_page(
			'Personnel List',
			'Personnel List',
			'manage_options',
			$this->plugin_name,
			array($this, 'admin_layout'),
			'dashicons-id-alt'
		);
	}

	/**
	 * Register the custom type person
	 */
	public function person_cpt()
	{
		$args = array(
			'public'       => false,
			'show_in_rest' => true,
			'label'        => 'Persons',
			'rest_base'		 => 'persons'
		);
		register_post_type('person', $args);
	}
}
