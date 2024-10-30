(function($){

    "use strict";
	
	var UxGallery               = [];
	
	//window
	UxGallery.win               = $(window);
	UxGallery.winHeight         = UxGallery.win.height();
	UxGallery.winScrollTop      = UxGallery.win.scrollTop();
	
	//document
	UxGallery.doc               = $(document);
	
	//selector
	UxGallery.sectionCuslList   = $('.cusl-style-list');
	UxGallery.isotope           = $('.container-masonry');
	UxGallery.photoSwipe        = $('.lightbox-photoswipe');
	UxGallery.gridStack         = $('.grid-stack');
	
	UxGallery.itemQueue         = [];
	UxGallery.itemDelay         = 150;
	UxGallery.queueTimer;
	
	//List Item Queue
	UxGallery.fnListItemQueue = function() {
		if (UxGallery.queueTimer) return  
		UxGallery.queueTimer = window.setInterval(function () {
			if (UxGallery.itemQueue.length) {
				$(UxGallery.itemQueue.shift()).addClass('grid-show');
				UxGallery.fnListItemQueue();
			}
			else {
				window.clearInterval(UxGallery.queueTimer);
				UxGallery.queueTimer = null;
			}
		}, UxGallery.itemDelay);
	}
	
	//Irregular Hover
	UxGallery.fnIrregularHover = function(selector){
		selector.on('mousemove', function(e){
			var elPos = $(this).offset(),
				cursPosX = e.pageX - elPos.left,
				cursPosY = e.pageY - elPos.top,
				elWidth = $(this).width(),
				elHeight = $(this).height(),
				elHalfWidth = elWidth / 2,
				elHalfHeight = elHeight / 2,
				cursFromCenterX = elHalfWidth - cursPosX,
				cursFromCenterY = elHalfHeight - cursPosY;
			var reflectPercent = (cursPosX + cursPosY) / (elWidth + elHeight) * 100;
			$(this).css('transform','perspective(1700px) rotateX('+ (cursFromCenterY / 20) +'deg) rotateY('+ -(cursFromCenterX / 20) +'deg)');
			//$('.reflect').css('transform', 'scale('+ reflectPercent / 40 +')')
			$(this).removeClass('leave');
		});

		selector.on('mouseleave', function(){
			//$('.reflect').css('transform', 'scale(1)')
			$(this).addClass('leave');
		});
	}
	
	//Isotope List
	UxGallery.isotopeList = function(){
		UxGallery.isotope.each(function(){
			var _this_wrap 	  = $(this),
				_this 		  = $(this).find('.masonry-list'),
				_this_wrap_st = $(this).scrollTop(),
				_this_wrap_pt = $(this).attr('data-template');

			if(_this.hasClass('grid-list')) {

				var $iso_list =  _this.isotope({
					itemSelector: '.grid-item',
					layoutMode: 'fitRows',
					stagger: 40,
					hiddenStyle: {
					  opacity: 0
					},
					visibleStyle: {
					  opacity: 1
					}
				});

			} else { 
				var $iso_list =  _this.isotope({ 
					itemSelector: '.grid-item',
					layoutMode: 'packery',
					stagger: 40,
					hiddenStyle: {
					  opacity: 0
					},
					visibleStyle: {
					  opacity: 1
					}
				}); 
			}
			
			
			//filter
			var filters = _this_wrap.find('.filters');
			if($('.menu-filter-wrap').length){ filters = $('.menu-filter-wrap'); }
			if($('.menu-filter').length){ filters = $('.menu-filter'); }
			
			if(filters.length){
				var filter = filters.find('[data-filter]');
				
				filter.click(function(){
					var filterValue = $( this ).attr('data-filter');
					var filterClick = $( this );
					
					var galleryID = $(this).attr('data-galleryid');
					var postFound = Number(_this_wrap.attr('data-found'));
					var postCount = Number($(this).find('.filter-num').text());
					var postNumber = Number(_this_wrap.attr('data-number'));
					var cat_id = 0;
					var post__not_in = [];
					
					/*if(_this.hasClass('infiniti-scroll')){
						_this.addClass('infiniti-scrolling');
					}*/
					
					$iso_list.isotope({ filter: filterValue }); 
					filters.find('li').removeClass('active');
					$(this).parent().addClass('active');
					
					if(filterValue != '*'){
						cat_id = $(this).attr('data-catid');
						postFound = postCount;
					}
					
					$iso_list.find('section').each(function(){
						var section_postid = $(this).attr('data-postid');
						var filterActive = filters.find('li.active');
						var filterValue = filterActive.find('> a').attr('data-filter');
						
						cat_id = filterActive.find('> a').attr('data-catid');
						var postCount = Number(filterActive.find('.filter-num').text());
						
						if(filterValue == '*'){
							post__not_in.push(section_postid);
						}else{
							if($(this).is(filterValue)){
								post__not_in.push(section_postid);
								if(!$(this).find('.ux-lazyload-img').hasClass('lazy-loaded')){
									$(this).find('.ux-lazyload-img').addClass('lazy-loaded');
									$(this).find('.ux-lazyload-img').attr('src', $(this).find('.ux-lazyload-img').attr('data-src'));
								}
								UxGallery.itemQueue.push($(this).find('.grid-item-inside'));
								UxGallery.fnListItemQueue();
							}
						}
					});
					
					var isotopeLoadMore = _this_wrap.find('.ux-gallery-load-more');
					if(post__not_in.length >= postFound){
						isotopeLoadMore.hide();
					}else{
						isotopeLoadMore.show();
					}
					
					if(post__not_in.length < postFound){
						postNumber = postFound - post__not_in.length;
						$.post(ajaxurl, {
							'action': 'bm_grid_masonry_list',
							'cat_id': cat_id,
							'gallery_id': galleryID,
							'post__not_in': post__not_in,
							'postNumber': postNumber
						}).done(function(result){
							var content = $(result);
							$iso_list.isotope('insert', content);
							if(_this.hasClass('masonry-grid')) {
								UxGallery.isotopeListResize(_this, _this_wrap);
								
								_this.isotope('layout');
							}
							
							if(_this.hasClass('infiniti-scroll')){
								_this.removeClass('infiniti-scrolling');
							}else{
								var thisPostCount = $iso_list.find('section' +filterValue).length;
								if(thisPostCount >= postFound){
									isotopeLoadMore.hide();
								}else{
									isotopeLoadMore.show();
								}
							}
							
							setTimeout(function() {
								$(window).lazyLoadXT(); 
								content.find('.grid-item-inside').each(function(){
									UxGallery.itemQueue.push($(this));
									UxGallery.fnListItemQueue();
								});
							}, 10);
						});
					}else{
						_this.removeClass('infiniti-scrolling');
					}
					
					return false;
				});
			}
			
			//call page load more
			var isotopeLoadMore = _this_wrap.find('.ux-gallery-load-more');
			if(isotopeLoadMore.length){
				var loadBtn = isotopeLoadMore.find('.ux-page-load-more');

				loadBtn.click(function(){
					var galleryID = loadBtn.attr('data-galleryid');
					var pagedMAX = Number(loadBtn.attr('data-max'));
					var paged = Number(loadBtn.attr('data-paged'));
					var pageText = loadBtn.parent().attr('data-pagetext');
					var pageLoadingText = loadBtn.parent().attr('data-loadingtext');
					var cat_id = 0;
					var post__not_in = [];
					var post_found = Number(_this_wrap.attr('data-found'));
					
					$iso_list.find('section').each(function(){
						var section_postid = $(this).attr('data-postid');
						if(filters.length){
							var filterActive = filters.find('li.active');
							var filterValue = filterActive.find('> a').attr('data-filter');
							
							cat_id = filterActive.find('> a').attr('data-catid');
							var postCount = Number(filterActive.find('.filter-num').text());
							
							if(filterValue == '*'){
								post__not_in.push(section_postid);
							}else{
								if($(this).is(filterValue)){
									post__not_in.push(section_postid);
								}
							}
						}else{
							post__not_in.push(section_postid);
						}
					});
					
					loadBtn.text(pageLoadingText);

					if(!_this.hasClass('loading-more')){
						_this.addClass('loading-more');
						$.post(ajaxurl, {
							'action': 'bm_grid_masonry_list',
							'cat_id': cat_id,
							'gallery_id': galleryID,
							'post__not_in': post__not_in,
							'paged': paged
						}).done(function(result){
							var content = $(result);
							$iso_list.isotope('insert', content);
							if(_this.hasClass('masonry-grid')) {
								UxGallery.isotopeListResize(_this, _this_wrap);
								
								_this.isotope('layout');
							}
							
							loadBtn.text(pageText);
							_this.removeClass('loading-more');
							
							var thisPostCount = $iso_list.find('section').length;
							if(filters.length){
								var filterActive = filters.find('li.active');
								var filterValue = filterActive.find('> a').attr('data-filter');
								var postCount = Number(filterActive.find('.filter-num').text());
								if(filterValue != '*'){
									thisPostCount = $iso_list.find('section' +filterValue).length;
									post_found = postCount;
								}
							}
							
							if(thisPostCount >= post_found){
								loadBtn.parent().hide();
							}else{
								loadBtn.parent().show();
							}
							
							setTimeout(function() {
								$(window).lazyLoadXT(); 
								content.find('.grid-item-inside').each(function(){
									UxGallery.itemQueue.push($(this));
									UxGallery.fnListItemQueue();
								});
							}, 10);
						});
					}
					
					return false;
				});
			}
			
			//infiniti scroll
			if(_this.hasClass('infiniti-scroll')){
				UxGallery.fnInfinitiScroll(_this);
			}
			
			if(_this.hasClass('masonry-grid')) {
				UxGallery.win.on( 'resize', function () {
					UxGallery.isotopeListResize(_this, _this_wrap);
				}).resize();
				//$("#all").trigger('click'); 
				$iso_list.isotope('layout');
			}
			
			
		});
	}
	
	//Isotope List Resize
	UxGallery.isotopeListResize = function(_this, _this_wrap){
		var winWidth   = window.innerWidth,
			ListWidth  = _this.width(),
			GridSpacer = _this_wrap.data('spacer'),
			columnNumb = _this_wrap.data('col'),
			GridWith   = Math.floor(ListWidth / columnNumb),
			GridRatio  = _this_wrap.data('ratio'),
			GridText   = _this_wrap.data('text');  

		if (winWidth >= 768) { 

			_this.find('.grid-item').each(function () { 
				$('.grid-item').css({ 
					width : GridWith * 1 - GridSpacer + 'px',
					height : GridWith * GridRatio - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio - GridSpacer)/(GridWith * 1 - GridSpacer)) * 100 + '%'
				); 
				$('.grid-item.grid-item-big').css({ 
					width : GridWith * 2 - GridSpacer + 'px',
					height : GridWith * GridRatio * 2 - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item.grid-item-big .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio * 2 - GridSpacer)/(GridWith * 2 - GridSpacer)) * 100 + '%'
				); 
				$('.grid-item.grid-item-long').css({ 
					width : GridWith * 2 - GridSpacer + 'px',
					height : GridWith * GridRatio - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item.grid-item-long .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio - GridSpacer)/(GridWith * 2 - GridSpacer)) * 100 + '%'
				); 
				$('.grid-item.grid-item-tall').css({ 
					width : GridWith * 1 - GridSpacer + 'px',
					height : GridWith * GridRatio * 2 - GridSpacer + GridText * 2  + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item.grid-item-tall .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio * 2 - GridSpacer + GridText)/(GridWith * 1 - GridSpacer)) * 100 + '%'
				); 
			});

		} else {
			
			GridWith = Math.floor(ListWidth / 1);

			_this.find('.grid-item.grid-item-small').each(function () { 
				$('.grid-item').css({ 
					width : GridWith * 1 - GridSpacer + 'px',
					height : GridWith * GridRatio - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio - GridSpacer)/(GridWith * 1 - GridSpacer)) * 100 + '%'
				); 
				$('.grid-item.grid-item-big').css({ 
					width : GridWith * 1 - GridSpacer + 'px',
					height : GridWith * GridRatio - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item.grid-item-big .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio - GridSpacer)/(GridWith * 1 - GridSpacer)) * 100 + '%'
				); 
				$('.grid-item.grid-item-long').css({ 
					width : GridWith * 1 - GridSpacer + 'px',
					height : GridWith * GridRatio * 0.5 - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item.grid-item-long .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio - GridSpacer)/(GridWith * 2 - GridSpacer)) * 100 + '%'
				); 
				$('.grid-item.gird-item-tall').css({ 
					width : GridWith * 1 - GridSpacer + 'px',
					height : GridWith * GridRatio * 2 - GridSpacer + GridText + 'px',
					margin : GridSpacer * 0.5 + 'px' 
				});
				$('.grid-item.gird-item-tall .ux-lazyload-wrap').css(
					"padding-top", ((GridWith * GridRatio * 2 - GridSpacer)/(GridWith * 1 - GridSpacer)) * 100 + '%'
				); 

			});	
		}
	}
	
	//Infiniti Scroll
	UxGallery.fnInfinitiScroll = function(selector){
		var waypoints = selector.find('section:last').waypoint({
			handler: function(direction){
				var galleryID = selector.attr('data-galleryid');
				var pagedMAX = Number(selector.attr('data-max'));
				var paged = Number(selector.attr('data-paged'));
				var _this_wrap = selector.parent();
				
				if(selector.hasClass('masonry-list')){
					var filters = _this_wrap.find('.filters');
					var cat_id = 0;
					var post__not_in = [];
					var post_found = Number(_this_wrap.attr('data-found'));
					
					if($('.menu-filter-wrap').length){
						filters = $('.menu-filter-wrap');
					}
					
					if($('.menu-filter').length){
						filters = $('.menu-filter');
					}
					
					selector.find('section').each(function(){
						var section_postid = $(this).attr('data-postid');
						if(filters.length){
							var filterActive = filters.find('li.active');
							var filterValue = filterActive.find('> a').attr('data-filter');
							
							cat_id = filterActive.find('> a').attr('data-catid');
							var postCount = Number(filterActive.find('.filter-num').text());
							
							if(filterValue == '*'){
								cat_id = 0;
								post__not_in.push(section_postid);
							}else{
								if($(this).is(filterValue)){
									post__not_in.push(section_postid);
								}
							}
						}else{
							post__not_in.push(section_postid);
						}
					});
				}
				
				if(!selector.hasClass('infiniti-scrolling')){
					selector.addClass('infiniti-scrolling');
					
					if(selector.hasClass('masonry-list')){
						$.post(ajaxurl, {
							'action': 'bm_grid_masonry_list',
							'cat_id': cat_id,
							'gallery_id': galleryID,
							'post__not_in': post__not_in,
							'paged': paged
						}).done(function(result){
							var content = $(result);
							selector.isotope('insert', content);
							if(selector.hasClass('masonry-grid')) {
								UxGallery.isotopeListResize(selector, selector.parent('.container-masonry'));
								
								selector.isotope('layout');
							}
							
							var thisPostCount = selector.find('section').length;
							
							if(thisPostCount < post_found){
								selector.removeClass('infiniti-scrolling');
							}
							
							UxGallery.fnInfinitiScroll(selector);
							
							setTimeout(function() {
								$(window).lazyLoadXT(); 
								content.find('.grid-item-inside').each(function(){
									UxGallery.itemQueue.push($(this));
									UxGallery.fnListItemQueue();
								});
							}, 10);
						});
					}else{
						if(pagedMAX >= paged){
							$.post(ajaxurl, {
								'action': 'bm_grid_irregular_list',
								'gallery_id': galleryID,
								'paged': paged
							}).done(function(result){
								var content = $(result);
								selector.find('section:last').after(content);
										
								selector.attr('data-paged', paged + 1);
								selector.removeClass('infiniti-scrolling');
								
								UxGallery.fnInfinitiScroll(selector);
								
								setTimeout(function() {
									$(window).lazyLoadXT();
									selector.find('section').each(function(){
										if(!$(this).hasClass('cusl-show')){
											$(this).addClass('cusl-show')
										}
									});
									
									UxGallery.fnIrregularHover(content.find('.cusl-style-unit-inn')); 
								}, 50);
							});
						}
					}
				}
			},
			offset: 'bottom-in-view'
		});
	}
	
	//Grid Stack Init Size
	UxGallery.fnGridStackInitSize = function(items){
		items.each(function(){
			var gs_x = Number($(this).attr('data-gs-x'));
			var gs_y = Number($(this).attr('data-gs-y'));
			var gs_width = Number($(this).attr('data-gs-width'));
			var gs_height = Number($(this).attr('data-gs-height'));
			
			$(this).attr({
				'data-o-x': gs_x,
				'data-o-y': gs_y,
				'data-o-width': gs_width,
				'data-o-height': gs_height
			});
		});
	}
	
	//Grid Stack Resize
    UxGallery.fnGridStackResize = function(gridStack){
		var gridStackWidth = gridStack.width(); 
		var gridStackSpacing = gridStack.data('spacing');
		var gridStackColWidth = (gridStackWidth + gridStackSpacing) / 12;
		var gridStackOffsetTop = gridStack.offset().top;
		var gridOffsetTop = [];
		
		gridStack.find('.grid-stack-item').each(function(){
			var gs_x = Number($(this).attr('data-gs-x'));
			var gs_y = Number($(this).attr('data-gs-y'));
			var gs_width = Number($(this).attr('data-gs-width'));
			var gs_height = Number($(this).attr('data-gs-height'));
			
			var set_height = gridStackColWidth * gs_height;
			var set_top = gridStackColWidth * gs_y;
			
			var gs_content = $(this).find('.grid-stack-item-content');
			var gs_brick_content = $(this).find('.brick-content');
			
			$(this).css({
				width: gridStackColWidth * gs_width + 'px',
				height: set_height + 'px',
				left: gridStackColWidth * gs_x + 'px',
				top: set_top + 'px'
			});
			
			gs_content.css({
				left: gridStackSpacing * 0.5 + 'px',
				right: gridStackSpacing * 0.5 + 'px',
				top: gridStackSpacing * 0.5 + 'px',
				bottom: gridStackSpacing * 0.5 + 'px'
			});
			
			if(gs_content.height() > 0 && gs_content.width() > 0){
				gs_brick_content.css('padding-top', (gs_content.height() / gs_content.width()) * 100 + '%');
			}
			
			gridOffsetTop.push(set_top + $(this).height());
			
			// if(!$(this).find(".grid-item-inside").hasClass('grid-show')){
			// 	$(this).find(".grid-item-inside").addClass('grid-show');
			// }
		});
		
		var gridStackHeight = Math.max.apply(Math,gridOffsetTop);
		gridStack.height(gridStackHeight);
		
		
		if(UxGallery.win.width() <= 768){
			gridStack.addClass('grid-stack-one-column-mode');
		}else{
			gridStack.removeClass('grid-stack-one-column-mode');
		}
	}
	
	//document ready
	UxGallery.doc.ready(function(){
		if(UxGallery.sectionCuslList.length){
			$.extend($.lazyLoadXT, {
				edgeY:  700 
			}); 
		} 
		
		//Call Irregular Hover
		if(UxGallery.sectionCuslList.length){
			UxGallery.sectionCuslList.each(function(){
				var cuslList = $(this);
				var cuslListLoadMore = cuslList.next('.ux-gallery-load-more')
				
                UxGallery.fnIrregularHover(cuslList.find('.cusl-style-unit-inn'));
				
				//Irregular Load more
				if(cuslListLoadMore.length){
					var loadBtn = cuslListLoadMore.find('.ux-page-load-more');
	
					loadBtn.click(function(){
						var galleryID = loadBtn.attr('data-galleryid');
						var pagedMAX = Number(loadBtn.attr('data-max'));
						var paged = Number(loadBtn.attr('data-paged'));
						var pageText = loadBtn.parent().attr('data-pagetext');
						var pageLoadingText = loadBtn.parent().attr('data-loadingtext');
						
						loadBtn.text(pageLoadingText);
	
						if(!cuslList.hasClass('loading-more')){
							cuslList.addClass('loading-more');
							$.post(ajaxurl, {
								'action': 'bm_grid_irregular_list',
								'gallery_id': galleryID,
								'paged': paged
							}).done(function(result){
								var content = $(result);
								cuslList.find('section:last').after(content);
								
								if(paged >= pagedMAX){
									loadBtn.hide();
								}
								
								loadBtn.text(pageText).attr('data-paged', paged + 1);
								cuslList.removeClass('loading-more');
								
								setTimeout(function() {
									$(window).lazyLoadXT();
									cuslList.find('section').each(function(){
										if(!$(this).hasClass('cusl-show')){
											$(this).addClass('cusl-show')
										}
									});
	
									UxGallery.fnIrregularHover(content.find('.cusl-style-unit-inn'));
	
								}, 50);
							});
						}
						
						return false;
					});
				}
				
				//Irregular List Infiniti Scroll
				if(cuslList.hasClass('infiniti-scroll')){
					UxGallery.fnInfinitiScroll(cuslList);
				}
            });
		}
		
		//Call Isotope
		if(UxGallery.isotope.length){
			UxGallery.isotopeList();
			$('.masonry-list').isotope('on', 'layoutComplete', function() {
	            $(window).trigger('layoutComplete');
	        });
		}
	});
	
	//win load
	UxGallery.win.load(function(){
		// Irregular List
		if(UxGallery.sectionCuslList.length) {
			UxGallery.sectionCuslList.each(function(){
				var cuslList = $(this);
				
				cuslList.imagesLoaded(function(){
					cuslList.find('.cusl-style-unit').each(function(){
						var thisUnit = $(this);
						thisUnit.waypoint(function(direction) { 
							if (direction === 'down') { 
								thisUnit.addClass('cusl-show');
							}
							this.destroy();
						},{
							offset: '100%'
						});
					});
				});
			});
    	}
		
		//Call Lightbox 
		if(UxGallery.photoSwipe.length){
			fnInitPhotoSwipeFromDOM('.lightbox-photoswipe');
		}
		
		//grid Stack
		if(UxGallery.gridStack.length){
			UxGallery.gridStack.each(function(){
				var gridStack = $(this);
                var gridStackSpacing = gridStack.data('spacing');
				
				gridStack.css('margin', - gridStackSpacing * 0.5 + 'px');
				
				UxGallery.fnGridStackInitSize(gridStack.find('.grid-stack-item'));
				
				/*var gridStack = $(this).gridstack({
					verticalMargin: gridStackSpacing,
					disableDrag: true,
					draggable: {disabled: true},
					disableResize: true
				});*/
				
				var isoGridStack = gridStack.isotope({ 
					itemSelector: '.grid-stack-item',
					layoutMode: 'packery',
					stagger: 40,
					resize: false
				});
				
				UxGallery.fnGridStackResize(gridStack);
				
				UxGallery.win.resize(function(){
					var filters = $('.filters');
						
					if($('.menu-filter-wrap').length) { filters = $('.menu-filter-wrap'); }
					if($('.menu-filter').length) { filters = $('.menu-filter'); }
					
					var filterActive = filters.find('li.active');
					var filterValue = filterActive.find('> a').attr('data-filter');
					
					if(filterValue){
						if(filterValue != '*'){
							isoGridStack.isotope('layout');
						}else{
							UxGallery.fnGridStackResize(gridStack);
						}
					}else{
						UxGallery.fnGridStackResize(gridStack);
					}
				});
				
				var grid = gridStack.data('gridstack');
				
				var filterHidden = false;
				if($('.filters').length || $('.menu-filter-wrap').length || $('.menu-filter').length){
					var _filters = $('.filters [data-filter]');
					
					if($('.menu-filter-wrap').length){
						_filters = $('.menu-filter-wrap [data-filter]');
					}
					
					if($('.menu-filter').length){
						_filters = $('.menu-filter [data-filter]');
					}
					
					_filters.click(function(){
						var filterValue = $(this).attr('data-filter');
						var filterCatID = $(this).attr('data-catid');
						var filterItems = [];
						var filterCount = Number($(this).find('.filter-num').text());
						var post__not_in = [];
						var postCount = 0;
						
						$(this).parent().parent().find('li').removeClass('active');
						$(this).parent().addClass('active');
						
						if(filterValue == '*'){
							filterCatID = 0;
							filterHidden = gridStack.find('.grid-stack-item:hidden');
							UxGallery.fnGridStackResize(gridStack);
							filterHidden.show();
						}else{
							if(filterHidden){
								filterHidden.hide();
							}
							
							isoGridStack.isotope({ filter: filterValue });
						}
						return false;
					});
				}
            });
		}
		
		if($(".grid-item-inside").length) {
			$(".grid-item-inside").waypoint(function (direction) {
				UxGallery.itemQueue.push(this.element);
				UxGallery.fnListItemQueue();
			}, {
				offset: '100%'
			});
			$('.grid-item-inside').each(function(index, element) {
				if($(this).parent().offset().top < UxGallery.winScrollTop + UxGallery.winHeight){
					var lazyload = $(this).find('.ux-lazyload-bgimg');
					var lazyload_bgimg = lazyload.data('bg');
					if(lazyload_bgimg) {
						lazyload.addClass('lazy-loaded').css('background-image', 'url("' +lazyload_bgimg+ '")');
					}
					
					UxGallery.itemQueue.push($(this));
					UxGallery.fnListItemQueue();
				}
            });
		}
	});
	
})(jQuery);

jQuery.extend(jQuery.lazyLoadXT, {
	edgeY:  200 
}); 

function fnInitPhotoSwipeFromDOM(gallerySelector){
    var parseThumbnailElements = function(el){
		var thumbElements = jQuery(el).find('[data-lightbox=\"true\"]'),
			numNodes = thumbElements.length,
			items = [],
			figureEl,
			linkEl,
			size,
			type,
			item;

		for(var i = 0; i < numNodes; i++){

			figureEl = thumbElements[i]; // <figure> element

			// include only element nodes 
			if(figureEl.nodeType !== 1){
				continue;
			}

			//linkEl = figureEl.children[0]; // <a> element
			linkEl = jQuery(figureEl).find('.lightbox-item');

			size = linkEl.attr('data-size').split('x');
			type = linkEl.attr('data-type');

			// create slide object
			if(type == 'video'){
				item = {
					html: linkEl.find('> div').html()
				}
			}else{
				item = {
					src: linkEl.attr('href'),
					w: parseInt(size[0], 10),
					h: parseInt(size[1], 10)
				};
			}

			if(figureEl.children.length > 0){
				// <figcaption> content
				item.title = linkEl.attr('title'); 
			}

			if(linkEl.find('img').length > 0){
				// <img> thumbnail element, retrieving thumbnail url
				item.msrc = linkEl.find('img').attr('src');
			} 

			item.el = figureEl; // save link to element for getThumbBoundsFn
			items.push(item);
		}

		return items;
	};

	// find nearest parent element
	var closest = function closest(el, fn){
		return el && (fn(el) ? el : closest(el.parentNode, fn));
	};

	// triggers when user clicks on thumbnail
	var onThumbnailsClick = function(e){
		e = e || window.event;
		e.preventDefault ? e.preventDefault() : e.returnValue = false;

		var eTarget = e.target || e.srcElement;

		// find root element of slide
		var clickedListItem = closest(eTarget, function(el){
			if(el.tagName){
				return (el.hasAttribute('data-lightbox') && el.getAttribute('data-lightbox') === 'true'); 
			}
		});

		if(!clickedListItem){
			return;
		}

		// find index of clicked item by looping through all child nodes
		// alternatively, you may define index via data- attribute
		var clickedGallery = jQuery(clickedListItem).parents('.lightbox-photoswipe'),
			childNodes = clickedGallery.find('[data-lightbox=\"true\"]'),
			numChildNodes = childNodes.length,
			nodeIndex = 0,
			index;

		for (var i = 0; i < numChildNodes; i++){
			if(childNodes[i].nodeType !== 1){ 
				continue; 
			}

			if(childNodes[i] === clickedListItem){
				index = nodeIndex;
				break;
			}
			nodeIndex++;
		}
		
		if(index >= 0){
			// open PhotoSwipe if valid index found
			openPhotoSwipe(index, clickedGallery[0]);
		}
		return false;
	};

	// parse picture index and gallery index from URL (#&pid=1&gid=2)
	var photoswipeParseHash = function(){
		var hash = window.location.hash.substring(1),
		params = {};

		if(hash.length < 5) {
			return params;
		}

		var vars = hash.split('&');
		for (var i = 0; i < vars.length; i++) {
			if(!vars[i]) {
				continue;
			}
			var pair = vars[i].split('=');  
			if(pair.length < 2) {
				continue;
			}           
			params[pair[0]] = pair[1];
		}

		if(params.gid) {
			params.gid = parseInt(params.gid, 10);
		}

		if(!params.hasOwnProperty('pid')) {
			return params;
		}
		params.pid = parseInt(params.pid, 10);
		return params;
	};

	var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL){
		var pswpElement = document.querySelectorAll('.pswp')[0],
			gallery,
			options,
			items;

		items = parseThumbnailElements(galleryElement);

		// define options (if needed)
		options = {
			index: index,

			// define gallery index (for URL)
			galleryUID: galleryElement.getAttribute('data-pswp-uid'),

			showHideOpacity:true,

			getThumbBoundsFn: function(index) {
				// See Options -> getThumbBoundsFn section of documentation for more info
				var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
					pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
					rect = thumbnail.getBoundingClientRect(); 

				return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
			},
			
			addCaptionHTMLFn: function(item, captionEl, isFake) {
				if(!item.title) {
					captionEl.children[0].innerText = '';
					return false;
				}
				captionEl.children[0].innerHTML = item.title;
				return true;
			},
			
			getImageURLForShare: function( shareButtonData ) { 
				return items[index].src || '';
			},
			
			getPageURLForShare: function( shareButtonData ) {
				return items[index].src || '';
			},
			
			getTextForShare: function( shareButtonData ) {
				return items[index].title || '';
			},
			
			// Parse output of share links
			parseShareButtonOut: function(shareButtonData, shareButtonOut) { 
				return shareButtonOut;
			}
		};
        
        if(fromURL) {
            if(options.galleryPIDs) {
                // parse real index when custom PIDs are used 
                // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                for(var j = 0; j < items.length; j++) {
                    if(items[j].pid == index) {
                        options.index = j;
                        break;
                    }
                }
            } else {
                options.index = parseInt(index, 10) - 1;
            }
        } else {
            options.index = parseInt(index, 10);
        }

        // exit if index not found
        if( isNaN(options.index) ) {
            return;
        }

        var radios = document.getElementsByName('gallery-style');
        for (var i = 0, length = radios.length; i < length; i++) {
            if (radios[i].checked) {
                if(radios[i].id == 'radio-all-controls') {

                } else if(radios[i].id == 'radio-minimal-black') {
                    options.mainClass = 'pswp--minimal--dark';
                    options.barsSize = {top:0,bottom:0};
                    options.captionEl = false;
                    options.fullscreenEl = false;
                    options.shareEl = false;
                    options.bgOpacity = 0.85;
                    options.tapToClose = true;
                    options.tapToToggleControls = false;
                }
                break;
            }
        }

		if(disableAnimation) {
			options.showAnimationDuration = 0;
		}

		// Pass data to PhotoSwipe and initialize it
		gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
		gallery.init();
	};

	// loop through all gallery elements and bind events
	var galleryElements = document.querySelectorAll(gallerySelector);
	
	for(var i = 0, l = galleryElements.length; i < l; i++){
		galleryElements[i].setAttribute('data-pswp-uid', i+1);
		galleryElements[i].onclick = onThumbnailsClick;
	}

	// Parse URL and open gallery if it contains #&pid=3&gid=1
	var hashData = photoswipeParseHash();
	if(hashData.pid > 0 && hashData.gid > 0) {
		openPhotoSwipe( hashData.pid - 1 ,  galleryElements[ hashData.gid - 1 ], true, true );
	}
}