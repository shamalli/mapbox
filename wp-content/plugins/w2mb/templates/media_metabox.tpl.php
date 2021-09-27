<?php if ($images_number): ?>
<script>
	var images_number = <?php echo esc_js($images_number); ?>;

	(function($) {
		"use strict";

		window.w2mb_image_attachment_tpl = function(attachment_id, uploaded_file, title, size, width, height) {
			var image_attachment_tpl = '<div class="w2mb-attached-item w2mb-move-label">' +
					'<input type="hidden" name="attached_image_id[]" class="w2mb-attached-item-id" value="'+attachment_id+'" />' +
					'<a href="'+uploaded_file+'" data-w2mb_lightbox="listing_images" class="w2mb-attached-item-img" style="background-image: url('+uploaded_file+')"></a>' +
					'<div class="w2mb-attached-item-input">' +
						'<input type="text" name="attached_image_title[]" class="w2mb-form-control" value="" placeholder="<?php esc_attr_e('optional image title', 'W2MB'); ?>" />' +
					'</div>' +
					<?php if ($logo_enabled): ?>
					'<div class="w2mb-attached-item-logo w2mb-radio">' +
						'<label>' +
							'<input type="radio" name="attached_image_as_logo" value="'+attachment_id+'"> <?php esc_attr_e('set this image as logo', 'W2MB'); ?>' +
						'</label>' +
					'</div>' +
					<?php endif; ?>
					'<div class="w2mb-attached-item-delete w2mb-fa w2mb-fa-trash-o" title="<?php esc_attr_e("remove", "W2MB"); ?>"></div>' +
					'<div class="w2mb-attached-item-metadata">'+size+' ('+width+' x '+height+')</div>' +
				'</div>';

			return image_attachment_tpl;
		};

		window.w2mb_update_images_attachments_order = function() {
			$("#w2mb-attached-images-order").val($(".w2mb-attached-item-id").map(function() {
				return $(this).val();
			}).get());
		}
		window.w2mb_check_images_attachments_number = function() {
			if (images_number > $("#w2mb-images-upload-wrapper .w2mb-attached-item").length) {
				<?php if (is_admin()): ?>
				$("#w2mb-admin-upload-functions").show();
				<?php else: ?>
				$(".w2mb-upload-item").show();
				<?php endif; ?>
				return true;
			} else {
				<?php if (is_admin()): ?>
				$("#w2mb-admin-upload-functions").hide();
				<?php else: ?>
				$(".w2mb-upload-item").hide();
				<?php endif; ?>
				return false;
			}
		}

		$(function() {
			var sortable_images = $("#w2mb-attached-images-wrapper").sortable({
				delay: 50,
	    		placeholder: "ui-sortable-placeholder",
	    		items: ".w2mb-attached-item",
				helper: function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				},
				start: function(e, ui){
					ui.placeholder.width(ui.item.width());
					ui.placeholder.height(ui.item.height());
				},
				update: function( event, ui ) {
					w2mb_update_images_attachments_order();
				}
		    });

	    	// disable sortable on android, otherwise it breaks click events on image, radio and delete button
	    	var ua = navigator.userAgent.toLowerCase();
	    	if (ua.indexOf("android") > -1) {
				sortable_images.sortable("disable");
			};
			
			w2mb_check_images_attachments_number();

			$("#w2mb-attached-images-wrapper").on("click", ".w2mb-attached-item-delete", function() {
				$(this).parents(".w2mb-attached-item").remove();

				$.ajax({
					url: w2mb_js_objects.ajaxurl,
					type: "POST",
					dataType: "json",
					data: {
						action: 'w2mb_remove_image',
						post_id: <?php echo esc_js($object_id); ?>,
						attachment_id: $(this).parent().find(".w2mb-attached-item-id").val(),
						_wpnonce: '<?php echo wp_create_nonce('remove_image'); ?>'
					}
				});
	
				w2mb_check_images_attachments_number();
				w2mb_update_images_attachments_order();
			});

			<?php if (!is_admin()): ?>
			$(document).on("click", ".w2mb-upload-item-button", function(e){
				e.preventDefault();
			
				$(this).parent().find("input").trigger("click");
			});

			$('.w2mb-upload-item').fileupload({
				sequentialUploads: true,
				dataType: 'json',
				url: '<?php echo admin_url('admin-ajax.php?action=w2mb_upload_image&post_id='.$object_id.'&_wpnonce='.wp_create_nonce('upload_images')); ?>',
				dropZone: $('.w2mb-drop-attached-item'),
				add: function (e, data) {
					if (w2mb_check_images_attachments_number()) {
						var jqXHR = data.submit();
					} else {
						return false;
					}
				},
				send: function (e, data) {
					w2mb_add_iloader_on_element($(this).find(".w2mb-drop-attached-item"));
				},
				done: function(e, data) {
					var result = data.result;
					if (result.uploaded_file) {
						var size = result.metadata.size;
						var width = result.metadata.width;
						var height = result.metadata.height;
						$(this).before(w2mb_image_attachment_tpl(result.attachment_id, result.uploaded_file, data.files[0].name, size, width, height));
						w2mb_custom_input_controls();
					} else {
						$(this).find(".w2mb-drop-attached-item").append("<p>"+result.error_msg+"</p>");
					}
					$(this).find(".w2mb-drop-zone").show();
					w2mb_delete_iloader_from_element($(this).find(".w2mb-drop-attached-item"));

					w2mb_check_images_attachments_number();
					w2mb_update_images_attachments_order();
				}
			});
			<?php endif; ?>
		});
	})(jQuery);
</script>

<div id="w2mb-images-upload-wrapper" class="w2mb-content w2mb-media-upload-wrapper">
	<input type="hidden" id="w2mb-attached-images-order" name="attached_images_order" value="<?php echo implode(',', array_keys($images)); ?>">
	<h4><?php esc_html_e('Attach images', 'W2MB'); ?></h4>

	<div id="w2mb-attached-images-wrapper">
		<?php foreach ($images AS $attachment_id=>$attachment): ?>
		<?php $src = wp_get_attachment_image_src($attachment_id, array(250, 250)); ?>
		<?php $src_full = wp_get_attachment_image_src($attachment_id, 'full'); ?>
		<?php $metadata = wp_get_attachment_metadata($attachment_id); ?>
		<?php $metadata['size'] = size_format(filesize(get_attached_file($attachment_id))); ?>
		<div class="w2mb-attached-item w2mb-move-label">
			<input type="hidden" name="attached_image_id[]" class="w2mb-attached-item-id" value="<?php echo esc_attr($attachment_id); ?>" />
			<a href="<?php echo $src_full[0]; ?>" data-w2mb_lightbox="listing_images" class="w2mb-attached-item-img" style="background-image: url('<?php echo $src[0]; ?>')"></a>
			<div class="w2mb-attached-item-input">
				<input type="text" name="attached_image_title[]" class="w2mb-form-control" value="<?php esc_attr_e($attachment['post_title']); ?>" placeholder="<?php esc_attr_e('optional image title', 'W2MB'); ?>" />
			</div>
			<?php if ($logo_enabled): ?>
			<div class="w2mb-attached-item-logo w2mb-radio">
				<label>
					<input type="radio" name="attached_image_as_logo" value="<?php echo esc_attr($attachment_id); ?>" <?php checked($logo_image, $attachment_id); ?>> <?php esc_html_e('set this image as logo', 'W2MB'); ?>
				</label>
			</div>
			<?php endif; ?>
			<div class="w2mb-attached-item-delete w2mb-fa w2mb-fa-trash-o" title="<?php esc_attr_e("delete", "W2MB"); ?>"></div>
			<div class="w2mb-attached-item-metadata"><?php echo $metadata['size']; ?> (<?php echo $metadata['width']; ?> x <?php echo $metadata['height']; ?>)</div>
		</div>
		<?php endforeach; ?>
		<?php if (!is_admin()): ?>
		<div class="w2mb-upload-item">
			<div class="w2mb-drop-attached-item">
				<div class="w2mb-drop-zone">
					<?php esc_html_e("Drop here", "W2MB"); ?>
					<button class="w2mb-upload-item-button w2mb-btn w2mb-btn-primary"><?php esc_html_e("Browse", "W2MB"); ?></button>
					<input type="file" name="browse_file" multiple />
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="w2mb-clearfix"></div>

	<?php if (is_admin() && current_user_can('upload_files')): ?>
	<script>
		(function($) {
			"use strict";
		
			$(function() {
				$(document).on('click', '#w2mb-admin-upload-image', function(event) {
					event.preventDefault();
			
					var frame = wp.media({
						title : '<?php echo esc_js(sprintf(esc_html__('Upload image (%d maximum)', 'W2MB'), $images_number)); ?>',
						multiple : true,
						library : { type : 'image'},
						button : { text : '<?php echo esc_js(esc_html__('Insert', 'W2MB')); ?>'},
					});
					frame.on('select', function() {
						var selected_images = [];
						var selection = frame.state().get('selection');
						selection.each(function(attachment) {
							attachment = attachment.toJSON();
							if (attachment.type == 'image') {
					
								if (w2mb_check_images_attachments_number()) {
									w2mb_ajax_loader_show();
			
									$.ajax({
										type: "POST",
										async: false,
										url: w2mb_js_objects.ajaxurl,
										data: {
											'action': 'w2mb_upload_media_image',
											'attachment_id': attachment.id,
											'post_id': <?php echo esc_js($object_id); ?>,
											'_wpnonce': '<?php echo wp_create_nonce('upload_images'); ?>',
										},
										attachment_id: attachment.id,
										attachment_url: attachment.sizes.full.url,
										attachment_title: attachment.title,
										dataType: "json",
										success: function (response_from_the_action_function) {
											if (response_from_the_action_function != 0) {
												var size = response_from_the_action_function.metadata.size;
												var width = response_from_the_action_function.metadata.width;
												var height = response_from_the_action_function.metadata.height;
												$("#w2mb-attached-images-wrapper").append(w2mb_image_attachment_tpl(this.attachment_id, this.attachment_url, this.attachment_title, size, width, height));
												w2mb_check_images_attachments_number();
												w2mb_update_images_attachments_order();
											}
												
											w2mb_ajax_loader_hide();
										}
									});
								}
							}
						
						});
					});
					frame.open();
				});
			});
		})(jQuery);
	</script>
	<div id="w2mb-admin-upload-functions">
		<div class="w2mb-upload-option">
			<input
				type="button"
				id="w2mb-admin-upload-image"
				class="w2mb-btn w2mb-btn-primary"
				value="<?php esc_attr_e('Upload image', 'W2MB'); ?>" />
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>


<?php if ($videos_number): ?>
<script>
	var videos_number = <?php echo esc_js($videos_number); ?>;

	(function($) {
		"use strict";

		window.w2mb_video_attachment_tpl = function(video_id, image_url) {
			var video_attachment_tpl = '<div class="w2mb-attached-item">' +
				'<input type="hidden" name="attached_video_id[]" value="'+video_id+'" />' +
				'<div class="w2mb-attached-item-img" style="background-image: url('+image_url+')"></div>' +
				'<div class="w2mb-attached-item-delete w2mb-fa w2mb-fa-trash-o" title="<?php esc_attr_e("delete", "W2MB"); ?>"></div>' +
			'</div>';

			return video_attachment_tpl;
		};

		window.w2mb_check_videos_attachments_number = function() {
			if (videos_number > $("#w2mb-attached-videos-wrapper .w2mb-attached-item").length) {
				$("#w2mb-attach-videos-functions").show();
			} else {
				$("#w2mb-attach-videos-functions").hide();
			}
		}

		$(function() {
			w2mb_check_videos_attachments_number();

			$("#w2mb-attached-videos-wrapper").on("click", ".w2mb-attached-item-delete", function() {
				$(this).parents(".w2mb-attached-item").remove();
	
				w2mb_check_videos_attachments_number();
			});
		});
	})(jQuery);
</script>

<div id="w2mb-video-attach-wrapper" class="w2mb-content w2mb-media-upload-wrapper">
	<h4><?php esc_html_e("Attach videos", "W2MB"); ?></h4>
	
	<div id="w2mb-attached-videos-wrapper">
		<?php foreach ($videos AS $video): ?>
		<div class="w2mb-attached-item">
			<input type="hidden" name="attached_video_id[]" value="<?php echo esc_attr($video['id']); ?>" />
			<?php
			if (strlen($video['id']) == 11) {
				$image_url = "http://i.ytimg.com/vi/" . $video['id'] . "/0.jpg";
			} elseif (strlen($video['id']) == 8 || strlen($video['id']) == 9) {
				$data = file_get_contents("http://vimeo.com/api/v2/video/" . $video['id'] . ".json");
				$data = json_decode($data);
				$image_url = $data[0]->thumbnail_medium;
			} ?>
			<div class="w2mb-attached-item-img" style="background-image: url('<?php echo $image_url; ?>')"></div>
			<div class="w2mb-attached-item-delete w2mb-fa w2mb-fa-trash-o" title="<?php esc_attr_e("delete", "W2MB"); ?>"></div>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="w2mb-clearfix"></div>

	<script>
		(function($) {
			"use strict";
		
			window.attachVideo = function() {
				if ($("#w2mb-attach-video-input").val()) {
					var regExp_youtube = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
					var regExp_vimeo = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
					var matches_youtube = $("#w2mb-attach-video-input").val().match(regExp_youtube);
					var matches_vimeo = $("#w2mb-attach-video-input").val().match(regExp_vimeo);
					if (matches_youtube && matches_youtube[2].length == 11) {
						var video_id = matches_youtube[2];
						var image_url = 'http://i.ytimg.com/vi/'+video_id+'/0.jpg';
						$("#w2mb-attached-videos-wrapper").append(w2mb_video_attachment_tpl(video_id, image_url));

						w2mb_check_videos_attachments_number();
					} else if (matches_vimeo && (matches_vimeo[3].length == 8 || matches_vimeo[3].length == 9)) {
						var video_id = matches_vimeo[3];
						var url = "//vimeo.com/api/v2/video/" + video_id + ".json?callback=showVimeoThumb";
					    var script = document.createElement('script');
					    script.src = url;
					    $("#w2mb-attach-videos-functions").before(script);
					} else {
						alert("<?php esc_attr_e('Wrong URL or this video is unavailable', 'W2MB'); ?>");
					}
				}
			};

			window.showVimeoThumb = function(data){
				var video_id = data[0].id;
			    var image_url = data[0].thumbnail_medium;
			    $("#w2mb-attached-videos-wrapper").append(w2mb_video_attachment_tpl(video_id, image_url));

			    w2mb_check_videos_attachments_number();
			};
		})(jQuery);
	</script>
	<div id="w2mb-attach-videos-functions">
		<div class="w2mb-upload-option">
			<label><?php esc_html_e('Enter full YouTube or Vimeo video link', 'W2MB'); ?></label>
		</div>
		<div class="w2mb-upload-option">
			<input type="text" id="w2mb-attach-video-input" class="w2mb-form-control" placeholder="https://youtu.be/XXXXXXXXXXXX" />
		</div>
		<div class="w2mb-upload-option">
			<input
				type="button"
				class="w2mb-btn w2mb-btn-primary"
				onclick="return attachVideo(); "
				value="<?php esc_attr_e('Attach video', 'W2MB'); ?>" />
		</div>
	</div>
</div>
<?php endif; ?>