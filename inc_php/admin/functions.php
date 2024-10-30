<?php
/**
 * Display Post Option.
 */
function bm_grid_display_meta_option($post, $metabox) {
	global $ux_gallery_config, $ux_gallery_fields;
	
	$html = '';
	foreach($ux_gallery_config as $option_id => $option){
		if($option_id == $metabox['id']){
			$section_tabs = false;
			$section_tabs_name = false;
			$section_tabs_val = false;
			foreach($option['section'] as $section_id => $section){
				$section = wp_parse_args($section, array(
					'content-id' => false,
					'default' => false,
					'name' => false
				));
				
				$section_default = false;
				if(is_string($section_id)){
					$section_default = $section['default'] ? $section_id : false;
					$section_active = $section['default'] ? 'active' : '';
					$section_tabs = true;
					if($section['name']){
						$section_tabs_name = $section['name'];
						$section_tabs_val = $section_id;
					}
					
					$get_val = get_post_meta($post->ID, $section_tabs_name, true);
					if($get_val){
						$section_tabs_val = $get_val;
						$section_active = $section_id == $get_val ? 'active' : '';
					}
					
					$html .= '<button type="button" class="__ux_gallery_section_tab ' .sanitize_html_class($section_active). '" data-id="' .esc_attr($section_id). '">' .$section['title']. '</button>';
				}else{
					$section_hidden = $section_default == $section_id ? '' : 'hidden';
					$section_content_class = $section_tabs ? '__ux_gallery_section_content' : '';
					
					if($section_tabs_name){
						$get_val = get_post_meta($post->ID, $section_tabs_name, true);
						if($get_val){
							$section_hidden = $section['content-id'] == $get_val ? '' : 'hidden';
						}
					}
					
					if(!$section['content-id']){
						$section_hidden = '';
					}
					
					$html .= '<table id="' .esc_attr($section['content-id']). '" class="form-table ' .sanitize_html_class($section_hidden). ' ' .sanitize_html_class($section_content_class). '"><tbody>';
					
					foreach($section['items'] as $item){
						$item = wp_parse_args($item, array(
							'title' => '',
							'ctrl' => 'false',
							'desc' => '',
							'name' => '',
							'thstyle' => ''
						));
						
						if($item['type'] == 'divide'){
							$html .= '<tr class="__ux_gallery_item_row_divide"><td colspan="2"><hr /></td></tr>';
						}else{
							$html .= '<tr class="' .esc_attr($item['name']). ' ' .bm_grid_hidden_fields($post, $item). ' __ux_gallery_item_row" data-ctrl="' .esc_attr($item['ctrl']). '">';
							$html .=   '<th scope="row" style="' .esc_attr($item['thstyle']). '"><label for="' .esc_attr($item['name']). '">' .esc_html($item['title']). '</label></th>';
							$html .=   '<td>';
							$html .=     $ux_gallery_fields->fields($post, $item);
							$html .=     '<p class="description" id="' .esc_attr($item['name']). '-description">' .esc_html($item['desc']). '</p>';
							$html .=   '</td>';
							$html .= '</tr>';
						}
					}
					
					$html .= '</tbody></table>';
				}
			}
			$html .= '<input type="hidden" name="' .esc_attr($section_tabs_name). '" value="' .esc_attr($section_tabs_val). '">';
		}
	}
	
	if($post == 'option'){
		return $html;
	}else{
		echo $html;
	}
}

/**
 * Hidden fields.
 */
function bm_grid_hidden_fields($post, $item){
	global $ux_gallery_config;
	
	$current_item = wp_parse_args($item, array(
		'ctrl' => 'false'
	));
	
	$item_hidden = '';
					
	if($current_item['ctrl'] != 'false'){
		$item_ctrl = explode(',', $current_item['ctrl']);
		$item_ctrl_array = explode('|', $item_ctrl[1]);
		
		if($post == 'option'){
			$item_ctrl_meta = get_option($item_ctrl[0]);
		}else{
			$item_ctrl_meta = get_post_meta($post->ID, $item_ctrl[0], true);
		}
		
		if($item_ctrl_meta){
			if(!in_array($item_ctrl_meta, $item_ctrl_array)){
				$item_hidden = 'hidden';
			}
		}else{
			foreach($ux_gallery_config as $option){
				foreach($option['section'] as $section_id => $section){
					if(!is_string($section_id)){
						foreach($section['items'] as $item){
							$item = wp_parse_args($item, array(
								'default' => '',
								'name' => ''
							));
							if($item['name'] == $item_ctrl[0]){
								if(!in_array($item['default'], $item_ctrl_array)){
									$item_hidden = 'hidden';
								}
							}
						}
					}
				}
			}
		}
	}
	
	return sanitize_html_class($item_hidden);
}

/**
 * Display Gallery General Options.
 */
function bm_grid_display_general_options() {
	global $ux_gallery_config;
	
	foreach($ux_gallery_config as $option_id => $option){
		if($option['callback'] == __FUNCTION__){
			$html = '';
			$metabox = array();
			$metabox['id'] = $option_id;
			
			$html .= '<div class="wrap __ux_theme_wrap">';
			$html .=   '<h3>' .esc_html($option['title']). '<button type="button" class="__ux_gallery_save_button" data-saving="' .esc_attr__('saving','bm-grid'). '" data-saved="' .esc_attr__('saved','bm-grid'). '" data-save="' .esc_attr__('save','bm-grid'). '">' .esc_html__('save','bm-grid'). '</button></h3>';
			$html .=   bm_grid_display_meta_option($option['screen'], $metabox);
			$html .= '</div>';
		}
	}
	
	echo $html;
}

/**
 * Display Gallery Modal.
 */
function bm_grid_display_modal($item){
	$html = '';
	$html .= '<div class="modal fade" id="modal-' .esc_attr($item['name']). '">';
	$html .=   '<div class="modal-dialog">';
	$html .=     '<div class="modal-content">';
	$html .=       '<div class="modal-header">';
	$html .=         '<button type="button" class="close" data-event="close"><span aria-hidden="true">&times;</span></button>';
	$html .=         '<h4 class="modal-title">' .esc_html($item['title']). '</h4>';
	$html .=       '</div>';
	$html .=       '<div class="modal-body">';
	$html .=       '</div>';
	$html .=       '<div class="modal-footer">';
	$html .=         '<button type="button" class="btn btn-default" data-event="close">' .esc_attr__('Close','bm-grid'). '</button>';
	$html .=         '<button type="button" class="btn btn-primary">' .esc_attr__('Save','bm-grid'). '</button>';
	$html .=       '</div>';
	$html .=     '</div>';
	$html .=   '</div>';
	$html .= '</div>';
	
	return $html;
}

/**
 * Shortcode Fields.
 */
function bm_grid_shortcode_fields(){
	$fields = array();
	
	$gallery_posts = get_posts(array(
		'posts_per_page' => -1,
		'post_type' => 'bm-grid',
		'meta_key' => '__ux_gallery_shortcode'
	));
	
	if($gallery_posts){
		foreach($gallery_posts as $post){
			array_push($fields, array($post->post_title, $post->ID));
		}
	}else{
		array_push($fields, array(esc_html__('No Gallery','bm-grid'), 0));
	}
	
	return $fields;
}
?>