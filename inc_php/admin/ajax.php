<?php
/**
 * Hidden fields.
 */
function bm_grid_ajax_options() { 
	$formData = isset( $_POST['formData'] ) ? (array) $_POST['formData'] : array(); 
	if(isset($formData)){
		if(is_array($formData)){
			$count = count($formData);
			$i = 0;
			foreach($formData as $data){
				$old_value = get_option(sanitize_text_field($data['name']));
				
				if($old_value == $data['value']){
					$update = true;
				}else{
					$update = update_option(sanitize_text_field($data['name']), sanitize_text_field($data['value']));
				}
				
				if($update){
					$i++;
				}
			}
			
			if($count == $i){
				echo 'ok';
			}
		}
	}
	
	exit;
}
add_action('wp_ajax_bm_grid_ajax_options', 'bm_grid_ajax_options');

/**
 * Edit Portfolio list layout for Page.
 */
function bm_grid_custom_grid_layouts_ajax(){
	$cat_id = intval($_POST['cat_id']);
	$post_ID = intval($_POST['post_ID']);
	
	if($post_ID){ ?>
        <div class="edit-portfolio-list-layout">
            <div class="grid-stack">
				<?php
				$layout_array = array();
				$list_layout = get_post_meta($post_ID, '__ux_gallery_custom_grid_layouts_' .$cat_id, true);
				if($list_layout){
					$layout_array = $list_layout;
				}
				
				$category = array();
				if($cat_id){
					$category = array($cat_id);
				}
				
				$get_categories = get_categories(array(
					'parent' => $cat_id
				));
				
				if($get_categories){
					foreach($get_categories as $cat){
						array_push($category, $cat->term_id);
					}
				}
				
				$get_posts = get_posts(array(
					'posts_per_page' => -1,
					'category__in' => $category,
					'meta_key' => '_thumbnail_id'
				));
				
				if($get_posts){
					$i = 0;
					$width = 3;
					$height = 3;
					$col = 12 / $width;
					$row = 0;
					
					foreach($get_posts as $post){
						if($i > 0 && $i % $col == 0){
							$row++;
						}
						
						$x = ($i % $col) * $width;
						$y = $row * $height;
						
						if(count($layout_array)){
							foreach($layout_array as $layout){
								if($layout['post_id'] == $post->ID){
									$x = $layout['x'];
									$y = $layout['y'];
									$width = $layout['width'];
									$height = $layout['height'];
								}
							}
						}
						
						$bg_style = false;
						if(has_post_thumbnail($post->ID)){
							$thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'ux-thumb-11-normal');
							$bg_style = 'background-image:url(' .esc_url($thumb[0]). ');';
						} ?>
						
						<div class="grid-stack-item" data-postid="<?php echo esc_attr($post->ID); ?>"
							data-gs-x="<?php echo esc_attr($x); ?>" data-gs-y="<?php echo esc_attr($y); ?>"
							data-gs-width="<?php echo esc_attr($width); ?>" data-gs-height="<?php echo esc_attr($height); ?>">
								<div class="grid-stack-item-content" style=" <?php echo esc_attr($bg_style); ?>">
									<?php if(!has_post_thumbnail($post->ID)){
										echo '<div class="title">' .get_the_title($post->ID). '</div>';
									} ?>
								</div>
						</div>
						<?php
						$i ++;
					}
				}  ?>
            </div>
        </div>
	<?php
    }
	exit;
}
add_action('wp_ajax_bm_grid_custom_grid_layouts_ajax', 'bm_grid_custom_grid_layouts_ajax');

/**
 * Save Portfolio list layout for Page.
 */
function bm_grid_custom_grid_layouts_save_ajax(){
	$data = isset( $_POST['data'] ) ? (array) $_POST['data'] : array(); 
	$cat_id = intval($_POST['cat_id']);
	$post_ID = intval($_POST['post_ID']);
	
	if($post_ID){
		$layouts = array();
		
		$result = update_post_meta($post_ID, '__ux_gallery_custom_grid_layouts_' .$cat_id, $data);
		if($result){
			echo 'ok';
		}
	}
	
	exit;
}
add_action('wp_ajax_bm_grid_custom_grid_layouts_save_ajax', 'bm_grid_custom_grid_layouts_save_ajax');
?>