<?php
/**
 * Shortcode Content.
 */
function bm_grid_shortcode_content($atts, $content = ""){
	$atts = shortcode_atts(
	array(
		'id' => false,
	), $atts, 'bm-grid' );
	
	if($atts['id']){
		$gallery_id = $atts['id'];
		
		$__ux_gallery_layout = get_post_meta($gallery_id, '__ux_gallery_layout', true);
		switch($__ux_gallery_layout){
			case 'irregular-list': $content = bm_grid_irregular_list($gallery_id); break;
			case 'custom-grid-portfolio': $content = bm_grid_custom_grid_list($gallery_id); break;
			default:
				$content .= bm_grid_masonry_list($gallery_id);
				$content .= bm_grid_photoswipe_wrap();
			break;
		}
	}
	return $content;
}
add_shortcode('bm-grid', 'bm_grid_shortcode_content');
?>