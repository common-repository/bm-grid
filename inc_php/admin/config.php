<?php
/**
 * Config
 */
function bm_grid_option_config() {
	global $post;
	
	$config = array(
	
		//BM Gallery Post Options
		'gallery-post-options' => array(
			'title'     => esc_html__( 'BM Grid Post Options','bm-grid' ),
			'callback'  => 'bm_grid_display_meta_option',
			'screen'    => 'post',
			'context'   => 'normal',
			'priority'  => 'high',
			'section'   => array(
				array(
					'items' => array(
						//For Thumbnail
						array('title'   => esc_html__('For Thumbnail','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_for_thumbnail',
							  'default' => 'default',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Default','bm-grid'),        'default'),
								  array(esc_html__('External Link','bm-grid'),  'external-link'),
								  array(esc_html__('Lightbox Video','bm-grid'), 'lightbox-video')
							  )),
						
						//External Link URL
						array('title'   => esc_html__('External Link URL','bm-grid'),
							  'desc'    => '',
							  'type'    => 'text',
							  'name'    => '__ux_gallery_thumb_external_link',
							  'style'   => 'width:50%;',
							  'ctrl'    => '__ux_gallery_for_thumbnail,external-link'),
						
						//Embed Video Code
						array('title'   => esc_html__('Embed Video Code','bm-grid'),
							  'desc'    => '',
							  'type'    => 'textarea',
							  'name'    => '__ux_gallery_thumb_embed_video_code',
							  'style'   => 'width:50%;',
							  'ctrl'    => '__ux_gallery_for_thumbnail,lightbox-video')
					)
				)
			)
		),
		
		//BM Gallery Page Options - Insert Gallery
		'gallery-page-options-insert' => array(
			'title'     => esc_html__( 'Insert Grid','bm-grid' ),
			'callback'  => 'bm_grid_display_meta_option',
			'screen'    => 'page',
			'context'   => 'side',
			'priority'  => 'high',
			'section'   => array(
				array(
					'items' => array(
						//Shortcode
						array('title'   => esc_html__('Shortcode','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_shortcode_selector',
							  'nosave'  => true,
							  'thstyle' => 'width:auto;',
							  'fields'  => bm_grid_shortcode_fields()),
							  
						//Insert Button
						array('type'    => 'switch',
							  'text'    => esc_html__('Insert','bm-grid'),
							  'name'    => '__ux_gallery_insert_shortcode',
							  'thstyle' => 'width:auto;')
					)
				)
			)
		),
	
		//BM Gallery General Options
		'gallery-general-options' => array(
			'title'     => esc_html__( 'BM Grid General Options','bm-grid' ),
			'callback'  => 'bm_grid_display_general_options',
			'screen'    => 'option',
			'section'   => array(
				array(
					'items' => array(
						//Lightbox Skin
						array('title'   => esc_html__('Lightbox Skin','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_option_lightbox_skin',
							  'default' => 'dark',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Dark','bm-grid'),  'dark'),
								  array(esc_html__('Light','bm-grid'), 'light')
							  )),
						
						//Image Lazy Load
						array('title'   => esc_html__('Image Lazy Load','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_option_image_lazy_load',
							  'default' => 'on')
					)
				)
			)
		),
	
		//BM Gallery Page Options
		'gallery-page-options' => array(
			'title'     => esc_html__( 'Grid Settings','bm-grid' ),
			'callback'  => 'bm_grid_display_meta_option',
			'screen'    => 'bm-grid',
			'context'   => 'normal',
			'priority'  => 'high',
			'section'   => array(
				'custom-grid-portfolio'   => array(
					'title'   => esc_html__('Custom Grid Portfolio','bm-grid'),
					'default' => true,
					'name'    => '__ux_gallery_layout'
				),
				'masonry-portfolio'       => array('title'   => esc_html__('Masonry Portfolio','bm-grid')),
				'standard-grid-portfolio' => array('title'   => esc_html__('Standard Grid Portfolio','bm-grid')),
				'irregular-list'          => array('title'   => esc_html__('Irregular List','bm-grid')),
				
				//Custom Grid Portfolio
				array(
					'content-id' => 'custom-grid-portfolio',
					'items'      => array(
						// Image Source
						array('title'   => esc_html__('Image Source','bm-grid'),
							  'desc'    => '',
							  'type'    => 'single-category',
							  'default' => 0,
							  'name'    => '__ux_gallery_custom_grid_image_source',
							  'style'   => 'width:50%;'),
						
						// Edit Portfolio List Layout
						array('title'   => esc_html__('Drag&Drop Builder','bm-grid'),
							  'type'    => 'custom-grid-layouts',
							  'name'    => '__ux_gallery_custom_grid_layouts',
							  'ctrl'    => '__ux_gallery_custom_grid_image_source,!0'),
								  
						// Item Style
						array('title'   => esc_html__('Item Style','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_custom_grid_item_style',
							  'default' => 'image',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Image','bm-grid'),        'image'),
								  array(esc_html__('Image + Text','bm-grid'), 'image-text')
							  )),
							  
						// Transparent for Mask
						array('title'   => esc_html__('Transparent for Mask','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => '1',
							  'name'    => '__ux_gallery_custom_grid_transparent_mask',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('100%','bm-grid'), '1'),
								  array(esc_html__('90%','bm-grid'),  '0.9'),
								  array(esc_html__('80%','bm-grid'),  '0.8')
							  ),
							  'ctrl'    => '__ux_gallery_custom_grid_item_style,image'),

						// Show Title
						array('title'   => esc_html__('Show Title','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_custom_grid_show_title',
							  'default' => 'off',
							  'ctrl'    => '__ux_gallery_custom_grid_item_style,image'),

						// Show Category
						array('title'   => esc_html__('Show Category','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_custom_grid_show_category',
							  'default' => 'off'),
								  
						// Text Align
						array('title'   => esc_html__('Text Align','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'grid-text-center',
							  'name'    => '__ux_gallery_custom_grid_text_align',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Center','bm-grid'),        'grid-text-center'),
								  array(esc_html__('Left','bm-grid'),          'grid-text-left'),
								  array(esc_html__('Right','bm-grid'),         'grid-text-right'),
								  array(esc_html__('Top Left','bm-grid'),      'grid-text-top-left'),
								  array(esc_html__('Top Center','bm-grid'),    'grid-text-top-center'),
								  array(esc_html__('Top Right','bm-grid'),     'grid-text-top-right'),
								  array(esc_html__('Bottom Left','bm-grid'),   'grid-text-bottom-left'),
								  array(esc_html__('Bottom Center','bm-grid'), 'grid-text-bottom-center'),
								  array(esc_html__('Bottom Right','bm-grid'),  'grid-text-bottom-right')
							  ),
							  'ctrl'    => '__ux_gallery_custom_grid_item_style,image'),

						// Text Align
						array('title'   => esc_html__('Text Align','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'grid-text-center',
							  'name'    => '__ux_gallery_custom_grid_text_align_for_text',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Center','bm-grid'), 'grid-text-center'),
								  array(esc_html__('Left','bm-grid'),   'grid-text-left'),
								  array(esc_html__('Right','bm-grid'),  'grid-text-right')
							  ),
							  'ctrl'    => '__ux_gallery_custom_grid_item_style,image-text'),

						// Text Padding
						array('title'   => esc_html__('Text Padding','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_custom_grid_text_padding',
							  'default' => 'off',
							  'ctrl'    => '__ux_gallery_custom_grid_item_style,image-text'),
								  
						// What Thumbnail Does
						array('title'   => esc_html__('What Thumbnail Does','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'open-item',
							  'name'    => '__ux_gallery_custom_grid_what_thumb',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Open The Post Item','bm-grid'), 'open-item'),
								  array(esc_html__('Open Lightbox','bm-grid'),      'open-featured-img'),
								  array(esc_html__('Open External Link','bm-grid'), 'open-external-link')
							  )),
								  
						// Item Spacing
						array('title'   => esc_html__('Item Spacing','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'normal',
							  'name'    => '__ux_gallery_custom_grid_spacing',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Normal','bm-grid'),     'normal'),
								  array(esc_html__('10','bm-grid'),         '10'),
								  array(esc_html__('20','bm-grid'),         '20'),
								  array(esc_html__('30','bm-grid'),         '30'),
								  array(esc_html__('40','bm-grid'),         '40'),
								  array(esc_html__('No Spacing','bm-grid'), 'no-spacing')
							  )),
								  
						// Show Filter
						array('title'   => esc_html__('Show Filter','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'no',
							  'name'    => '__ux_gallery_custom_grid_show_filter',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('No','bm-grid'),                              'no'),
								  array(esc_html__('Show Filter On Header With Menu','bm-grid'), 'on-menu'),
								  array(esc_html__('Above Gallery','bm-grid'),                   'above-gallery')
							  )),
								  
						// Filter Align
						array('title'   => esc_html__('Filter Align','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'filter-left',
							  'name'    => '__ux_gallery_custom_grid_filter_align',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Center','bm-grid'), 'filter-center'),
								  array(esc_html__('Left','bm-grid'),   'filter-left'),
								  array(esc_html__('Right','bm-grid'),  'filter-right')
							  )),

						// Show Top Spacer
						array('title'   => esc_html__('Show Top Spacer','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_custom_grid_show_top_spacer',
							  'default' => 'off')
					)
				),
				
				//Masonry Portfolio
				array(
					'content-id' => 'masonry-portfolio',
					'items'      => array(
						// Category
						array('title'   => esc_html__('Category','bm-grid'),
							  'desc'    => '',
							  'type'    => 'single-category',
							  'default' => 0,
							  'name'    => '__ux_gallery_masonry_portfolio_category',
							  'style'   => 'width:50%;'),
							  
						// Select Category Order
						array('title'   => esc_html__('Order','bm-grid'),
							  'desc'    => '',
							  'type'    => 'category-orderby',
							  'name'    => '__ux_gallery_masonry_portfolio_category_orderby',
							  'default' => 'date',
							  'style'   => 'width:30%;margin-right:15px;'),
								  
						// Columns
						array('title'   => esc_html__('Columns','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_masonry_portfolio_columns',
							  'default' => '2',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('2','bm-grid'), '2'),
								  array(esc_html__('3','bm-grid'), '3'),
								  array(esc_html__('4','bm-grid'), '4'),
								  array(esc_html__('5','bm-grid'), '5'),
								  array(esc_html__('6','bm-grid'), '6')
							  )),
								  
						// Post Number per Page
						array('title'   => esc_html__('Post Number per Page','bm-grid'),
							  'desc'    => '',
							  'type'    => 'text',
							  'default' => 10,
							  'name'    => '__ux_gallery_masonry_portfolio_number',
							  'style'   => 'width:50%;'),
							  
						// Pagination
						array('title'   => esc_html__('Pagination','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'load-more',
							  'name'    => '__ux_gallery_masonry_portfolio_pagination',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Load More Button','bm-grid'), 'load-more'),
								  array(esc_html__('Infiniti Scroll','bm-grid'),  'infiniti-scroll')
							  )),
								  
						// What Thumbnail Does
						array('title'   => esc_html__('What Thumbnail Does','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'open-item',
							  'name'    => '__ux_gallery_masonry_portfolio_what_thumb',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Open The Post Item','bm-grid'), 'open-item'),
								  array(esc_html__('Open Lightbox','bm-grid'),      'open-featured-img'),
								  array(esc_html__('Open External Link','bm-grid'), 'open-external-link')
							  )),
								  
						// Item Spacing
						array('title'   => esc_html__('Item Spacing','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'normal',
							  'name'    => '__ux_gallery_masonry_portfolio_spacing',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('No Spacing','bm-grid'), 'no-spacing'),
								  array(esc_html__('Narrow','bm-grid'),     'narrow'),
								  array(esc_html__('Normal','bm-grid'),     'normal')
							  )),
								  
						// Show Filter
						array('title'   => esc_html__('Show Filter','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'no',
							  'name'    => '__ux_gallery_masonry_portfolio_show_filter',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('No','bm-grid'),                              'no'),
								  array(esc_html__('Show Filter On Header With Menu','bm-grid'), 'on-menu'),
								  array(esc_html__('Above Gallery','bm-grid'),                   'above-gallery')
							  )),
								  
						// Filter Align
						array('title'   => esc_html__('Filter Align','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'filter-left',
							  'name'    => '__ux_gallery_masonry_portfolio_filter_align',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Center','bm-grid'), 'filter-center'),
								  array(esc_html__('Left','bm-grid'),   'filter-left'),
								  array(esc_html__('Right','bm-grid'),  'filter-right')
							  ))
					)
				),
				
				//Standard Grid Portfolio
				array(
					'content-id' => 'standard-grid-portfolio',
					'items'      => array(
						// Category
						array('title'   => esc_html__('Category','bm-grid'),
							  'desc'    => '',
							  'type'    => 'single-category',
							  'default' => 0,
							  'name'    => '__ux_gallery_standard_grid_category',
							  'style'   => 'width:50%;'),
							  
						// Select Category Order
						array('title'   => esc_html__('Order','bm-grid'),
							  'desc'    => '',
							  'type'    => 'category-orderby',
							  'name'    => '__ux_gallery_standard_grid_category_orderby',
							  'default' => 'date',
							  'style'   => 'width:30%;margin-right:15px;'),
							  
						// Show Title
						array('title'   => esc_html__('Show Title','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_standard_grid_show_title',
							  'default' => 'off'),
  
						// Text Align
						array('title'   => esc_html__('Text Align','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'standard-text-left',
							  'name'    => '__ux_gallery_standard_grid_text_align',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Center','bm-grid'), 'standard-text-center'),
								  array(esc_html__('Left','bm-grid'),   'standard-text-left'),
								  array(esc_html__('Right','bm-grid'),  'standard-text-right')
							  )),
  
						// Show Padding
						array('title'   => esc_html__('Show Padding','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_standard_grid_show_padding',
							  'default' => 'off',
							  'ctrl'    => '__ux_gallery_standard_grid_text_align,standard-text-left|standard-text-right'),
								  
						// Columns
						array('title'   => esc_html__('Columns','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_standard_grid_columns',
							  'default' => '2',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('2','bm-grid'), '2'),
								  array(esc_html__('3','bm-grid'), '3'),
								  array(esc_html__('4','bm-grid'), '4'),
								  array(esc_html__('5','bm-grid'), '5'),
								  array(esc_html__('6','bm-grid'), '6')
							  )),
								  
						// Post Number per Page
						array('title'   => esc_html__('Post Number per Page','bm-grid'),
							  'desc'    => '',
							  'type'    => 'text',
							  'default' => 10,
							  'name'    => '__ux_gallery_standard_grid_number',
							  'style'   => 'width:50%;'),
							  
						// Pagination
						array('title'   => esc_html__('Pagination','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'load-more',
							  'name'    => '__ux_gallery_standard_grid_pagination',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Load More Button','bm-grid'), 'load-more'),
								  array(esc_html__('Infiniti Scroll','bm-grid'),  'infiniti-scroll')
							  )),
								  
						// Grid Ratio
						array('title'   => esc_html__('Grid Ratio','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'name'    => '__ux_gallery_standard_grid_ratio',
							  'default' => '4_3',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('4:3','bm-grid'),  '4_3'),
								  array(esc_html__('16:9','bm-grid'), '16_9'),
								  array(esc_html__('1:1','bm-grid'),  '1_1')
							  )),
								  
						// Item Spacing
						array('title'   => esc_html__('Item Spacing','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'normal',
							  'name'    => '__ux_gallery_standard_grid_spacing',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('No Spacing','bm-grid'), 'no-spacing'),
								  array(esc_html__('Narrow','bm-grid'),     'narrow'),
								  array(esc_html__('Normal','bm-grid'),     'normal')
							  )),
								  
						// Show Filter
						array('title'   => esc_html__('Show Filter','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'no',
							  'name'    => '__ux_gallery_standard_grid_show_filter',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('No','bm-grid'),                              'no'),
								  array(esc_html__('Show Filter On Header With Menu','bm-grid'), 'on-menu'),
								  array(esc_html__('Above Gallery','bm-grid'),                   'above-gallery')
							  )),
								  
						// Filter Align
						array('title'   => esc_html__('Filter Align','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'filter-left',
							  'name'    => '__ux_gallery_standard_grid_filter_align',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Center','bm-grid'), 'filter-center'),
								  array(esc_html__('Left','bm-grid'),   'filter-left'),
								  array(esc_html__('Right','bm-grid'),  'filter-right')
							  ))
					)
				),
				
				//Irregular List
				array(
					'content-id' => 'irregular-list',
					'items'      => array(
						// Portfolio Layout Builder
						array('title'   => esc_html__('Portfolio Layout Builder','bm-grid'),
							  'desc'    => '',
							  'type'    => 'portfolio_layout_builder',
							  'default' => '',
							  'name'    => '__ux_gallery_irregular_list_layout_builder',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  'image_align' => array(
									  array(esc_html__('Image Align','bm-grid'), 0),
									  array(esc_html__('Left','bm-grid'),        'left'),
									  array(esc_html__('Center','bm-grid'),      'center'),
									  array(esc_html__('Right','bm-grid'),       'right')
								  ),
								  
								  'title_align' => array(
									  array(esc_html__('Text Align','bm-grid'),   0),
									  array(esc_html__('Top Left','bm-grid'),     'top-left'),
									  array(esc_html__('Middle Left','bm-grid'),  'middle-left'),
									  array(esc_html__('Bottom Left','bm-grid'),  'bottom-left'),
									  array(esc_html__('Top Right','bm-grid'),    'top-right'),
									  array(esc_html__('Middle Right','bm-grid'), 'middle-right'),
									  array(esc_html__('Bottom Right','bm-grid'), 'bottom-right')
								  ),
								  
								  'top_padding' => array(
									  array(esc_html__('Top Padding','bm-grid'),   0),
									  array(esc_html__('100px Spacing','bm-grid'), '100px'),
									  array(esc_html__('Overlap','bm-grid'),       'overlap')
								  ),
								  
								  'image_width' => array(
									  array(esc_html__('Image Width','bm-grid'), 0),
									  array(esc_html__('30%','bm-grid'),         '30%'),
									  array(esc_html__('40%','bm-grid'),         '40%'),
									  array(esc_html__('50%','bm-grid'),         '50%'),
									  array(esc_html__('60%','bm-grid'),         '60%'),
									  array(esc_html__('70%','bm-grid'),         '70%')
								  )
							  )),
							  
						array('type'    => 'divide'),
						
						// Category
						array('title'   => esc_html__('Category','bm-grid'),
							  'desc'    => '',
							  'type'    => 'single-category',
							  'default' => 0,
							  'name'    => '__ux_gallery_irregular_list_category',
							  'style'   => 'width:50%;'),
							  
						// Select Category Order
						array('title'   => esc_html__('Order','bm-grid'),
							  'desc'    => '',
							  'type'    => 'category-orderby',
							  'name'    => '__ux_gallery_irregular_list_category_orderby',
							  'default' => 'date',
							  'style'   => 'width:30%;margin-right:15px;'),
								  
						// Post Number per Page
						array('title'   => esc_html__('Post Number per Page','bm-grid'),
							  'desc'    => '',
							  'type'    => 'text',
							  'default' => 10,
							  'name'    => '__ux_gallery_irregular_list_number',
							  'style'   => 'width:50%;'),
							  
						// Pagination
						array('title'   => esc_html__('Pagination','bm-grid'),
							  'desc'    => '',
							  'type'    => 'select',
							  'default' => 'load-more',
							  'name'    => '__ux_gallery_irregular_list_pagination',
							  'style'   => 'width:50%;',
							  'fields'  => array(
								  array(esc_html__('Load More Button','bm-grid'), 'load-more'),
								  array(esc_html__('Infiniti Scroll','bm-grid'),  'infiniti-scroll')
							  )),

						// Show Tags
						array('title'   => esc_html__('Show Tags','bm-grid'),
							  'desc'    => '',
							  'type'    => 'switch',
							  'name'    => '__ux_gallery_irregular_list_show_tags',
							  'default' => 'off'),
					)
				),
				
				//Global Section
				array(
					'items' => array(
						// Shortcode
						array('title'   => esc_html__('Shortcode','bm-grid'),
							  'desc'    => '',
							  'type'    => 'text',
							  'default' => '[bm-grid id=""]',
							  'name'    => '__ux_gallery_shortcode',
							  'style'   => 'width:50%;'),
					)
				)
			)
		)
	);
	
	return $config;
}

?>