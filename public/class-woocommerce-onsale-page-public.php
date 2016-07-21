<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpgenie.org
 * @since      1.0.0
 *
 * @package    Woocommerce_onsale_page
 * @subpackage Woocommerce_onsale_page/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_onsale_page
 * @subpackage Woocommerce_onsale_page/public
 * @author     Your Name <email@example.com>
 */
class Woocommerce_onsale_page_Public {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	

	public function template_loader( $template ) {		
		
		$find = array( 'woocommerce.php' );
		$file = '';
		
		if ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'onsale' ) ) ) {

			$file 	= 'archive-product.php';
			$find[] = $file;
			$find[] = WC()->template_path() . $file;

		}

		if ( !empty($file) ) {
			
			$template = locate_template( array_unique( $find ) );
			
			if ( ! $template || WC_TEMPLATE_DEBUG_MODE ) {
				$template = WC()->plugin_path() . '/templates/' . $file;
			}
		}

		return $template;
	}


	public function pre_get_posts( $q ) {
		
		if(!$q->query)
			return;
		
		if(wc_get_page_id( 'onsale' ) != -1 ){
			if( is_page( wc_get_page_id( 'onsale' ) ) ){
				
				$q->set( 'post_type', 'product' );
				$q->set( 'page', '' );
				$q->set( 'pagename', '' );			

				// Fix conditional Functions
				$q->is_archive           = true;
				$q->is_post_type_archive = true;
				$q->is_singular          = false;
				$q->is_page              = false;
				$q->is_sale_page         = true;
				$q->is_paged             = true; // hack for displaying when Shop Page Display is set to show categories
				}
		}	
	}

	public function mod_woocommerce_product_query($q, $WC_Query){
		
		global $wp_query;

		if($wp_query->is_sale_page) {

			$product_ids_on_sale = wc_get_product_ids_on_sale();
			$meta_query = WC()->query->get_meta_query();
			$q->set( 'post__in', array_merge( array( 0 ), $product_ids_on_sale ) );
		}
	}

	public function woocommerce_page_title($title) {

		global $wp_query;
		
		$onsale_page_id = wc_get_page_id( 'onsale' );


		if($wp_query->is_sale_page) {
			
			$page_title   = get_the_title( $onsale_page_id );

			return $page_title;

		}

		return $title;

	}

	public function woocommerce_get_breadcrumb($crumbs, $WC_Breadcrumb){
		
		global $wp_query;
		
		if($wp_query->is_sale_page) {
			
			$onsale_page_id = wc_get_page_id( 'onsale' );						
			$crumbs[1] = array(get_the_title( $onsale_page_id ), get_permalink( $onsale_page_id )	);
		}
		
		return $crumbs;

	}	

}