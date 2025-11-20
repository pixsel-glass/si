<div style="
			display: flex;
			flex-direction: column;
  			align-items: flex-start;
			flex-wrap: wrap;
			align-content: space-between;
			justify-content: space-between;
			width: 100%;
			margin-top: 4px;
			margin-bottom: 11px;
			">
	<div style="display: flex; align-items: center; margin-bottom: 11px;">
		<div style="width: 160px;">
			<?php echo $language->get('entry_seolang_seolang_ocmodrefresh'); ?>&nbsp;
		</div>
		<div class="input-group">
			<a href="#" id="seolang_ocmod_refresh" onclick="
										$.ajax({
										url: '<?php echo $url_ocmodrefresh; ?>',
										dataType: 'html',
										beforeSend: function()
										{
											$('#div_ocmod_refresh').html('<?php echo $language->get('text_seolang_loading_main'); ?>').show();
										},
										success: function(content) {
											if (content) {
												$('#div_ocmod_refresh').html('<span style=\'color:green\'><?php echo $language->get('text_seolang_ocmodrefresh_success'); ?><\/span>');
												setTimeout(hide_messages, 3000, '#div_ocmod_refresh');
											}
										},
										error: function(content) {
											$('#div_ocmod_refresh').html('<span style=\'color:red\'><?php echo $language->get('text_seolang_ocmodrefresh_fail'); ?><\/span>');
										}
										}); return false;" class="markbuttono sc_button" style=""><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('url_text_seolang_ocmodrefresh'); ?></a>
			<div id="div_ocmod_refresh">&nbsp;</div>
		</div>
	</div>

	<div style="display: flex; align-items: center; margin-bottom: 11px;">
		<div style="width: 160px;">
			<?php echo $language->get('entry_seolang_seolang_cacheremove'); ?>&nbsp;
		</div>
		<div class="input-group">
			<a id="seolang_cache_remove" class="markbuttono sc_button"><i class="fa fa-recycle" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('url_text_seolang_cacheremove'); ?></a>
			<div id="div_cache_remove">&nbsp;</div>
		</div>
	</div>
</div>



<div style="display: flex;
flex-direction: column;
  align-items: flex-start;
flex-wrap: wrap;
align-content: space-between;
justify-content: space-between;
width: 100%;
margin-top: 4px;
margin-bottom: 11px;">

	<div style="display: flex; align-items: center; margin-bottom: 11px;">
		<div style="width: 160px;">
			<?php echo $language->get('entry_lm_backup'); ?>&nbsp;
		</div>
		<div class="input-group">
			<a href="#" id="lm_backup" onclick="
				$.ajax({
				url: '<?php echo $url_backup; ?>&lm_backup=1',
				dataType: 'html',
				beforeSend: function()
				{
					$('#div_lm_backup').html('<?php echo $language->get('text_seolang_loading_main'); ?>').show();
				},
				success: function(content) {
					if (content) {
						content_array = JSON.parse(content);
						$('#div_lm_backup').html(content_array['text']);
						setTimeout(hide_messages, 3000, '#div_lm_backup');
						if (content_array['success']) {
							window.location = '<?php echo $url_backup; ?>';
						}
					}
				},
				error: function(content) {
					$('#div_lm_backup').html('<span style=\'color:red\'><?php echo $language->get('text_lm_backup_fail'); ?><\/span>');
				}
				}); return false;" class="markbuttono sc_button" style=""><i class="fa fa-download" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('text_lm_url_backup'); ?></a>
			<div id="div_lm_backup">&nbsp;</div>
		</div>
	</div>

	<div style="display: flex; align-items: center; margin-bottom: 11px;">
		<div style="width: 160px;">
			<?php echo $language->get('entry_lm_restore'); ?>&nbsp;&nbsp;
		</div>
		<div class="input-group">
			<a id="lm_restore" class="markbuttono sc_button"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('text_lm_url_restore'); ?></a>
			<div id="div_lm_restore">&nbsp;</div>
		</div>
	</div>


	<div style="display: flex; align-items: center; margin-bottom: 11px;">
		<div style="width: 160px;">
		<?php echo $text_seolang_check_ver; ?>&nbsp;&nbsp;
		</div>
		<div class="input-group">
			<a id="sc_check_common" onclick="
										$.ajax({
											url: '<?php echo $url_check_ver; ?>',
											dataType: 'html',
											beforeSend: function()
											{
								               $('#check_ver').html('<?php echo $language->get('text_seolang_loading_main'); ?>');
											},
											success: function(json) {
												
													
												if (json) {
													$('#check_ver').html(json);
													setTimeout(hide_messages, 5000, '#check_ver');
												}												
											},
											error: function(json) {
											$('#check_ver').html('error');
											}
										}); return false;" class="markbuttono sc_button"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $text_seolang_check_ver; ?></a>
			<div id="check_ver">&nbsp;</div>
		</div>
	</div>








</div>

<script>
	$('#lm_restore').on('click', function() {
		$('#sv-form-upload').remove();

		$('body').prepend('<form enctype="multipart/form-data" id="sv-form-upload" style="display: none;"><input type="file" name="file" /></form>');

		$('#sv-form-upload input[name=\'file\']').trigger('click');

		if (typeof timer != 'undefined') {
			clearInterval(timer);
		}
		timer = setInterval(function() {
			if ($('#sv-form-upload input[name=\'file\']').val() != '') {
				clearInterval(timer);
				$.ajax({
					url: '<?php echo $url_restore; ?>',
					type: 'post',
					data: new FormData($('#sv-form-upload')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function() {
						$('#div_lm_restore').html('<?php echo $language->get('text_seolang_loading_main_without'); ?>').show();
					},
					success: function(content) {
						if (content) {
							content_array = JSON.parse(content);
							$('#div_lm_restore').html(content_array['text']);
							setTimeout(hide_messages, 3000, '#div_lm_backup');
							if (content_array['success']) {
								$('#seolang_remove').trigger('click');
								window.location = '<?php echo $url_seolang; ?>&lm_restore=1';
							}
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						$('#div_lm_restore').html('<span style="color:red"><?php echo $language->get('text_lm_backup_fail'); ?></span><br>' + thrownError + '<br>' + xhr.statusText + '<br>' + xhr.responseText);
					}
				});
			}
		}, 500);

	});

	<?php if (isset($lm_save) && $lm_save) { ?>
		$(document).ready(function() {
			$('#lm_save').click();
		});
	<?php } ?>
</script>

<script>
	function lm_cache_remove() {
		$.ajax({
			url: '<?php echo $url_cacheremove; ?>',
			dataType: 'html',
			beforeSend: function() {
				$('#div_cache_remove').html('<?php echo $language->get('text_seolang_loading_main_without'); ?>').show();
			},
			success: function(content) {
				if (content) {
					$('#div_cache_remove').html('<span style=\'color:green\'>' + content + '<\/span>');
					setTimeout(hide_messages, 3000, '#div_cache_remove');
				}
			},
			error: function(content) {
				$('#div_cache_remove').html('<span style=\'color:red\'><?php echo $language->get('text_seolang_cacheremove_fail'); ?><\/span>');
			}
		});
		return false;
	}

	if ($.isFunction($.fn.on)) {
		$(document).on('click', '#seolang_cache_remove', lm_cache_remove);
	} else {
		$('#seolang_cache_remove').live('click', lm_cache_remove);
	}

	function hide_messages(sel) {
		$(sel).html('&nbsp;');

	}
</script>