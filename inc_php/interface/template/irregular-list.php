<?php
/**
 * Template: Irregular List.
 */
function bm_grid_irregular_list($gallery_id, $paged=1, $ajax=false, $echo=false){
	if(isset($_POST['gallery_id'])){
		$gallery_id = intval($_POST['gallery_id']);
		$ajax = true;
		$echo = true;
	}
	
	if(isset($_POST['paged'])){
		$paged = intval($_POST['paged']);
	}
	
	$layout_builder = get_post_meta($gallery_id, '__ux_gallery_irregular_list_layout_builder', true);
	$category       = get_post_meta($gallery_id, '__ux_gallery_irregular_list_category', true);
	$orderby        = get_post_meta($gallery_id, '__ux_gallery_irregular_list_category_orderby', true);
	$order          = get_post_meta($gallery_id, '__ux_gallery_irregular_list_category_orderby_order', true);
	$number         = get_post_meta($gallery_id, '__ux_gallery_irregular_list_number', true);
	$pagination     = get_post_meta($gallery_id, '__ux_gallery_irregular_list_pagination', true);
	$show_tags      = get_post_meta($gallery_id, '__ux_gallery_irregular_list_show_tags', true);
	
	$per_page = $number ? $number : -1;
	
	if(!is_array($category)){
		$category = array($category);
	}
	
	$html = '';
	
	$the_query = new WP_Query(array(
		'posts_per_page' => $per_page,
		'category__in' => $category,
		'orderby' => $orderby,
		'order' => $order,
		'paged' => $paged,
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
		$layout_builder_count = 0;
		if(isset($layout_builder['image_align'])){
			$layout_builder_count = count($layout_builder['image_align']);
		}
		
		if($the_query->have_posts()){
			$i = intval($per_page) * ($paged - 1);
			while($the_query->have_posts()){ $the_query->the_post();
				$num = $i % $layout_builder_count;
				$post_id = get_the_ID();
			
				$gallery_brightness = apply_filters( 'ux-gallery-post-brightness', 'light-logo', $post_id );
				
				$cusl_class = '';
				if($gallery_brightness == 'dark-logo'){
					$cusl_class = 'cusl-dark-img';
				}
				
				$image_align = bm_grid_irregular_list_cusl_class('image_align', $num, $layout_builder);
				$title_align = bm_grid_irregular_list_cusl_class('title_align', $num, $layout_builder);
				$top_padding = bm_grid_irregular_list_cusl_class('top_padding', $num, $layout_builder);
				$image_width = bm_grid_irregular_list_cusl_class('image_width', $num, $layout_builder);
				
				$thumb_width = 600;
				$thumb_height = 400;
				$thumb_black = BM_GRID_URL. 'images/blank.gif';
				$thumb_url = $thumb_black;
				
				if(has_post_thumbnail()){
					$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
					$thumb_width = $thumb[1];
					$thumb_height = $thumb[2];
					$thumb_url = $thumb[0];
				}
			
				$thumb_padding_top = false;
				if($thumb_height > 0 && $thumb_width > 0){
					$thumb_padding_top = 'padding-top: ' . (intval($thumb_height) / intval($thumb_width)) * 100 . '%;';
				}
				
				$image_lazyload = get_option('__ux_gallery_option_image_lazy_load');
				$image_src = ' src="'.esc_url($thumb_url).'"';
				$image_class = ' ';
				if($image_lazyload) {
					$image_src = 'src="' .esc_url($thumb_black). '" data-src="' .esc_url($thumb_url). '"';
					$image_class = 'lazy';
				}
				
				$html .= '<section class="cusl-style-unit ' .sanitize_html_class($image_align). ' ' .sanitize_html_class($title_align). ' ' .sanitize_html_class($top_padding). ' ' .sanitize_html_class($image_width). '" data-postid="' .esc_attr($post_id). '">';
				$html .=   '<div class="cusl-style-unit-inn">';
				$html .=     '<div class="cusl-img-wrap" style="' .esc_attr($thumb_padding_top). '"><img ' .wp_kses_post($image_src). ' alt="' .get_the_title(). '" class="cusl-img ux-lazyload-img ' .sanitize_html_class($image_class). '" /></div>';
				$html .=     '<div class="cusl-style-normal-text-wrap">';
				$html .=       '<div class="cusl-style-tit-wrap">';
				$html .=         '<span class="cusl-cate">' .bm_grid_get_the_category(' ', 'cusl-cate-a'). '</span>';
				$html .=         '<h2 class="cusl-tit"><a class="cusl-tit-a" href="' .get_permalink(). '" title="' .get_the_title(). '">' .get_the_title(). '</a></h2>';
				$html .=       '</div>';
				$html .=     '</div>';
				$html .=     '<div class="cusl-style-light-text-wrap">';
				$html .=       '<div class="cusl-style-tit-wrap">';
				$html .=         '<span class="cusl-cate">' .bm_grid_get_the_category(' ', 'cusl-cate-a'). '</span>';
				$html .=         '<h2 class="cusl-tit"><a class="cusl-tit-a" href="' .get_permalink(). '" title="' .get_the_title(). '">' .get_the_title(). '</a></h2>';
				$html .=       '</div>';
				$html .=     '</div>';
				$html .=   '</div>';
				$html .= '</section>';
				
				$i++;
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
		
		//no ajax output
		$html .= '<div class="cusl-style-list ' .sanitize_html_class($page_pagination_class). '" ' .wp_kses_post($page_pagination_tag). '>';
		
		if($the_query->have_posts()){
			$html .= bm_grid_irregular_list($gallery_id, 1, true);
		}
		
		$html .= '</div>';
		
		if($the_query->have_posts() && $pagination != 'infiniti-scroll'){
			$html .= wp_kses_post(bm_grid_pagination($gallery_id, $max_num_pages, $found_posts, false)); 
		}
	}
	
	if($echo){
		echo $html;
	}else{
		return $html;
	}
	
	exit;
}
add_action('wp_ajax_bm_grid_irregular_list', 'bm_grid_irregular_list');
add_action('wp_ajax_nopriv_bm_grid_masonry_list', 'bm_grid_irregular_list');

/**
 * Irregular List layout cusl class.
 */
function bm_grid_irregular_list_cusl_class($key, $num, $builder){
	$return = false;
	if($builder){
		if(isset($builder[$key])){
			$layout = $builder[$key];
			
			switch($key){
				case 'image_align':
					switch($layout[$num]){
						case 'left':   $return = 'cusl-img-left'; break;
						case 'center': $return = 'cusl-img-center'; break;
						case 'right':  $return = 'cusl-img-right'; break;
					}
				break;
				case 'title_align':
					switch($layout[$num]){
						case 'top-left':     $return = 'cusl-text-top-left'; break;
						case 'middle-left':  $return = 'cusl-text-middle-left'; break;
						case 'bottom-left':  $return = 'cusl-text-bottom-left'; break;
						case 'top-right':    $return = 'cusl-text-top-right'; break;
						case 'middle-right': $return = 'cusl-text-middle-right'; break;
						case 'bottom-right': $return = 'cusl-text-bottom-right'; break;
					}
				break;
				case 'top_padding':
					switch($layout[$num]){
						case '100px':   $return = 'cusl-100-padding'; break;
						case 'overlap': $return = 'cusl-negative-padding'; break;
					}
				break;
				case 'image_width':
					switch($layout[$num]){
						case '30%': $return = 'cusl-img-w30'; break;
						case '40%': $return = 'cusl-img-w40'; break;
						case '50%': $return = 'cusl-img-w50'; break;
						case '60%': $return = 'cusl-img-w60'; break;
						case '70%': $return = 'cusl-img-w70'; break;
					}
				break;
			}
		}
	}
	
	return $return;
}
?>