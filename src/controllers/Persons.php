<?php

namespace JoseMartos\PersonnelList\Controllers;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * The file that defines the rest api custom endpoints
 *
 * @package JoseMartos\PersonnelList\Controllers
 */

class Persons extends WP_REST_Controller
{
  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   */
  public function __construct($plugin_name)
  {
    $this->plugin_name = $plugin_name;
  }

  /**
   * Register the routes for the objects of the controller.
   */
  public function register_routes()
  {
    $version = '1';
    $namespace = $this->plugin_name . '/v' . $version;
    $base = 'persons';
    register_rest_route($namespace, '/' . $base, array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'get_items'),
        'permission_callback' => array($this, 'get_items_permissions_check'),
        'args'                => array(),
      ),
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array($this, 'create_item'),
        'permission_callback' => array($this, 'create_item_permissions_check'),
        'args'                => $this->get_endpoint_args_for_item_schema(true),
      ),
      'schema' => 'person_schema'
    ));
    register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
      array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => array($this, 'get_item'),
        'permission_callback' => array($this, 'get_item_permissions_check'),
        'args'                => array(
          'context' => array(
            'default' => 'view',
          ),
        ),
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array($this, 'update_item'),
        'permission_callback' => array($this, 'update_item_permissions_check'),
        'args'                => $this->get_endpoint_args_for_item_schema(false),
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array($this, 'delete_item'),
        'permission_callback' => array($this, 'delete_item_permissions_check'),
        'args'                => array(
          'force' => array(
            'default' => false,
          ),
        ),
      ),
      'schema' => 'person_schema'
    ));
  }

  /**
   * Create / update the post metadata
   *
   * @param Integer $post_id
   * @param WP_REST_Request $request Full data about the request.
   */
  public function update_metadata($post_id, $request)
  {
    // TODO: Image
    update_post_meta(
      $post_id,
      '_personnel_list_position',
      isset($request['position']) ? $request['position'] : ''
    );

    update_post_meta(
      $post_id,
      '_personnel_list_short_description',
      isset($request['short_description']) ? $request['short_description'] : ''
    );

    update_post_meta(
      $post_id,
      '_personnel_list_github',
      isset($request['github']) ? $request['github'] : ''
    );

    update_post_meta(
      $post_id,
      '_personnel_list_linkedin',
      isset($request['linkedin']) ? $request['linkedin'] : ''
    );

    update_post_meta(
      $post_id,
      '_personnel_list_xing',
      isset($request['xing']) ? $request['xing'] : ''
    );

    update_post_meta(
      $post_id,
      '_personnel_list_facebook',
      isset($request['facebook']) ? $request['facebook'] : ''
    );
  }

  /**
   * Get a collection of items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_items($request)
  {
    $args = array(
      'orderby'          => 'date',
      'order'            => 'DESC',
      'post_type'        => 'person'
    );

    $items = get_posts($args);
    $data = array();
    foreach ($items as $item) {
      $itemdata = $this->prepare_item_for_response($item, $request);
      $data[] = $this->prepare_response_for_collection($itemdata);
    }

    return new WP_REST_Response($data, 200);
  }

  /**
   * Get one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function get_item($request)
  {
    if (!isset($request["id"])) {
      return new WP_Error(401);
    }

    $item = get_post($request["id"]);

    //return a response or error based on some conditional
    if ($item) {
      $data = $this->prepare_item_for_response($item, $request);
      return new WP_REST_Response($data, 200);
    } else {
      return new WP_Error(400, __('message', $this->plugin_name));
    }
  }

  /**
   * Create one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function create_item($request)
  {
    $item = $this->prepare_item_for_database($request);
    $post_id = wp_insert_post($item);

    if ($post_id) {
      $this->update_metadata($post_id, $request);
      $item = get_post($post_id);
      $data = $this->prepare_item_for_response($item, $request);
      return new WP_REST_Response($data, 200);
    }

    return new WP_Error('cant-create', __('message', $this->plugin_name), array('status' => 500));
  }

  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function update_item($request)
  {
    if (!isset($request["id"])) {
      return new WP_Error(401);
    }

    $item = $this->prepare_item_for_database($request);
    $item['ID'] = $request["id"];
    $post_id = wp_insert_post($item);

    if ($post_id) {
      $this->update_metadata($post_id, $request);
      $item = get_post($post_id);
      $data = $this->prepare_item_for_response($item, $request);
      return new WP_REST_Response($data, 200);
    }

    return new WP_Error('cant-update', __('message', $this->plugin_name), array('status' => 500));
  }

  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Request
   */
  public function delete_item($request)
  {
    $item_id = $request["id"];

    if (!isset($item_id)) {
      return new WP_Error(401);
    }

    $item = wp_delete_post($item_id);
    if ($item) {
      return new WP_REST_Response(array("id" => $item_id), 200);
    }

    return new WP_Error('cant-delete', __('message', $this->plugin_name), array('status' => 500));
  }

  /**
   * Check if a given request has access to get items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_items_permissions_check($request)
  {
    // Public
    return true;
  }

  /**
   * Check if a given request has access to get a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function get_item_permissions_check($request)
  {
    return $this->get_items_permissions_check($request);
  }

  /**
   * Check if a given request has access to create items
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function create_item_permissions_check($request)
  {
    return current_user_can('edit_posts');
  }

  /**
   * Check if a given request has access to update a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function update_item_permissions_check($request)
  {
    return $this->create_item_permissions_check($request);
  }

  /**
   * Check if a given request has access to delete a specific item
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|bool
   */
  public function delete_item_permissions_check($request)
  {
    return current_user_can('delete_posts');
  }

  /**
   * Prepare the item for create or update operation
   *
   * @param WP_REST_Request $request Request object
   * @return WP_Error|object $prepared_item
   */
  protected function prepare_item_for_database($request)
  {
    $name = $request->get_param('name');

    return array(
      "post_title" => $name,
      "post_type" => "person",
      "post_status" => "publish"
    );
  }

  /**
   * Prepare the item for the REST response
   *
   * @param mixed $item WordPress representation of the item.
   * @param WP_REST_Request $request Request object.
   * @return mixed
   */
  public function prepare_item_for_response($item, $request)
  {
    // If true, returns only the first value for the specified meta key
    return array(
      "id" => $item->ID,
      "name" => $item->post_title,
      "position" => get_post_meta($item->ID, '_personnel_list_position', true),
      "short_description" => get_post_meta($item->ID, '_personnel_list_short_description', true),
      "github" => get_post_meta($item->ID, '_personnel_list_github', true),
      "linkedin" => get_post_meta($item->ID, '_personnel_list_linkedin', true),
      "xing" => get_post_meta($item->ID, '_personnel_list_xing', true),
      "facebook" => get_post_meta($item->ID, '_personnel_list_facebook', true)
    );
  }

  /**
   * Get the query params for collections
   *
   * @return array
   */
  public function person_schema()
  {
    return array(
      // This tells the spec of JSON Schema we are using which is draft 4.
      '$schema'              => 'http://json-schema.org/draft-04/schema#',
      // The title property marks the identity of the resource.
      'title'                => 'person',
      'type'                 => 'object',
      // In JSON Schema you can specify object properties in the properties attribute.
      'properties'           => array(
        'id' => array(
          'description'  => esc_html__('Unique identifier for the object.'),
          'type'         => 'integer',
          'readonly'     => true,
        ),
        'name' => array(
          'description'  => esc_html__('The user\'s name'),
          'required'     => true,
          'type'         => 'string',
        ),
        'position' => array(
          'description'  => esc_html__('The user\'s position'),
          'required'     => true,
          'type'         => 'string',
        ),
        'short_description' => array(
          'description'  => esc_html__('The user\'s description'),
          'required'     => true,
          'type'         => 'string',
        ),
        'github' => array(
          'description'  => esc_html__('The user\'s github url'),
          'required'     => false,
          'type'         => 'string',
        ),
        'linkedin' => array(
          'description'  => esc_html__('The user\'s linkedin url'),
          'required'     => false,
          'type'         => 'string',
        ),
        'xing' => array(
          'description'  => esc_html__('The user\'s xing url'),
          'required'     => false,
          'type'         => 'string',
        ),
        'facebook' => array(
          'description'  => esc_html__('The user\'s facebook url'),
          'required'     => false,
          'type'         => 'string',
        )
      ),
    );
  }
}
