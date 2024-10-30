<?php
/**
 * Pagination
 */
function bm_grid_pagination($gallery_id, $max, $found, $echo=true){
	$html = '';
	if($max > 1){
		$pagination_text = apply_filters( 'ux-gallery-pagination-text', __('LOAD MORE ARTICLES','bm-grid') );
		$loading_text = apply_filters( 'ux-gallery-pagination-loading-text', __('LOADING...','bm-grid') );
		
		$html = '';
		$html .= '<div class="clearfix pagenums tw_style page_twitter ux-gallery-load-more" data-pagetext="' .esc_attr($pagination_text). '" data-loadingtext="' .esc_attr($loading_text). '">';
		$html .=   '<a class="tw-style-a ux-btn ux-page-load-more" data-galleryid="' .esc_attr($gallery_id). '" data-max="' .esc_attr($max). '" data-paged="2" href="#">' .esc_html($pagination_text). '</a>';
		$html .= '</div>';
		
	}
	
	if($echo){
		echo $html;
	}else{
		return $html;
	}
}

/**
 * Get The Category
 */
function bm_grid_get_the_category($separator=' ', $class=''){
	$post_id = get_the_ID();
	
	$categories = get_the_category();
	$output = '';
	
	if(!empty($categories)){
		foreach( $categories as $category ) {
			$output .= '<a href="' .esc_url(get_category_link($category->term_id)). '" alt="' .esc_attr(sprintf(__('View all posts in %s','bm-grid'), $category->name)). '" class="' .sanitize_html_class($class). '">' .esc_html($category->name). '</a>' .$separator;
		}
		return trim($output, $separator);
	}
}

/**
 * Photoswipe Wrap
 */
function bm_grid_photoswipe_wrap(){
	$html = '';
	
	$html .= '<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">';
	$html .=   '<div class="pswp__bg"></div>';
	$html .=   '<div class="pswp__scroll-wrap">';
	$html .=     '<div class="pswp__container">';
	$html .=       '<div class="pswp__item"></div>';
	$html .=       '<div class="pswp__item"></div>';
	$html .=       '<div class="pswp__item"></div>';
	$html .=     '</div>';
	$html .=     '<div class="pswp__ui pswp__ui--hidden">';
	$html .=       '<div class="pswp__top-bar">';
	$html .=         '<div class="pswp__counter"></div>';
	$html .=         '<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>';
	$html .=         '<button class="pswp__button pswp__button--share" title="Share"></button>';
	$html .=         '<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>';
	$html .=         '<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>';
	$html .=         '<div class="pswp__preloader">';
	$html .=           '<div class="pswp__preloader__icn">';
	$html .=             '<div class="pswp__preloader__cut">';
	$html .=               '<div class="pswp__preloader__donut"></div>';
	$html .=             '</div>';
	$html .=           '</div>';
	$html .=         '</div>';
	$html .=       '</div>';
	$html .=       '<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">';
	$html .=         '<div class="pswp__share-tooltip"></div>';
	$html .=       '</div>';
	$html .=       '<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>';
	$html .=       '<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>';
	$html .=       '<div class="pswp__caption">';
	$html .=         '<div class="pswp__caption__center"></div>';
	$html .=       '</div>';
	$html .=     '</div>';
	$html .=   '</div>';
	$html .= '</div>';
	
	return $html;
}

/**
 * Body class
 */
function bm_grid_body_class( $classes ){
	$lightbox_skin = get_option('__ux_gallery_option_lightbox_skin');
	$lightbox_skin_class = 'pswp-dark-skin';
	if($lightbox_skin == 'light'){
		$lightbox_skin_class = 'pswp-light-skin';
	}
	$classes[] = $lightbox_skin_class;
	
	
	return $classes;
}
add_filter( 'body_class','bm_grid_body_class' );

?>