(function($){

    "use strict";
	
	var UxGallery               = [];
	var UxGalleryItemRow        = [];
	
	//window
	UxGallery.win               = $(window);
	
	//document
	UxGallery.doc               = $(document);
	
	//selector
	UxGallery.body              = $('body');
	UxGallery.selectControl     = $('select.__ux-gallery-select, button.__ux-gallery-switch');
	UxGallery.itemRow           = $('.__ux_gallery_item_row');
	UxGallery.saveButton        = $('.__ux_gallery_save_button');
	UxGallery.sectionTab        = $('.__ux_gallery_section_tab');
	UxGallery.customGridLayouts = $('#__ux_gallery_custom_grid_layouts');
	UxGallery.portfolioLayout   = $('.__ux-gallery-portfolio-layout-builder-wrap');
	UxGallery.modal             = $('.__ux_gallery_meta_box .modal');
	UxGallery.shortcode         = $('#__ux_gallery_shortcode');
	
	//select control class
	function uxClassSelectControl(selector, tagName){
		var itemRow = selector.parents('.__ux_gallery_item_row');
		
		switch(tagName){
			case 'SELECT':
				selector.change(function(){
					var thisName = $(this).attr('name');
					var thisValue = $(this).val();
					var thisControlContainer = $('tr[data-name="' +thisName+ '"]');
					
					if(thisControlContainer.length){
						thisControlContainer.each(function(){
							if($(this).is('.control-' +thisValue)){
								$(this).show();
							}else{
								$(this).hide();
							}
						});
					}
				});
			break;
			
			case 'BUTTON':
				selector.click(function(){
					var thisTextOn = $(this).data('text-on');
					var thisTextOff = $(this).data('text-off');
					var thisInput = $(this).next('input');
					var thisName = thisInput.attr('name');
					var thisControlContainer = $('tr[data-name="' +thisName+ '"]');
					var thisValue;
					
					switch(thisName){
						//insert shortcode
						case '__ux_gallery_insert_shortcode':
							var shortcodeSelector = $('#__ux_gallery_shortcode_selector');
							var shortcode = '[bm-grid id="' +shortcodeSelector.val()+ '"]';
							
							if(shortcodeSelector.val() != '0'){
								$('#content_ifr').contents().find('body').append(shortcode);
								$('#content').append(shortcode);
							}
						break;
						
						default:
							if($(this).hasClass('on')){
								$(this).removeClass('on').addClass('off').text(thisTextOff);
								thisValue = 'off';
							}else{
								$(this).removeClass('off').addClass('on').text(thisTextOn);
								thisValue = 'on';
							}
							thisInput.val(thisValue);
							
							if(thisControlContainer.length){
								thisControlContainer.each(function(){
									 if($(this).is('.control-' +thisValue)){
										 $(this).show();
									 }else{
										 $(this).hide();
									 }
								});
							}
						break;
					}
				});
			break;
		}
		
		return selector;
	}
	
	//items control class
	function uxClassItemsControl(selector){
		var items = [];
		
		items.rowSelector = selector.find('.layout-row');
		
		selector.sortable();
		
		items.add = function(el){
			el.click(function(){
				var rowLast = selector.find('.layout-row:last');
				var rowLastClone = rowLast.clone();
				var rowLastCloneBtnAdd = rowLastClone.find('.layout-add');
				var rowLastCloneBtnRemove = rowLastClone.find('.layout-remove');
				
				rowLastClone.find('select').val(0);
				items.add(rowLastCloneBtnAdd);
				items.remove(rowLastCloneBtnRemove, rowLastClone);
				
				selector.append(rowLastClone);
			});
		}
		
		items.remove = function(el, row){
			el.click(function(){
				var rowLength = selector.find('.layout-row').length;
				
				if(rowLength > 1){
					row.remove();
				}else{
					row.find('select').val(0);
				}
			});
		}
		
		items.rowSelector.each(function(){
			var btnAdd = $(this).find('.layout-add');
			var btnRemove = $(this).find('.layout-remove');
			
			items.add(btnAdd);
			items.remove(btnRemove, $(this));
		});
	}
	
	//document ready
	UxGallery.doc.ready(function(){
		if(UxGallery.itemRow.length){
			UxGallery.itemRow.each(function(){
                var thisRow = $(this);
				var thisCtrl = $(this).data('ctrl');
				
				if(thisCtrl){
					thisCtrl = thisCtrl.split(',');
					thisRow.attr('data-name', thisCtrl[0]);
					
					if(thisCtrl[1].indexOf("!")){
						thisCtrl = thisCtrl[1].split('|');
						$.each(thisCtrl, function(index, val){
							thisRow.addClass('control-' +val);
						});
					}else{
						thisCtrl[1] = thisCtrl[1].replace('!', '');
						
						var selectCtrl = $('#' +thisCtrl[0]);
						var selectTagName = selectCtrl[0].tagName;
						
						if(selectCtrl.val() != thisCtrl[1]){
							thisRow.show();
						}else{
							thisRow.hide();
						}
						
						switch(selectTagName){
							case 'SELECT':
								selectCtrl.find('option').each(function(){
									if($(this).val() != thisCtrl[1]){
										thisRow.addClass('control-' +$(this).val());
									}
								});
							break;
						}
					}
				}
            });
		}
		
		//select, switch
		if(UxGallery.selectControl.length){
			UxGallery.selectControl.each(function(){
				var selector = $(this);
				var tagName = selector[0].tagName;
                var selectCtrl = new uxClassSelectControl(selector, tagName);
            });
		}
		
		//save option
		if(UxGallery.saveButton.length){
			UxGallery.saveButton.click(function(){
				var saveButton = $(this);
				var textSave = saveButton.data('save');
				var textSaved = saveButton.data('saved');
				var textSaving = saveButton.data('saving');
				var formData = $('.__ux_theme_wrap [name]').serializeArray();
				
				saveButton.text(textSaving).attr('disabled', 'disabled');
				$.post(ajaxurl, {
					'action': 'bm_grid_ajax_options',
					'formData': formData
				}).done(function(result){
					if(result == 'ok'){
						saveButton.text(textSaved);
						setTimeout(function(){
							saveButton.text(textSave).removeAttr('disabled');
						}, 1000);
					}
				});
			});
		}
		
		//section tab
		if(UxGallery.sectionTab.length){
			UxGallery.sectionTab.click(function(){
				var thisID = $(this).data('id');
				var thisInput = $(this).parent().find('> input');
				
				UxGallery.sectionTab.removeClass('active');
				$(this).addClass('active');
				
				$('.__ux_gallery_meta_box .form-table').each(function(){
					if($(this).attr('id') != ''){
						$(this).hide();
					}
				})
				$('.__ux_gallery_meta_box .form-table#' +thisID).fadeIn();
				
				thisInput.val(thisID);
			});
		}
		
		//custom grid layouts
		if(UxGallery.customGridLayouts.length){
			UxGallery.customGridLayouts.click(function(){
				var thisParent = $(this).parent();
				var thisModal = thisParent.find('.modal');
				var thisModalBackdrop = thisModal.next('.modal-backdrop');
				var thisCatSelector = $('select#__ux_gallery_custom_grid_image_source');
				var thisPostIDSelector = $('input#post_ID');
				
				UxGallery.body.addClass('modal-open');
				thisModalBackdrop.show();
				thisModal.find('.modal-body').html('<div class="loading"></div>');
				thisModal.show(function(){
					thisModal.addClass('in');
				});
				
				$.post(ajaxurl, {
					'action': 'bm_grid_custom_grid_layouts_ajax',
					'cat_id': thisCatSelector.val(),
					'post_ID': thisPostIDSelector.val()
				}).done(function(result){
					var content = $(result);
					
					thisModal.find('.modal-body').html(content);
					
					thisModal.find('.modal-body').find('.grid-stack').gridstack({
						verticalMargin: 20,
						resizable: {
							handles: 'e, se, s, sw, w'
						}
					});
				});
			});
		}
		
		//portfolio layout builder
		if(UxGallery.portfolioLayout.length){
			UxGallery.portfolioLayout.each(function(){
				var selector = $(this);
                var itemsCtrl = new uxClassItemsControl(selector);
            });
		}
		
		//modal
		if(UxGallery.modal.length){
			UxGallery.modal.each(function(){
                var thisModal = $(this);
				var thisParent = thisModal.parents('tbody');
				var thisClose = thisModal.find('[data-event="close"]');
				var thisBackdrop = thisModal.next('.modal-backdrop');
				
				thisClose.click(function(){
					thisBackdrop.hide();
					UxGallery.body.removeClass('modal-open');
					thisModal.hide(function(){
						thisModal.removeClass('in');
						thisModal.find('.modal-body').html('');
					});
				});
				
				thisModal.find('.btn-primary').click(function(){
					var thisCatSelector = $('select#__ux_gallery_custom_grid_image_source');
					var thisPostIDSelector = $('input#post_ID');
					var thisLayoutMap = $.map($('.grid-stack > .grid-stack-item:visible'), function(el){
						el = $(el);
						var node = el.data('_gridstack_node');
						var post_id = el.data('postid');
						return {
							x: node.x,
							y: node.y,
							width: node.width,
							height: node.height,
							post_id: post_id
						};
					});
					
					$.post(ajaxurl, {
						'action': 'bm_grid_custom_grid_layouts_save_ajax',
						'data': thisLayoutMap,
						'cat_id': thisCatSelector.val(),
						'post_ID': thisPostIDSelector.val()
					}).done(function(result){
						if(result == 'ok'){
							thisBackdrop.hide();
							UxGallery.body.removeClass('modal-open');
							thisModal.hide(function(){
								thisModal.removeClass('in');
								thisModal.find('.modal-body').html('');
							});
						}
					});
				});
            });
		}
		
		//shortcode
		if(UxGallery.shortcode.length){
			var postID = $('input#post_ID').val();
			var shortcodeText = UxGallery.shortcode.val();
				shortcodeText = shortcodeText.replace('[bm-grid id=\"\"]', '[bm-grid id=\"' +postID+ '\"]');
			
			UxGallery.shortcode.val(shortcodeText);
		}
	});
	
})(jQuery);