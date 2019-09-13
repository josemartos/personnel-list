<?php

namespace JoseMartos\PersonnelList\Block;

/**
 * The gutenberg block functionality of the plugin.
 *
 * Defines the plugin name, version, and registers the block.
 *
 * @package    JoseMartos\PersonnelList\Block
 * 
 */
class Block
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * It renders the modal HTML base markup
	 *
	 * @since    1.0.0
	 */
	public function render_modal()
	{
		ob_start();
		include_once 'partials/pl_modal.php';
		echo ob_get_clean();
	}

	/**
	 * It renders one list item
	 *
	 * @since    1.0.0
	 * @param    WP_Post $post
	 */
	private function list_item_row($post)
	{
		$metadata = ["position", "short_description", "github", "linkedin", "xing", "facebook"];
		$info = [];

		// Populate info
		$info["name"] = get_the_title($post);
		foreach ($metadata as $meta) {
			$info[$meta] = get_post_meta($post->ID, '_personnel_list_' . $meta, true);
		}

		$serialized = htmlspecialchars(json_encode($info));

		$html =  '<li data-content="' . $serialized . '" class="js-personnelList_item personnelList_item personnelList_item-hoverable">';
		$html .=		'<img src="https://via.placeholder.com/50" alt="' . $info["name"] . '" />';
		$html .=		'<div class="personnelList_item_info">';
		$html .=			'<h2 class="personnelList_item_title">' . $info["name"];
		$html .=				' - <span class="personnelList_item_position">' . $info["position"] . '</span>';
		$html .=			'</h2>';
		$html .=		'</div>';
		$html .= '</li>';

		return $html;
	}

	/**
	 * It renders the created block
	 *
	 * @since    1.0.0
	 * @param    array    $attributes
	 */
	public function render_person_callback($attributes)
	{
		$persons = $attributes['persons'];
		$posts = get_posts(array(
			"include" => $persons,
			"post_type" => "person"
		));
		$html = "";

		foreach ($posts as $post) {
			$html .= $this->list_item_row($post);
		}

		return '<div><ul>' . $html . '</ul></div>';
	}

	/**
	 * Register the JavaScript for the public side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name . '-public-js', plugins_url('../frontend/dist/public.js', dirname(__FILE__)), array(), $this->version, true);

		wp_localize_script(
			$this->plugin_name . '-public-js',
			'wpSettings',
			array(
				'pluginUrl'   => plugins_url() . '/' . $this->plugin_name
			)
		);
	}

	/**
	 * Register the gutenberg block.
	 *
	 * @since    1.0.0
	 */
	public function register_block()
	{
		wp_register_script(
			$this->plugin_name . '-block-js',
			plugins_url('../frontend/dist/block.js', dirname(__FILE__)),
			array('wp-blocks', 'wp-data')
		);

		wp_register_style(
			$this->plugin_name . '-block-css',
			plugins_url('../frontend/dist/block.css', dirname(__FILE__)),
			array('wp-edit-blocks')
		);

		register_block_type($this->plugin_name . '/add-person', array(
			'style' => $this->plugin_name . '-block-css',
			'editor_style' => $this->plugin_name . '-block-css',
			'editor_script' => $this->plugin_name . '-block-js',
			'render_callback' => array($this, 'render_person_callback'),
			'attributes' => array(
				'persons' => array(
					'type' => 'array'
				)
			)
		));
	}
}
