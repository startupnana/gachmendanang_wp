<?php

/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 	

class class_wcps_functions  {
	
	
    public function __construct(){

		}
		
		
		
	public function skins(){
		
		$skins = array(
		
						'flat'=> array(
										'slug'=>'flat',									
										'name'=>'Flat',
										'thumb_url'=>'',
										),		

						'zoomin'=>array(
										'slug'=>'zoomin',
										'name'=>'ZoomIn',
										'thumb_url'=>'',
										),							

						'spinleft'=>array(
										'slug'=>'spinleft',
										'name'=>'SpinLeft',
										'thumb_url'=>'',
										),

						'contentbottom'=>array(
										'slug'=>'contentbottom',
										'name'=>'ContentBottom',
										'thumb_url'=>'',
										),
																								
					
						
						);
		
		$skins = apply_filters('post_grid_filter_skins', $skins);	
		
		return $skins;
		
		}
		

		
	public function wcps_grid_items($grid_items = array()){
		
			$grid_items = array(
							'thumb'=>__('Thumbnail','wcps'),
							'title'=>__('Title','wcps'),
							'excerpt'=>__('Excerpt','wcps'),
							'category'=>__('Category','wcps'),
							'price'=>__('Price','wcps'),
							'rating'=>__('Rating','wcps'),
							'cart'=>__('Cart','wcps'),
							'sale'=>__('Sale','wcps'),
							'featured'=>'Featured',
							);
			return $grid_items;
		}		
		
		
		
		
		
		
		
	
}