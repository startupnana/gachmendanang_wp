<?php
/*
Plugin Name: Custom Content Shortcode
Plugin URI: http://wordpress.org/plugins/custom-content-shortcode/
Description: Display posts, pages, custom post types, custom fields, files, images, comments, attachments, menus, or widget areas
Version: 3.1.6
Shortcodes: loop, content, field, taxonomy, if, for, each, comments, user, url, load
Author: Eliot Akira
Author URI: eliotakira.com
License: GPL2
*/

define('CCS_PATH', dirname(__FILE__));
define('CCS_URL', untrailingslashit(plugins_url('/',__FILE__)));
define('CCS_PLUGIN_BASENAME', plugin_basename(__FILE__));

new CCS_Plugin;

class CCS_Plugin {

  public static $settings;
  public static $settings_name;
  public static $settings_definitions;
  public static $state;

  function __construct() {

    $this->load_settings();
    $this->load_main_modules();
    $this->load_optional_modules();
    self::$state['doing_ccs_filter'] = false;
    self::$state['original_post_id'] = 0;
    add_action('init',array($this,'init'));
  }

  function init() {
    $this->setup_wp_filters();
  }


  /*---------------------------------------------
   *
   * Load settings
   *
   */

  function load_settings() {

    self::$settings_name = 'ccs_content_settings';
    self::$settings = get_option( self::$settings_name );

    self::$settings_definitions = array(

      'load_acf_module' => array(
        'module' => 'acf',
        'default' => 'on',
        'tab' => 'acf',
        'text' => '<b>ACF</b> shortcodes',
      ),
      'load_bootstrap_module' => array(
        'module' => 'bootstrap',
        'default' => 'off',
        'tab' => 'bootstrap',
        'text' => '<b>Bootstrap</b> shortcodes',
      ),
      'load_file_loader' => array(
        'module' => 'load',
        'default' => 'on',
        'tab' => 'load',
        'text' => '<b>File Loader</b> module',
      ),
      'load_gallery_field' => array(
        'default' => 'on',
        'module' => 'gallery',
        'tab' => 'gallery',
        'text' => '<b>Gallery Field</b> module',
      ),
      'load_mobile_detect' => array(
        'default' => 'off',
        'module' => 'mobile',
        'tab' => 'mobile',
        'text' => '<b>Mobile Detect</b> module',
      ),
      'raw_shortcode' => array(
        'default' => 'off',
        'module' => 'raw',
        'tab' => 'raw',
        'text' => '<b>[raw]</b> shortcode',
      ),
      'block_shortcode' => array(
        'default' => 'off',
        'module' => 'block',
        'tab' => 'block',
        'text' => '<b>HTML block</b> shortcodes',
      ),
      'shortcodes_in_widget' => array(
        'default' => 'on',
        'module' => '',
        'tab' => '',
        'text' => 'Enable shortcodes in Text widget',
      ),
      'shortcodes_in_excerpt' => array(
        'default' => 'off',
        'module' => '',
        'tab' => '',
        'text' => 'Enable shortcodes in <code>the_excerpt()</code>',
      ),
    );

    if ( self::$settings === false ) {

      self::$settings = array();

      foreach (self::$settings_definitions as $option_name => $def) {
        self::$settings[$option_name] = $def['default'];
      }

      update_option( self::$settings_name, self::$settings );
    }

    if ( isset(self::$settings['disable_shortcodes']) ) {
      self::$state['disabled_shortcodes'] =
        array_map( 'trim', explode(',', self::$settings['disable_shortcodes']) );
    } else {
      self::$state['disabled_shortcodes'] = array();
    }
  }


  /*---------------------------------------------
   *
   * Load main and optional modules
   *
   */

  function load_module( $module ) {

    include_once ( CCS_PATH.'/includes/'.$module.'.php' );
  }

  function load_main_modules() {

    $modules = array(
      'core/local-shortcodes', // Local shortcodes
      'core/content',       // Content shortcode
      'core/loop',          // Loop shortcode
      'docs/docs',          // Documentation under Settings -> Custom Content
      'modules/attached',   // Attachment loop
      'modules/cache',      // Cache shortcode
      'modules/comments',   // Comments shortcode
      'modules/foreach',    // For/each loop
      'modules/format',     // Format shortcodes: br, p, x, clean, direct, format
      'modules/if',         // If shortcode
      'modules/menu',       // Loop menu
      'modules/paging',     // Pagination shortcode
      'modules/pass',       // Pass shortcode
      'modules/related',    // Related posts loop
      'modules/url',        // URL shortcode
      'modules/user',       // User shortcodes
      'optional/wck',       // WCK support

      // 'optional/widget'       // Widget shortcode (not ready)

    );

    foreach ($modules as $module) {
      $this->load_module( $module );
    }
  }

  /*---------------------------------------------
   *
   * Optional modules
   *
   */

  function load_optional_modules() {

    foreach (self::$settings_definitions as $option_name => $def) {

      if ( !empty($def['module']) &&
        isset(self::$settings[ $option_name ]) &&
        self::$settings[ $option_name ]=='on' ) {

        $this->load_module( 'optional/'.$def['module'] );
      }
    }
  }


  /*---------------------------------------------
   *
   * Set up WP filters
   *
   */

  function setup_wp_filters() {

    $settings = self::$settings;


    // Render plugin shortcodes after wpautop but before do_shortcode
    add_filter( 'the_content', array($this, 'ccs_content_filter'), 11 );
    remove_filter( 'the_content', 'do_shortcode', 11 );
    add_filter( 'the_content', 'do_shortcode', 12 );
    add_action( 'doing_it_wrong_run', array($this, 'disable_nonexistent_shortcode_error'), 99 );


    /*---------------------------------------------
     *
     * Enable shortcodes in Text widget
     *
     */

    if ( isset( $settings['shortcodes_in_widget'] ) &&
      $settings['shortcodes_in_widget'] == "on" ) {

      // Before 10, in case of other theme or plugin
      add_filter('widget_text', array($this, 'ccs_content_filter'), 9 );
      add_filter('widget_text', 'do_shortcode', 11 );
    }


    /*---------------------------------------------
     *
     * Enable shortcodes in the_excerpt()
     *
     */

    if ( isset( $settings['shortcodes_in_excerpt'] ) &&
      $settings['shortcodes_in_excerpt'] == "on" ) {

      remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
      add_filter( 'get_the_excerpt', array($this, 'do_shortcode_before_excerpt') );
    }

    // Exempt [loop] from wptexturize()
    add_filter( 'no_texturize_shortcodes',
      array( $this, 'shortcodes_to_exempt_from_wptexturize') );

  }


  function do_shortcode_before_excerpt($text = '') {
    $raw_excerpt = $text;
    if ( empty($text) ) {
      $text = wp_trim_words(
        apply_filters('the_content', get_the_content()),
        apply_filters('excerpt_length', 55),
        apply_filters('excerpt_more', ' ' . '[...]')
      );
    }
    return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
  }

  function disable_nonexistent_shortcode_error( $function ) {
    if ($function == 'do_shortcode_tag') {
      add_filter('doing_it_wrong_trigger_error', array($this, 'return_false'), 99 );
    } else {
      remove_filter('doing_it_wrong_trigger_error', array($this, 'return_false'), 99 );
    }
  }

  function return_false() { return false; }

  function shortcodes_to_exempt_from_wptexturize($shortcodes){
    $shortcodes[] = 'loop';
    return $shortcodes;
  }


  static function ccs_content_filter( $content ) {

    $content = do_ccs_shortcode( $content, false );

    // This gets passed to do_shortcode
    return $content;
  }

  static function add( $tag, $func = null, $global = true ) {
    add_ccs_shortcode( $tag, $func, $global );
  }

} // End CCS_Plugin


/*---------------------------------------------
 *
 * Global helper functions
 *
 */

if (!function_exists('do_short')) {
  function do_short($content) {
    echo do_ccs_shortcode( $content );
  }
}

if (!function_exists('return_short')) {
  function return_short($content) {
    return do_ccs_shortcode( $content );
  }
}

if (!function_exists('start_short')) {
  function start_short() {
    ob_start();
  }
}

if (!function_exists('end_short')) {
  function end_short() {
    do_short( ob_get_clean() );
  }
}

function add_ccs_shortcode( $tag, $func = null, $global = true ) {

  if (is_array($tag)) {
    if ($func === false) $global = false;
    foreach ($tag as $this_tag => $this_func) {
      if ( ! in_array($this_tag, CCS_Plugin::$state['disabled_shortcodes']) )
        add_local_shortcode( 'ccs', $this_tag, $this_func, $global );
    }
  } else {
    if ( ! in_array($tag, CCS_Plugin::$state['disabled_shortcodes']) )
      add_local_shortcode( 'ccs', $tag, $func, $global );
  }
}

function do_ccs_shortcode( $content, $global = true ) {

  $prev = CCS_Plugin::$state['doing_ccs_filter'];
  CCS_Plugin::$state['doing_ccs_filter'] = true;
  //$content = CCS_Format::protect_script($content, $global);
  $content = do_local_shortcode( 'ccs', $content, false );

  CCS_Plugin::$state['doing_ccs_filter'] = $prev; // Restore

  if ( $global ) {
    $content = do_shortcode( $content );
  }

  return $content;
}