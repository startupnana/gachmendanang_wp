<?php

/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

	global $wp_query;
	
	if(($wcps_content_source=="recent"))
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					
					) );

		}		
	

	else if(($wcps_content_source=="featured"))
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'meta_query' => array(
						array(
							'key' => '_featured',
							'value' => 'yes',
							)),
					'posts_per_page' => $wcps_total_items,
					
					) );

		}
		
		
	else if(($wcps_content_source=="on_sale"))
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					'meta_query' => array(
							array(
							'key' => '_visibility',
							'value' => array('catalog', 'visible'),
							'compare' => 'IN'
							),
							array(
							'key' => '_sale_price',
							'value' => 0,
							'compare' => '>',
							'type' => 'NUMERIC'
							)
							) ));

		}			
		
		
	else if(($wcps_content_source=="best_selling"))
		{

			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => 'meta_value',
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					'meta_key' => 'total_sales',
					'meta_query' => array(
							array(
							'key' => '_visibility',
							'value' => array('catalog', 'visible'),
							'compare' => 'IN'
							),
							array(
							'key' => 'total_sales',
							'value' => 0,
							'compare' => '>',
							'type' => 'NUMERIC'
							),
							)
					) );

		}			
			

	else if(($wcps_content_source=="top_rated"))
		{
		

			$meta_query = WC()->query->get_meta_query();
			 
			$args = array(
			 'post_type'           => 'product',
			 'post_status'         => 'publish',
			 'ignore_sticky_posts' => 1,
			 'orderby'             => 'title',
			 'order'               => $wcps_query_order,
			 'posts_per_page'      => $wcps_total_items,
			 'meta_query'          => $meta_query
			);
			 
			add_filter('posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses'));
			$wp_query = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));
			remove_filter( 'posts_clauses', array( 'WC_Shortcodes', 'order_by_rating_post_clauses' ) );



		}


	else if(($wcps_content_source=="year"))
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'year' => $wcps_content_year,
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					) );

		}

	else if(($wcps_content_source=="month"))
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'year' => $wcps_content_month_year,
					'monthnum' => $wcps_content_month,
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					
					) );

		}
		
		
	else if(($wcps_content_source=="sku"))
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $wcps_content_sku,
							)),
					'posts_per_page' => $wcps_total_items,
					
					) );

		}		
		

	else if($wcps_content_source=="taxonomy")
		{
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',		
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,				
					'posts_per_page' => $wcps_total_items,
					'tax_query' => array(
						array(
							   'taxonomy' => $wcps_taxonomy,
							   'field' => 'id',
							   'terms' => $wcps_taxonomy_category,
						)
					)
					
					) );
		}



	
	else if(($wcps_content_source=="product_id"))
		{
			
			$wcps_product_ids = explode(',',$wcps_product_ids);
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'post__in' => $wcps_product_ids,
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					
					
					) );
		
		
		}


	else if(($wcps_content_source=="recently_viewed"))
		{
		
		$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
		$viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

		//var_dump($viewed_products);

		 
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					//'no_found_rows' => 1,
					'post__in' => $viewed_products,					
					'posts_per_page' => $wcps_total_items,
					
					
					) );
		
		
		
		
		}
		
	else
		{
		
			$wp_query = new WP_Query(
				array (
					'post_type' => 'product',
					'post_status' => 'publish',
					'orderby' => $wcps_query_orderby,
					'order' => $wcps_query_order,
					'posts_per_page' => $wcps_total_items,
					
					
					) );
		
		
		}