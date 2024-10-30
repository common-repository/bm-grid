<?php
/**
 * Template: Custom Grid Portfolio.
 */
function bm_grid_custom_grid_list($gallery_id, $paged=1, $ajax=false, $echo=false){
	if(isset($_POST['gallery_id'])){
		$gallery_id = intval($_POST['gallery_id']);
		$ajax = true;
		$echo = true;
	}
	
	$category            = get_post_meta($gallery_id, '__ux_gallery_custom_grid_image_source', true);
	$layouts             = get_post_meta($gallery_id, '__ux_gallery_custom_grid_layouts_' .$category, true);
	$item_style          = get_post_meta($gallery_id, '__ux_gallery_custom_grid_item_style', true);
	$transparent_mask    = get_post_meta($gallery_id, '__ux_gallery_custom_grid_transparent_mask', true);
	$show_title          = get_post_meta($gallery_id, '__ux_gallery_custom_grid_show_title', true);
	$show_category       = get_post_meta($gallery_id, '__ux_gallery_custom_grid_show_category', true);
	$text_align          = get_post_meta($gallery_id, '__ux_gallery_custom_grid_text_align', true);
	$text_align_for_text = get_post_meta($gallery_id, '__ux_gallery_custom_grid_text_align_for_text', true);
	$text_padding        = get_post_meta($gallery_id, '__ux_gallery_custom_grid_text_padding', true);
	$what_thumb          = get_post_meta($gallery_id, '__ux_gallery_custom_grid_what_thumb', true);
	$item_spacing        = get_post_meta($gallery_id, '__ux_gallery_custom_grid_spacing', true);
	$show_filter         = get_post_meta($gallery_id, '__ux_gallery_custom_grid_show_filter', true);
	$filter_align        = get_post_meta($gallery_id, '__ux_gallery_custom_grid_filter_align', true);
	
	$html = '';
	
	$number = -1;
	$per_page = $number ? $number : -1;
	
	$get_categories = get_categories(array(
		'parent' => $category
	));
	
	if(!is_array($category)){
		$category = array($category);
	}
	
	if(isset($_POST['cat_id'])){
		if(intval($_POST['cat_id']) != 0){
			$category = array($_POST['cat_id']);
		}
	}
	
	$post__not_in = array();
	if(isset($_POST['post__not_in'])){
		$post__not_in = isset( $_POST['post__not_in'] ) ? (array) $_POST['post__not_in'] : array();
	}
	
	if(isset($_POST['postNumber'])){
		$per_page = intval($_POST['postNumber']);
	}
	
	$the_query = new WP_Query(array(
		'posts_per_page' => $per_page,
		'category__in' => $category,
		'post__not_in' => $post__not_in,
		'meta_key' => '_thumbnail_id'
	));
	
	$max_num_pages = intval($the_query->max_num_pages);
	$found_posts = intval($the_query->found_posts);
	
	if($ajax){
		if($the_query->have_posts()){
			while($the_query->have_posts()){ $the_query->the_post();
				global $post;
				$post_class = 'post--' .$post->ID;
				$classes = array();
				$taxonomies = get_taxonomies(array('public' => true));
				foreach((array) $taxonomies as $taxonomy){
					if(is_object_in_taxonomy($post->post_type, $taxonomy)){
						foreach((array) get_the_terms($post->ID, $taxonomy) as $term){
							if(empty($term->slug)){
								continue;
							}
							
							$term_class = sanitize_html_class($term->slug);
							if(is_numeric($term_class) || !trim($term_class, '-')){
								$term_class = $term->term_id;
							}
							
							$classes[] = sanitize_html_class('filter_' . $term_class);
						}
					}
				}
				
				$classes = array_unique($classes);
				
				//lightbox
				$what_thumb_data = '';
				if($what_thumb == 'open-featured-img' && has_post_thumbnail()){
					$what_thumb_data = 'data-lightbox="true"';
				}
				
				//post bg color
				$bg_color = apply_filters( 'ux-gallery-post-bgcolor', '#ffffff', $post->ID );
				if($bg_color){
					$gallery_style = '<style type="text/css" scoped>.' .sanitize_html_class($post_class). ':after{ background-color: ' .esc_attr($bg_color). '; }</style>';
				}
				
				//thumbnail
				$thumb_width = 650;
				$thumb_height = 650;
				$thumb_black = BM_GRID_URL. 'images/blank.gif';
				$thumb_url = $thumb_black;
				
				if(has_post_thumbnail()){
					$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'bm-standard-thum');
					$thumb_width = $thumb[1];
					$thumb_height = $thumb[2];
					$thumb_url = $thumb[0];
				}
				
				//thumb padding top
				$thumb_padding_top = false;
				if($thumb_height > 0 && $thumb_width > 0){
					$thumb_padding_top = 'padding-top: ' . (intval($thumb_height) / intval($thumb_width)) * 100 . '%;';
				}
				
				//title link
				$title_link_before = $what_thumb != 'open-featured-img' ? '<a href="'.get_permalink().'" title="'.get_the_title().'" class="grid-item-tit-a">' : false;
				$title_link_after = $what_thumb != 'open-featured-img' ? '</a>' : false;
				
				//list layout
				$layout_array = array();
				if($layouts){
					$layout_array = $layouts;
				}
				
				$x = 0;
				$y = 0;
				$width = 3;
				$height = 3;
				
				if(count($layout_array)){
					foreach($layout_array as $layout){
						if($layout['post_id'] == $post->ID){
							$x = $layout['x'];
							$y = $layout['y'];
							$width = $layout['width'];
							$height = $layout['height'];
			
							// Image size for defferent size(width) Grid
							if(has_post_thumbnail()){
								if( $width > 3 && $width <= 9 ) {
									$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'bm-standard-thumb-medium'); 
								} elseif($width >= 10) {
									$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'bm-standard-thumb-big'); 
								} else {
									$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'bm-standard-thum');
								}
								$thumb_width = $thumb[1];
								$thumb_height = $thumb[2];
								$thumb_url = $thumb[0];
							}
			
						}
					}
				}

				//lazyload
				$image_lazyload = get_option('__ux_gallery_option_image_lazy_load');
				$image_lazyload_style = 'data-bg="' .esc_url($thumb_url). '" data-xx="'.$width.'"';
				$image_lazyload_class = 'ux-lazyload-bgimg';
				if(!$image_lazyload){
					$image_lazyload_style = 'style="background-image:url(' .esc_url($thumb_url). ');" data-xy="'.$width.'"'; 
				}
				
				$html .= '<div class="grid-stack-item ' .esc_attr(join(' ', $classes)). '" data-postid="' .esc_attr($post->ID). '" data-gs-x="' .esc_attr($x). '" data-gs-y="' .esc_attr($y). '" data-gs-width="' .esc_attr($width). '" data-gs-height="' .esc_attr($height). '">';
				$html .=   '<div class="grid-stack-item-content">';
				$html .=     '<div class="grid-item-inside" ' .wp_kses_post($what_thumb_data). '>';
				$html .=       '<div class="grid-item-con ' .sanitize_html_class($post_class). '">';
								
								if($what_thumb == 'open-featured-img'){
									$thumb_full = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
									$data_size = $thumb_full[1]. 'x' .$thumb_full[2];
									$html .= '<a title="' .esc_attr($post->post_excerpt). '" class="lightbox-item grid-item-mask-link" href="' .esc_url($thumb_full[0]). '" data-size="' .esc_attr($data_size). '"><img class="lightbox-img-hide" width="' .esc_attr($thumb_width). '" height="' .esc_attr($thumb_height). '" src="' .esc_url($thumb_black). '" alt="' .get_the_title(). '" title="' .get_the_title(). '" /></a>';
								}else{
									$html .= '<a href="' .get_permalink(). '" title="' .get_the_title(). '" class="grid-item-mask-link"></a>';
								}
								
								if($item_style == 'image'){
									$html .= '<div class="grid-item-con-text">';
										if($show_category == 'on'){ $html .= '<span class="grid-item-cate">' .bm_grid_get_the_category(' ', 'grid-item-cate-a'). '</span>'; }
										if($show_title == 'on'){ $html .= '<h2 class="grid-item-tit">' .wp_kses_post($title_link_before). '' .get_the_title(). '' .wp_kses_post($title_link_after). '</h2>'; }
									$html .= '</div>';
								}else{
									$html .= '<div class="grid-item-con-text-show">';
										if($show_category == 'on'){ $html .= '<span class="grid-item-cate">' .bm_grid_get_the_category(' ', 'grid-item-cate-a'). '</span>'; }
										$html .= '<h2 class="grid-item-tit">' .get_the_title(). '</h2>';
									$html .= '</div>';
								}
				$html .=       '</div>';
				$html .=       '<div class="brick-content ux-lazyload-wrap" style=" ' .esc_attr($thumb_padding_top). '">';
				$html .=         '<div class="' .sanitize_html_class($image_lazyload_class). ' ux-background-img" ' .wp_kses_post($image_lazyload_style). '></div>';
				$html .=       '</div>';
				$html .=     '</div>';
				$html .=   '</div>';
				$html .= '</div>';
			}
			wp_reset_postdata();
		}
	}else{
		//spacing
		$spacing = '0';
		switch($item_spacing){
			case 'normal': $spacing = '40'; break;
			case '10': $spacing = '10'; break;
			case '20': $spacing = '20'; break;
			case '30': $spacing = '30'; break;
			case '40': $spacing = '40'; break;
			case 'no-spacing': $spacing = '0'; break;
		}
		
		//transparent mask
		if($item_style == 'image' && $transparent_mask){
			$html .= '<style type="text/css">.grid-stack .grid-item-con:hover:after{ opacity: ' .esc_attr($transparent_mask). '; }</style>';
        }
		
		//list width
		$list_width_class = 'ux-portfolio-full';
		
		//what thumb class
		$what_thumb_class = '';
		if($what_thumb == 'open-featured-img'){
			$what_thumb_class = 'lightbox-photoswipe';
		}
		
		//text align class
		$text_align_class = 'grid-text-center';
		if($text_align){
			$text_align_class = $text_align;
			if($item_style == 'image-text'){
				$text_align_class = $text_align_for_text;
			}
		}
		
		//mouseover text class
		$mouseover_effect_class = false;
		$show_text_class = false;
		if($item_style != 'image'){
			$show_text_class = 'masonry-grid-show-text';
		}
		
		//grid padding class
		$grid_pading_class = false;
		if($text_padding == 'on' && ($show_text_align == 'grid-text-left' || $show_text_align == 'grid-text-right')){
			$grid_pading_class = 'masonry-text-padding';
		}
		
		if($show_filter == 'above-gallery'){
			$html .= '<div class="clearfix filters">';
			$html .=   '<ul class="filters-ul">';
			$html .=     '<li class="filters-li active"><a id="all" class="filters-a" href="#" data-filter="*">All<span class="filter-num">' .esc_html($found_posts). '</span></a></li>';
				if($get_categories){
					foreach($get_categories as $num => $category){
						$get_posts = get_posts(array(
							'posts_per_page' => -1,
							'cat' => $category->term_id,
							'meta_key' => '_thumbnail_id'
						));
						$category_count = count($get_posts);
						
						$html .= sprintf('<li class="filters-li"><a class="filters-a" data-filter=".filter_%1$s" href="%2$s" data-catid="%5$s" data-galleryid="%6$s">%3$s<span class="filter-num">%4$s</span></a></li>',
							esc_attr($category->slug),
							esc_url(get_category_link($category->term_id)),
							esc_html($category->name),
							esc_html($category_count),
							esc_attr($category->term_id),
							esc_attr($gallery_id)
						);
					}
				}
			$html .=   '</ul>';
			$html .= '</div>';
		}
		
		$html .= '<div class="' .esc_attr($list_width_class). '">';
		
		$html .=   '<div class="grid-stack ' .sanitize_html_class($what_thumb_class). ' ' .sanitize_html_class($text_align_class). ' ' .sanitize_html_class($mouseover_effect_class). ' ' .sanitize_html_class($show_text_class). ' ' .sanitize_html_class($grid_pading_class).'" data-spacing="' .esc_attr($spacing). '" data-item-style="' .esc_attr($item_style). '" data-number="' .esc_attr($per_page). '">';
		
			if($the_query->have_posts()){
				$html .= bm_grid_custom_grid_list($gallery_id, 1, true);
			}
		
		$html .=   '</div>';
		
		$html .= '</div>';
	}
	
	
	if($echo){
		echo $html;
	}else{
		return $html;
	}
	
	exit;
}
?>