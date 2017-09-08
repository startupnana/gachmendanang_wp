<?php
/*-----------------------------------------------------------------------------------*/
/* Include Theme Functions */
/*-----------------------------------------------------------------------------------*/

function virtue_lang_setup() {
	load_theme_textdomain('virtue', get_template_directory() . '/languages');
}

add_action( 'after_setup_theme', 'virtue_lang_setup' );
require_once locate_template('/themeoptions/framework.php');        // Options framework
require_once locate_template('/themeoptions/options.php');     		// Options framework
require_once locate_template('/themeoptions/options/virtue_extension.php'); // Options framework extension
require_once locate_template('/lib/utils.php');           			// Utility functions
require_once locate_template('/lib/init.php');            			// Initial theme setup and constants
require_once locate_template('/lib/sidebar.php');         			// Sidebar class
require_once locate_template('/lib/config.php');          			// Configuration
require_once locate_template('/lib/cleanup.php');        			// Cleanup
require_once locate_template('/lib/nav.php');            			// Custom nav modifications
require_once locate_template('/lib/metaboxes.php');     			// Custom metaboxes
require_once locate_template('/lib/comments.php');        			// Custom comments modifications
require_once locate_template('/lib/widgets.php');         			// Sidebars and widgets
require_once locate_template('/lib/aq_resizer.php');      			// Resize on the fly
require_once locate_template('/lib/scripts.php');        			// Scripts and stylesheets
require_once locate_template('/lib/custom.php');          			// Custom functions
require_once locate_template('/lib/admin_scripts.php');          	// Icon functions
require_once locate_template('/lib/authorbox.php');         		// Author box
require_once locate_template('/lib/custom-woocommerce.php'); 		// Woocommerce functions
require_once locate_template('/lib/virtuetoolkit-activate.php'); 	// Plugin Activation
require_once locate_template('/lib/custom-css.php'); 			    // Fontend Custom CSS

/**
* Code phan trang
*/
if ( ! is_admin() ) {
// ---------------------- FRONTPAGE -------------------
if ( defined('WC_VERSION') ) {
// ---------------------- WooCommerce active -------------------
  
   	/**
	* Set Pagination for shortcodes custom loop on single-pages.
  	* @uses $woocommerce_loop;
  	*/
  	add_action( 'pre_get_posts', 'kli_wc_pre_get_posts_query' ); 
  	function kli_wc_pre_get_posts_query( $query ) {
  		global $woocommerce_loop;
  	
	  	// Get paged from main query only
	  	// ! frontpage missing the post_type
	  	if ( is_main_query() && ( $query->query['post_type'] == 'product' ) || ! isset( $query->query['post_type'] ) ){
	  
		  if ( isset($query->query['paged']) ){
  			$woocommerce_loop['paged'] = $query->query['paged'];
		  }
	  	}
	  
  		if ( ! $query->is_post_type_archive || $query->query['post_type'] !== 'product' ){
		  	return;
	  	}
  		
  		$query->is_paged = true;
  		$query->query['paged'] = $woocommerce_loop['paged'];
  		$query->query_vars['paged'] = $woocommerce_loop['paged'];
  	}
  
	/** Prepare Pagination data for shortcodes on pages
  	* @uses $woocommerce_loop;
	**/
	add_action( 'loop_end', 'kli_query_loop_end' ); 
	function kli_query_loop_end( $query ) {
		
		if ( ! $query->is_post_type_archive || $query->query['post_type'] !== 'product' ){
			return;
		}
		
		// Cache data for pagination
		global $woocommerce_loop;
		$woocommerce_loop['pagination']['paged'] = $woocommerce_loop['paged'];
		$woocommerce_loop['pagination']['found_posts'] = $query->found_posts;
		$woocommerce_loop['pagination']['max_num_pages'] = $query->max_num_pages;
		$woocommerce_loop['pagination']['post_count'] = $query->post_count;
		$woocommerce_loop['pagination']['current_post'] = $query->current_post;
	}
	/**
	* Pagination for shortcodes on single-pages 
	* @uses $woocommerce_loop;
	*/
	add_action( 'woocommerce_after_template_part', 'kli_wc_shortcode_pagination' ); 
	function kli_wc_shortcode_pagination( $template_name ) {
		if ( ! ( $template_name === 'loop/loop-end.php' && is_page() ) ){
			return;
		}
		global $wp_query, $woocommerce_loop;
		if ( ! isset( $woocommerce_loop['pagination'] ) ){
			return;
		}
		$wp_query->query_vars['paged'] = $woocommerce_loop['pagination']['paged'];
		$wp_query->query['paged'] = $woocommerce_loop['pagination']['paged'];
		$wp_query->max_num_pages = $woocommerce_loop['pagination']['max_num_pages'];
		$wp_query->found_posts = $woocommerce_loop['pagination']['found_posts'];
		$wp_query->post_count = $woocommerce_loop['pagination']['post_count'];
		$wp_query->current_post = $woocommerce_loop['pagination']['current_post'];
 
		// Custom pagination function or default woocommerce_pagination()
		kli_woocommerce_pagination();
	}	
	/**
	* Custom pagination for WooCommerce instead the default woocommerce_pagination()
	* @uses plugin Prime Strategy Page Navi, but added is_singular() on #line16
	**/
	remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
	add_action( 'woocommerce_after_shop_loop', 'kli_woocommerce_pagination', 10);
	function kli_woocommerce_pagination() {
		woocommerce_pagination(); 
	}
}/*woocommerce*/
}/*frontpage*/

add_filter('widget_text', 'do_shortcode');

add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );
  
function woo_custom_cart_button_text() {
  
        return __( 'Đặt hàng', 'woocommerce' );
  
}

add_filter('add_to_cart_redirect', 'custom_add_to_cart_redirect');
function custom_add_to_cart_redirect() {
     /**
      * Replace with the url of your choosing
      * e.g. return 'http://www.yourshop.com/'
      */
     return get_permalink( get_option('woocommerce_cart_page_id') );
}

/*clear cart*/
add_action('init', 'woocommerce_clear_cart_url');
function woocommerce_clear_cart_url() {
	global $woocommerce;
	if( isset($_REQUEST['clear-cart']) ) {
		$woocommerce->cart->empty_cart();
	}
}
/*remove action creat account form*/
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
     unset($fields['billing']['billing_postcode']);
     unset($fields['account']['account_username']);
     unset($fields['account']['account_password']);
     unset($fields['account']['account_password-2']);
     return $fields;
}
/*----------------------------------*/
add_filter('woocommerce_sale_flash', 'dvd_woocommerce_sale_flash', 10, 2);
function dvd_woocommerce_sale_flash($post, $product){
    global $product;
    $sale_price = $product->get_sale_price();
    $regular_price = $product->get_regular_price();
    $tmp = ($sale_price * 100)/$regular_price;
    return '<span class="onsale">- '.number_format(100-$tmp).'%</span>';
}
/*----------------------------------*/
