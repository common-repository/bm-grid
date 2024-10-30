<?php
/**
 * Template: Masonry List.
 */
function bm_grid_masonry_list($gallery_id, $paged=1, $ajax=false, $echo=false){
	if(isset($_POST['gallery_id'])){
		$gallery_id = intval($_POST['gallery_id']);
		$ajax = true;
		$echo = true;
	}
	
	$layout = get_post_meta($gallery_id, '__ux_gallery_layout', true);
	if($layout == 'standard-grid-portfolio'){
		$category      = get_post_meta($gallery_id, '__ux_gallery_standard_grid_category', true);
		$orderby       = get_post_meta($gallery_id, '__ux_gallery_standard_grid_category_orderby', true);
		$order         = get_post_meta($gallery_id, '__ux_gallery_standard_grid_category_orderby_order', true);
		$show_title    = get_post_meta($gallery_id, '__ux_gallery_standard_grid_show_title', true);
		$text_align    = get_post_meta($gallery_id, '__ux_gallery_standard_grid_text_align', true);
		$show_padding  = get_post_meta($gallery_id, '__ux_gallery_standard_grid_show_padding', true);
		$columns       = get_post_meta($gallery_id, '__ux_gallery_standard_grid_columns', true);
		$number        = get_post_meta($gallery_id, '__ux_gallery_standard_grid_number', true);
		$pagination    = get_post_meta($gallery_id, '__ux_gallery_standard_grid_pagination', true);
		$ratio         = get_post_meta($gallery_id, '__ux_gallery_standard_grid_ratio', true);
		$spacing       = get_post_meta($gallery_id, '__ux_gallery_standard_grid_spacing', true);
		$show_filter   = get_post_meta($gallery_id, '__ux_gallery_standard_grid_show_filter', true);
		$filter_align  = get_post_meta($gallery_id, '__ux_gallery_standard_grid_filter_align', true);
	}else{
		$category      = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_category', true);
		$orderby       = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_category_orderby', true);
		$order         = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_category_orderby_order', true);
		$columns       = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_columns', true);
		$number        = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_number', true);
		$pagination    = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_pagination', true);
		$what_thumb    = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_what_thumb', true);
		$spacing       = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_spacing', true);
		$show_filter   = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_show_filter', true);
		$filter_align  = get_post_meta($gallery_id, '__ux_gallery_masonry_portfolio_filter_align', true);
	}
	
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
	
	$html = '';
	
	$the_query = new WP_Query(array(
		'posts_per_page' => $per_page,
		'category__in' => $category,
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $paged,
		'post__not_in' => $post__not_in,
		'tax_query' => array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => array('post-format-gallery'),
			)
		)
	));
	
	$max_num_pages = intval($the_query->max_num_pages);
	$found_posts =  intval($the_query->found_posts);
	
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
				
				//what thumb data
				$what_thumb_data = '';
				if($layout == 'masonry-portfolio'){
					if($what_thumb == 'open-featured-img' && has_post_thumbnail()){
						$what_thumb_data = 'data-lightbox="true"';
					}
					$title_link_before = $what_thumb != 'open-featured-img' ? '<a href="'.get_permalink().'" title="'.get_the_title().'" class="grid-item-tit-a">' : false;
					$title_link_after = $what_thumb != 'open-featured-img' ? '</a>' : false;
				}
				
				//post bg color
				if($layout == 'masonry-portfolio'){
					$bg_color = apply_filters( 'ux-gallery-post-bgcolor', '#ffffff', $post->ID );
					if($bg_color){
						$gallery_style = '<style type="text/css" scoped>.' .sanitize_html_class($post_class). ':after{ background-color: ' .esc_attr($bg_color). '; }</style>';
					}
				}
				
				//padding class
				$grid_pading_class = false;
				if($layout == 'standard-grid-portfolio'){
					if($show_padding == 'on'){
						$grid_pading_class = 'standard-text-padding';
					}
				}
				
				//thumbnail
				if($layout == 'masonry-portfolio'){
					switch($columns){
						case '2': $gallery_image_size = 'ux-standard-thumb-medium'; break;
						case '3': $gallery_image_size = 'ux-standard-thumb'; break;
						default:  $gallery_image_size = 'ux-standard-thumb'; break;
					}
				}else{
					$gallery_image_size = 'thumb-43-';
					$gallery_shape = apply_filters( 'ux-gallery-post-shape', false, $post->ID );
					switch($columns){
						case '2': 
							if($gallery_shape == 'gallery_shape_1'){
								$gallery_image_size .= 'medium';
							}else{
								$gallery_image_size .= 'big';
							}
						break;
						case '3':
							if($gallery_shape == 'gallery_shape_1'){
								$gallery_image_size .= 'small';
							}else{
								$gallery_image_size .= 'medium';
							}
						break;
						case '4': $gallery_image_size .= 'small'; break;
						case '5': $gallery_image_size .= 'small'; break;
						case '6': $gallery_image_size .= 'small'; break;
						default: $gallery_image_size .= 'medium'; break;
					}
				}
				
				$thumb_width = 650;
				$thumb_height = 490;
				$thumb_black = BM_GRID_URL. 'images/blank.gif';
				$thumb_url = $thumb_black;
				
				if(has_post_thumbnail()){
					$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), $gallery_image_size);
					$thumb_width = $thumb[1];
					$thumb_height = $thumb[2];
					$thumb_url = $thumb[0];
				}
			
				$thumb_padding_top = false;
				if($thumb_height > 0 && $thumb_width > 0){
					$thumb_padding_top = 'padding-top: ' . (intval($thumb_height) / intval($thumb_width)) * 100 . '%;';
				}
				
				$image_lazyload = get_option('__ux_gallery_option_image_lazy_load');
				$image_lazyload_style = 'data-bg="' .esc_url($thumb_url). '"';
				$image_lazyload_class = 'ux-lazyload-bgimg';
				$image_lazyload_img_style = 'src="' .esc_url($thumb_black). '" data-src="' .esc_url($thumb_url). '"';
				$image_lazyload_img_class = 'lazy';
				if(!$image_lazyload){
					$image_lazyload_style = 'style="background-image:url(' .esc_url($thumb_url). ');"';
					$image_lazyload_img_style = 'src="' .esc_url($thumb_url). '"';
					$image_lazyload_img_class = '';
				}
				
				
				$html .= '<section class="grid-item ' .esc_attr(join(' ', $classes)). '" data-postid="' .esc_attr($post->ID). '">';
				$html .=   '<div class="grid-item-inside" ' .wp_kses_post($what_thumb_data). '>';
				
				if($layout == 'masonry-portfolio'){
					$html .= '<div class="grid-item-con ' .sanitize_html_class($post_class). '">';
					
					if($what_thumb == 'open-featured-img'){
						$thumb_full = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
						$data_size = $thumb_full[1]. 'x' .$thumb_full[2];
						
						$html .= '<a title="' .esc_attr($post->post_excerpt). '" class="lightbox-item grid-item-mask-link" href="' .esc_url($thumb_full[0]). '" data-size="' .esc_attr($data_size). '"><img class="lightbox-img-hide" width="' .esc_attr($thumb_width). '" height="' .esc_attr($thumb_height). '" src="' .esc_url($thumb_black). '" alt="' .get_the_title($post->ID). '" title="' .get_the_title($post->ID). '" /></a>';
					}else{
						$html .= '<a href="' .get_permalink(). '" title="' .get_the_title(). '" class="grid-item-mask-link"></a>';
					}
					
					$html .=   '<div class="grid-item-con-text">';
					$html .=     '<span class="grid-item-cate">' .bm_grid_get_the_category(' ', 'grid-item-cate-a'). '</span>';
					$html .=     '<h2 class="grid-item-tit">' .wp_kses_post($title_link_before). '' .get_the_title(). '' .wp_kses_post($title_link_after). '</h2>';
					$html .=   '</div>';
					$html .= '</div>';
				}
				
				if($layout != 'masonry-portfolio'){
					$html .= '<div class="brick-content ux-lazyload-wrap" style=" ' .esc_attr($thumb_padding_top). '">';
					$html .=   '<div class="' .sanitize_html_class($image_lazyload_class). ' ux-background-img" ' .wp_kses_post($image_lazyload_style). '></div>';
					$html .= '</div>';
				}else{
					$html .= '<div class="brick-content ux-lazyload-wrap" style=" ' .esc_attr($thumb_padding_top). '">';
					$html .=   '<img class="ux-lazyload-img ' .sanitize_html_class($image_lazyload_img_class). '" width="' .esc_attr($thumb_width). '" height="' .esc_attr($thumb_height). '" alt="' .get_the_title($post->ID). '" title="' .get_the_title($post->ID). '" ' .wp_kses_post($image_lazyload_img_style). '/>';
					$html .= '</div>';
				}
						
				//grid title
				if($layout == 'standard-grid-portfolio' && $show_title == 'on'){
					$html .= '<div class="grid-item-con-text-tit-shown ' .esc_attr($text_align). ' ' .esc_attr($grid_pading_class). '">';
					if($show_title == 'on'){
						$html .= '<h2 class="grid-item-tit">' .get_the_title(). '</h2>';
					}
					$html .= '</div>';
				}
				
				$html .= '</div>';
				$html .= '</section>';
			}
			wp_reset_postdata();
		}
		
	}else{
		$page_pagination_tag = '';
		$page_pagination_class = '';
		if($pagination == 'infiniti-scroll'){
			$page_pagination_tag = 'data-paged="2" data-galleryid="' .esc_attr($gallery_id). '" data-max="' .esc_attr($max_num_pages). '"';
			$page_pagination_class = 'infiniti-scroll';
		}
		
		//spacing class
		$spacing_class = ''; $data_spacing = '0';
		switch($spacing){
			case 'narrow': $spacing_class = 'ux-portfolio-spacing-10'; $data_spacing = '10'; break;
			case 'normal': $spacing_class = 'ux-portfolio-spacing-40'; $data_spacing = '40'; break;
			case 'no-spacing': $spacing_class = 'ux-portfolio-spacing-none'; $data_spacing = '0'; break;
		}
		
		//columns class
		$columns_class = 'ux-portfolio-2col';
		switch($columns){
			case '2': $columns_class = 'ux-portfolio-2col'; break;
			case '3': $columns_class = 'ux-portfolio-3col'; break;
			case '4': $columns_class = 'ux-portfolio-4col'; break;
			case '5': $columns_class = 'ux-portfolio-5col'; break;
			case '6': $columns_class = 'ux-portfolio-6col'; break;
		}
		
		//list width
		$list_width_class = 'ux-portfolio-full';
		
		//filter
		$has_filter = '';
		if($show_filter == 'above-gallery'){
			$has_filter = 'ux-has-filter';
		}
		
		//grid ratio
		$grid_ratio_data = '';
		$grid_ratio_class = '';
		if($layout == 'standard-grid-portfolio'){
			switch($ratio){
				case '4_3': $grid_ratio_data = '0.75'; $grid_ratio_class = 'ux-ratio-34'; break;
				case '16_9': $grid_ratio_data = '0.5625'; $grid_ratio_class = 'ux-ratio-169'; break;
				case '1_1': $grid_ratio_data = '1'; $grid_ratio_class = 'ux-ratio-11'; break;
			}
		}
		
		//layout class
		$layout_class = '';
		switch($layout){
			case 'masonry-portfolio': $layout_class = 'masonry-grid'; break;
			case 'standard-grid-portfolio': $layout_class = 'grid-list'; break;
		}
		
		$grid_class = '';
		if($layout == 'standard-grid-portfolio'){
			$grid_class = 'grid-list-tit-shown';
		}
		
		//what thumb class
		$what_thumb_class = '';
		if($layout == 'masonry-portfolio'){
			if($what_thumb == 'open-featured-img'){
				$what_thumb_class = 'lightbox-photoswipe';
			}
		}
		
		//no ajax output
		$html .= '<div class="container-masonry ' .sanitize_html_class($spacing_class). ' ' .sanitize_html_class($columns_class). ' ' .sanitize_html_class($list_width_class). ' ' .sanitize_html_class($has_filter). ' ' .sanitize_html_class($grid_ratio_class). ' ' .sanitize_html_class($filter_align). '" data-ratio="' .esc_attr($grid_ratio_data). '" data-col="' .esc_attr($columns). '" data-spacer="' .esc_attr($data_spacing). '" data-template="' .esc_attr($layout). '" data-found="' .esc_attr($found_posts). '" data-number="' .esc_attr($per_page). '">';
		
		if($show_filter == 'above-gallery'){
			$html .= '<div class="clearfix filters">';
			$html .=   '<ul class="filters-ul">';
			$html .=     '<li class="filters-li active"><a id="all" class="filters-a" href="#" data-filter="*">All<span class="filter-num">' .esc_html($found_posts). '</span></a></li>';
				if($get_categories){
					foreach($get_categories as $num => $category){
						$get_posts = get_posts(array(
							'posts_per_page' => -1,
							'cat' => $category->term_id,
							'tax_query' => array(
								array(
									'taxonomy' => 'post_format',
									'field' => 'slug',
									'terms' => array('post-format-gallery'),
								)
							)
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
		
		$html .=   '<div class="masonry-list ' .sanitize_html_class($layout_class). ' ' .sanitize_html_class($grid_class). ' ' .sanitize_html_class($what_thumb_class). ' ' .sanitize_html_class($page_pagination_class). '" ' .wp_kses_post($page_pagination_tag). '>';
		
			if($the_query->have_posts()){
				$html .= bm_grid_masonry_list($gallery_id, 1, true);
			}
		
		$html .=   '</div>';
		
		if($the_query->have_posts() && $pagination != 'infiniti-scroll'){
			$html .= bm_grid_pagination($gallery_id, $max_num_pages, $found_posts, false); 
		}
		
		$html .= '</div>';
		
		
	}
	
	if($echo){
		echo $html;
	}else{
		return $html;
	}
	
	exit;
}
add_action('wp_ajax_bm_grid_masonry_list', 'bm_grid_masonry_list');
add_action('wp_ajax_nopriv_bm_grid_masonry_list', 'bm_grid_masonry_list');
?>