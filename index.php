<?php 

   /*
      Plugin Name: Share Network Tags Plugin
      Plugin URI: Url del plugin.
      Description:  This is a plugin that help you share tags from all your network (multisite)
      Version: 1.0.
      Author: Pedro Portocarrero
      Author URI: www.pedritokun.com
   */

  global $db_version;
  $db_version = '1.0';

  function jquery_ui_enqueue_scripts() {
    wp_enqueue_style( 'location_search_css', plugins_url( 'share_network_tags_plugin/css/main.css' ), array(), '1.11,4', 'all' );
    wp_enqueue_script( 'jquery_ui_js', plugins_url( 'share_network_tags_plugin/js/jquery-ui.min.js' ), array(), '1.11,4', true );
    wp_enqueue_script( 'location_search_js', plugins_url( 'share_network_tags_plugin/js/main.js' ), array(), '1.0.0', true ); 
  }

  add_action( 'admin_enqueue_scripts', 'jquery_ui_enqueue_scripts' );

  function share_network_plugin_install(){
    global $db_version;
    add_option( 'db_version', $db_version );
  }

  register_activation_hook( __FILE__,'share_network_plugin_install' );

  function search_network_tags_metabox() {
    add_meta_box('search_network_tags_metabox', 'Tags Globales', 'search_network_tags_callback');
  }

  add_action( 'add_meta_boxes','search_network_tags_metabox' );

  function search_network_tags_callback() {
  ?>

  <div>
    <input type="text" placeholder="Nombre del tag" id="autocomplete" class="search_network_tag_name">
  </div>
            
  <?php
  }

  function getTags() {
    global $wpdb;

    $table_name            = $wpdb->base_prefix . 'blogs';
    $blog_ids              = $wpdb->get_col( "SELECT blog_id FROM $table_name" );
    $t                     = $_GET['term'];
    $data                  = array();
    $global_tags_container = array();
    $i                     = 0;

    foreach ( $blog_ids as $id ) {
      switch_to_blog($id);

      //$taxonomy_array = array('post_tag','special_edition_tag','topic_tags');
      $taxonomy_array = array('post_tag');

      $args = array(
            'hide_empty' => 0,
            'name__like' => $t
      );

      $global_tags           = get_terms($taxonomy_array,$args);
      $global_tags_container = array_merge($global_tags_container,$global_tags);
      restore_current_blog();
    }

    foreach ( $global_tags_container as $value ) {
      $data[$i]['id']    = $value->term_id;
      $data[$i]['label'] = $value->name;
      $data[$i]['value'] = $value->name;
      $i++;
    }

    $data = array_unique(array_column($data,'value'));

    echo json_encode($data);

    die();
  }




  add_action( 'wp_ajax_getTags', 'getTags' );
  add_action( 'wp_ajax_nopriv_getTags', 'getTags' );

  function generate_js(){
    echo '<script> var plugin_location_search_dir = "'.plugins_url( 'share_network_tags_plugin/' ).'"; </script>';
  }

  add_action( 'wp_footer', 'generate_js');

  function share_network_plugin_uninstall(){
      $option_name = 'plugin_option_name';
      delete_option( $option_name );
      delete_site_option( $option_name ); 
  }

  register_deactivation_hook( __FILE__,'share_network_plugin_uninstall' );

?>