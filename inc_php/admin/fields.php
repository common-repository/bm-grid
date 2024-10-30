<?php
/**
 * Main BM_Grid_Fields Class.
 *
 * @class BM_Grid_Fields
 * @version	1.0.0
 */
if ( ! class_exists( 'BM_Grid_Fields' ) ) {

	class BM_Grid_Fields {
		
		function fields($post, $item){
			$html = '';
			
			$item = wp_parse_args($item, array(
				'default' => '',
				'style' => '',
				'fields' => false,
				'taxonomy' => 'category',
				'text' => false,
				'nosave' => false,
				'id' => $item['name']
			));
			
			if($post == 'option'){
				$get_val = get_option($item['name'], $item['default']);
			}else{
				$get_val = get_post_meta($post->ID, $item['name'], true);
			}
			
			$item_val = $item['default'];
			if($get_val){
				$item_val = $get_val;
			}
			
			if($item['nosave']){
				$item['name'] = '';
			}
			
			switch($item['type']){
				case 'text':
					$html .= '<input name="' .esc_attr($item['name']). '" type="text" id="' .esc_attr($item['id']). '" value="' .esc_attr($item_val). '" class="regular-text" style="' .esc_attr($item['style']). '">';
				break;
				
				case 'textarea':
                    $html .= '<textarea name="' .esc_attr($item['name']). '" class="regular-text" rows="3" style="' .esc_attr($item['style']). '">' .esc_textarea($item_val). '</textarea>';
                break;
				
				case 'select':
					$html .= '<select name="' .esc_attr($item['name']). '" id="' .esc_attr($item['id']). '" style="' .esc_attr($item['style']). '" class="__ux-gallery-select">';
					if($item['fields']){
						foreach($item['fields'] as $field){
							$html .= '<option value="' .esc_attr($field[1]). '" ' .selected($item_val, $field[1], false).'>' .esc_html($field[0]). '</option>';
						}
					}else{
						$html .= '<option selected="selected" value="0">' .esc_html__('No options','bm-grid'). '</option>';
					}
					$html .= '</select>';
				break;
				
				case 'switch':
					$switch_text_on = esc_attr__('On','bm-grid');
					$switch_text_off = esc_attr__('Off','bm-grid');
					$switch_text = $item_val == 'on' ? $switch_text_on : $switch_text_off;
					if($item['text']){
						$switch_text = $item['text'];
					}
					
					$html .= '<button type="button" class="__ux-gallery-switch ' .esc_attr($item_val). '" data-text-on="' .esc_attr($switch_text_on). '" data-text-off="' .esc_attr($switch_text_off). '">';
					$html .=   esc_html($switch_text);
					$html .= '</button>';
					$html .= '<input type="hidden" name="' .esc_attr($item['name']). '" id="' .esc_attr($item['id']). '" value="' .esc_attr($item_val). '">';
				break;
				
				case 'single-category':
					$terms = get_terms(array(
						'hide_empty' => false,
                        'taxonomy' => $item['taxonomy']
                    ));
					
					$html .= '<select name="' .esc_attr($item['name']). '" id="' .esc_attr($item['id']). '" style="' .esc_attr($item['style']). '" class="__ux-gallery-select">';
					if($terms){
						$html .= '<option value="0">' .esc_html__('Select a Category','bm-grid'). '</option>';
						foreach($terms as $term){
							$html .= '<option value="' .esc_attr($term->term_id). '" ' .selected($item_val, $term->term_id, false).'>' .esc_html($term->name). '</option>';
						}
					}else{
						$html .= '<option selected="selected" value="0">' .esc_html__('No Categories','bm-grid'). '</option>';
					}
					$html .= '</select>';
				break;
				
				case 'category-orderby':
					$item['fields'] = array(
						array(esc_html__('Please Select','bm-grid'), 'none'),
						array(esc_html__('Title','bm-grid'),         'title'),
						array(esc_html__('Date','bm-grid'),          'date'),
						array(esc_html__('ID','bm-grid'),            'id'),
						array(esc_html__('Modified','bm-grid'),      'modified'),
						array(esc_html__('Author','bm-grid'),        'author'),
						array(esc_html__('Comment count','bm-grid'), 'comment_count')
					);
				
					$html .= '<select name="' .esc_attr($item['name']). '" id="' .esc_attr($item['id']). '" style="' .esc_attr($item['style']). '">';
					if($item['fields']){
						foreach($item['fields'] as $field){
							$html .= '<option value="' .esc_attr($field[1]). '" ' .selected($item_val, $field[1], false).'>' .esc_html($field[0]). '</option>';
						}
					}else{
						$html .= '<option selected="selected" value="0">' .esc_html__('No options','bm-grid'). '</option>';
					}
					$html .= '</select>';
					
					$item['name'] = $item['name']. '_order';
					$get_val = get_post_meta($post->ID, $item['name'], true);
					$item_val = $get_val ? $get_val : 'DESC';
					$item['fields'] = array(
						array(esc_html__('Ascending','bm-grid'),  'ASC'),
						array(esc_html__('Descending','bm-grid'), 'DESC')
					);
					
					$html .= '<select name="' .esc_attr($item['name']). '" id="' .esc_attr($item['id']). '" style="' .esc_attr($item['style']). '">';
					if($item['fields']){
						foreach($item['fields'] as $field){
							$html .= '<option value="' .esc_attr($field[1]). '" ' .selected($item_val, $field[1], false).'>' .esc_html($field[0]). '</option>';
						}
					}else{
						$html .= '<option selected="selected" value="0">' .esc_html__('No options','bm-grid'). '</option>';
					}
					$html .= '</select>';
                break;
				
				case 'custom-grid-layouts':
					$html .= '<button id="' .esc_attr($item['id']). '" type="button">' .esc_html__('Activate Layout Builder','bm-grid'). '</button>';
					$html .= bm_grid_display_modal($item);
					$html .= '<div class="modal-backdrop"></div>';
				break;
				
				case 'portfolio_layout_builder':
					if($item['fields']){
						$num = 1;
						if($get_val){
							$num = count($get_val['image_align']);
						}
						
						$html .= '<div  class="__ux-gallery-portfolio-layout-builder-wrap">';
						for($i=0; $i<$num; $i++) {
							$html .= '<div class="__ux-gallery-portfolio-layout-builder layout-row">';
							
							foreach($item['fields'] as $field_id => $field){
								$html .= '<select name="' .esc_attr($item['name']. '[' .$field_id. '][]'). '">';
								
								$value = 0;
								if($get_val){
									$value = $get_val[$field_id][$i];
								}
								
								foreach($field as $option){
									$html .= '<option value="' .esc_attr($option[1]). '" ' .selected($value, $option[1], false).'>' .esc_html($option[0]). '</option>';
								}
								
								$html .= '</select>';
							}
											
							$html .=   '<div class="tool-btn">';
							$html .=     '<button type="button" class="layout-add"><span class="dashicons dashicons-plus"></span></button>';
							$html .=     '<button type="button" class="layout-remove"><span class="dashicons dashicons-minus"></span></button>';
							$html .=   '</div>';
							$html .= '</div>';
						}
						$html .= '</div>';
					}
				break;
			}
			
			return $html;
		}
	}
	
}
?>