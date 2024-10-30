<?php
/**
 *    Plugin Name: BM Grid
 *    Plugin URI: http://themes.uiueux.com/bm-grid-wordpress-plugin/
 *    Description: BM Grid - Unlimited grid layout with Drag&Drop custom portfolio builder.
 *    Version: 1.0.0
 *    Author: uiueux
 *    Author URI: http://uiueux.com
 *
 *    Text Domain: bm-grid
 *    Domain Path: /languages/
 *
 **/
$uxGalleryVersion = "1.0.0";

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'BM_GRID_URL', plugins_url( "/", __FILE__ ) );

/**
 * Main BM_Grid Class.
 *
 * @class BM_Grid
 * @version	1.0.0
 */
if ( ! class_exists( 'BM_Grid' ) ) {

	class BM_Grid {
		
		function __construct() {
			add_action( 'init', array( &$this, 'init'), 1000 );
			add_action( 'init', array( &$this, 'interface_img_size'), 1001 );
			add_action( 'admin_init', array( &$this, 'admin_enqueue'), 1002 );
		}
		
		/**
		 * Init
		 */
		function init() {
			add_action( 'wp_head', array( &$this, 'theme_ajaxurl'), 1);
			add_action( 'wp_enqueue_scripts', array( &$this, 'interface_enqueue') );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ), 9999 );
			add_action( 'save_post', array( &$this, 'meta_save' ) );
			add_action( 'load-post.php', array( &$this, 'meta_boxes' ) );
            add_action( 'load-post-new.php', array( &$this, 'meta_boxes' ) );
			add_action( 'load-post.php', array( &$this, 'admin_min_enqueue' ) );
            add_action( 'load-post-new.php', array( &$this, 'admin_min_enqueue' ) );
			
			require_once dirname(__FILE__) . '/inc_php/interface/functions.php';
			require_once dirname(__FILE__) . '/inc_php/interface/shortcode.php';
			
			//template
			require_once dirname(__FILE__) . '/inc_php/interface/template/custom-grid-portfolio.php';
			require_once dirname(__FILE__) . '/inc_php/interface/template/masonry-list.php';
			require_once dirname(__FILE__) . '/inc_php/interface/template/irregular-list.php';
			
			require_once dirname(__FILE__) . '/inc_php/admin/functions.php';
			require_once dirname(__FILE__) . '/inc_php/admin/config.php';
			require_once dirname(__FILE__) . '/inc_php/admin/fields.php';
			require_once dirname(__FILE__) . '/inc_php/admin/ajax.php';
			
			$GLOBALS['ux_gallery_config'] = bm_grid_option_config();
			$GLOBALS['ux_gallery_fields'] = new BM_Grid_Fields();
			
			$this->register();
		}
		
		/**
		 * Interface enqueue
		 */
		function interface_enqueue() {
			//style
			wp_enqueue_style('photoswipe', plugins_url("css/photoswipe.css", __FILE__), array(), '4.1.1');
			wp_enqueue_style('photoswipe-default-skin', plugins_url("css/skin/photoswipe/default/default-skin.css", __FILE__), array(), '4.1.1');
			wp_enqueue_style('ux-gallery-gridstack-style', plugins_url("css/gridstack.min.css", __FILE__), array(), '0.3.0');
			wp_enqueue_style('ux-gallery-interface-style', plugins_url("css/interface-style.css", __FILE__), array(), '0.0.1');
			
			wp_enqueue_script('imagesloaded');
			//script
			wp_enqueue_script('jquery.waypoints.min', plugins_url("js/jquery.waypoints.min.js", __FILE__), array('jquery'), '4.0.1', true);
			wp_enqueue_script('isotope.pkgd.min', plugins_url("js/isotope.pkgd.min.js", __FILE__), array('jquery'), '3.0.1', true);
			wp_enqueue_script('photoswipe.min', plugins_url("js/photoswipe.min.js", __FILE__), array('jquery'), '4.1.1', true);
			wp_enqueue_script('photoswipe-ui-default.min', plugins_url("js/photoswipe-ui-default.min.js", __FILE__), array('jquery'), '4.1.1', true);
			wp_enqueue_script('ux-gallery-interface-min', plugins_url("js/interface-min.js", __FILE__), array('jquery'), '0.0.1', true);
			wp_enqueue_script('ux-gallery-interface-main', plugins_url("js/interface-main.js", __FILE__), array('jquery'), '0.0.1', true);
		}

		//theme post type support
		function interface_img_size(){  
			add_image_size('bm-standard-thumb', 650, 9999);
			add_image_size('bm-standard-thumb-medium', 1000, 9999);
			add_image_size('bm-standard-thumb-big', 2000, 9999); 
		}
		
		
		/**
		 * Admin enqueue
		 */
		function admin_enqueue() {
			//style
			wp_enqueue_style('ux-gallery-admin-style', plugins_url("css/admin-style.css", __FILE__), array(), '0.0.1');
			
			//script
			wp_enqueue_script('ux-gallery-admin-main', plugins_url("js/admin-main.js", __FILE__), array('jquery'), '0.0.1', true);
		}
		
		/**
		 * Admin min enqueue
		 */
		function admin_min_enqueue() {
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-resizable');
			wp_enqueue_script('ux-gallery-admin-min', plugins_url("js/admin-min.js", __FILE__), array('jquery', 'jquery-ui-droppable', 'jquery-ui-draggable', 'jquery-ui-resizable'), '0.0.1', true);
		}
		
		/**
		 * Admin Menu
		 */
		function admin_menu() {
			add_submenu_page( 'edit.php?post_type=bm-grid', __('General Options','bm-grid'), __('General Options','bm-grid'), 'manage_options', 'ux-gallery-general-options', 'bm_grid_display_general_options');
		}
		
		/**
		 * Theme Ajaxurl
		 */
		function theme_ajaxurl() { ?>
			<script type="text/javascript">
				var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			</script>
		<?php
		}
		
		/**
		 * Register
		 */
		function register() {
			$labels = array(
				'name' => __('BM Grid','bm-grid'),
				'singular_name' => __('BM Grid','bm-grid'),
				'add_new' => __('Add New','bm-grid'),
				'add_new_item' => __('Add New Grid','bm-grid'),
				'edit_item' => __('Edit Grid','bm-grid'),
				'new_item' => __('New Grid','bm-grid'),
				'all_items' => __('All Grids','bm-grid'),
				'view_item' => __('View Grid','bm-grid'),
				'search_items' => __('Search Grids','bm-grid'),
				'not_found' => __('No Grid found.','bm-grid'),
				'not_found_in_trash' => __('No Grid found in Trash.','bm-grid'), 
				'parent_item_colon' => '',
				'menu_name' => __('BM Grid','bm-grid')
			);
			
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true, 
				'show_in_menu' => true, 
				'query_var' => true,
				'rewrite' => array( 'slug' => 'bm-grid' ),
				'capability_type' => 'post',
				'has_archive' => true, 
				'hierarchical' => true,
				'menu_position' => 72,
				'menu_icon' => BM_GRID_URL. 'images/gallery.png',
				'supports' => array('title')
			); 
			
			register_post_type('bm-grid', $args);
			
		}
		
		/**
		 * Register meta box(es).
		 */
		function meta_boxes() {
			global $ux_gallery_config;
			
			foreach($ux_gallery_config as $option_id => $option){
				if($option['screen'] != 'option'){
					add_meta_box($option_id, $option['title'], $option['callback'], $option['screen'], $option['context'], $option['priority']);
					add_filter( 'postbox_classes_' .$option['screen']. '_' .$option_id, function(){
						return array('__ux_gallery_meta_box');
					});
				}
			}
		}
		
		/**
		 * Save post meta.
		 */
		function meta_save($post_id) {
			global $ux_gallery_config;
			
			// dont run if the post array is no set
			if ( empty( $_POST ) || empty( $_POST['post_ID'] ) ) 
				return;
			
			// don't run the saving if this is an auto save
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return;
			
			// don't run the saving if the function is called for saving revision.
			if ( $post_object->post_type == 'revision' )
			 	return;
			
			$post_meta = array();
			if($ux_gallery_config){
				foreach($ux_gallery_config as $option){
					if($option['screen'] != 'option'){
						foreach($option['section'] as $section){
							$section = wp_parse_args($section, array(
								'name' => false

							));
							
							if($section['name']){
								$post_meta[] = $section['name'];
							}
							if($section['items']) {
								foreach($section['items'] as $item){
									$item = wp_parse_args($item, array(
										'name' => false
									));
									
									if($item['name']){
										$post_meta[] = $item['name'];
									}
								}
							}
						}
						
						$post_meta[] = '__ux_gallery_masonry_portfolio_category_orderby_order';
						$post_meta[] = '__ux_gallery_standard_grid_category_orderby_order';
						$post_meta[] = '__ux_gallery_irregular_list_category_orderby_order';
						
						if(count($post_meta)){
							foreach($post_meta as $meta_key){
								$old = get_post_meta($post_id, $meta_key, true);  
								$new = @$_POST[$meta_key];
								
								update_post_meta($post_id, $meta_key, $new); 
							}
						}
					}
				}
			}
		}
	}
}

function bm_grid_init(){
	// Global for backwards compatibility.
    $GLOBALS['bm_grid'] = new BM_Grid();
}
add_action('init', 'bm_grid_init', 999);



?>