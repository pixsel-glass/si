<?php include('seolang_header.tpl'); ?>
<style>
	.lm-select-toggle {
		display: inline-block;
		position: relative;
		overflow: hidden;
		cursor: pointer;
	}

	.lm-select-toggle .lm-select-switch {
		display: none !important;
	}

	.lm-select-toggle::before {
		content: attr(data-value);
		display: block;
		min-width: 120px;
		text-align: center;
		padding: 4px 20px 4px 20px;
		font-size: 15px;
		line-height: 20px;
		transition: 0.3s ease-in-out, transform 0.5s, background-color 0.5s, color 0.5s;
	}

	.lm-select-toggle.lm-select-on::before {
		background-color: #4caf50;
		color: #fff;
	}

	.lm-select-toggle.lm-select-off::before {
		background-color: #ccc;
		color: #333;
	}

	.lm-select-toggle:hover {
		cursor: pointer;
	}

	@keyframes blinking {
		0% {
			background-color: #FF4400;
		}

		50% {
			background-color: #FF5500;
		}

		100% {
			background-color: red;
		}
	}

	.lm-must-save {
		animation: blinking 1s infinite;
	}
</style>

<script>
	function add_switcher(customToggles) {

		customToggles.forEach((customToggle) => {
			const toggleSwitch = customToggle.querySelector('.lm-select-switch');
			const options = toggleSwitch.querySelectorAll('option');

			function updateToggleState(context) {

				for (let i = 0; i < options.length; i++) {
					if (options[i].hasAttribute('selected')) {
						toggleSwitch.value = options[i].value;
						if (toggleSwitch.value === '1') {
							context.classList.add('lm-select-on');
							context.classList.remove('lm-select-off');
							context.setAttribute('data-value', options[i].textContent);
						} else {
							context.classList.add('lm-select-off');
							context.classList.remove('lm-select-on');
							context.setAttribute('data-value', options[i].textContent);
						}
						break;
					}
				}
			}

			customToggle.addEventListener('click', function() {
				click_options = this.querySelectorAll('option');

				if (click_options.length > 1) {
					const lm_saves = document.querySelectorAll('.seolang_save');
					lm_saves.forEach((lm_save) => {
						lm_save.classList.add('lm-must-save');
					})
					toggleSwitch.value = toggleSwitch.value === '1' ? '0' : '1';
					for (let i = 0; i < options.length; i++) {
						if (options[i].hasAttribute('selected')) {
							options[i].removeAttribute('selected');
						} else {
							options[i].setAttribute('selected', 'selected');
						}
					}
					updateToggleState(this);
				}

			});

			updateToggleState(customToggle);
		});
	}
</script>


<div class="page-header">
	<div class="container-fluid">
		<div id="content1" style="border: none;">
			<div class="clearboth-1"></div>
			<?php if ($success) { ?>
				<div class="alert-success success"><i class="fa fa-check-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button>
					<?php echo $success; ?>
				</div>
			<?php } ?>
			<?php if (isset($lm_error_warning) && $lm_error_warning) { ?>
				<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button>
					<?php echo $lm_error_warning; ?>
				</div>
			<?php } ?>

			<div id="content" style="border: none;">

				<div style="clear: both; line-height: 1px; font-size: 1px;"></div>

				<?php if (isset($session_success)) {
					unset($session_success); ?>
					<div class="success"><?php echo $language_text_success; ?></div>
				<?php } ?>


				<div class="box1">

					<div class="content">



						<div id="sticky-anchor"></div>

						<div id="sticky" style="margin:5px; float:right;">
							<a href="#" class="mbutton langmark_save"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
						</div>





						<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">


							<div style="display: flex; align-items: center; margin-top: 4px; margin-bottom: 8px;">
								<div>
									<?php echo $ico_seolang; ?> <?php echo strip_tags($heading_title_seolang); ?>&nbsp;<?php echo $seolang_version; ?>&nbsp;&nbsp;<?php if (!isset($seolang_langmark_settings['langmark_widget_status']) || !$seolang_langmark_settings['langmark_widget_status']) { ?><span style="color: red;"><?php } else { ?><span style="color: green;"><?php } ?><?php echo $entry_seolang_widget_status; ?><?php if (!isset($seolang_langmark_settings['langmark_widget_status']) || !$seolang_langmark_settings['langmark_widget_status']) { ?></span><?php } ?>&nbsp;&nbsp;
								</div>

								<div class="input-group lm-select-toggle">
									<select class="form-control lm-select-switch" name="seolang_langmark_settings[langmark_widget_status]">
										<?php if (isset($seolang_langmark_settings['langmark_widget_status']) && $seolang_langmark_settings['langmark_widget_status']) { ?>
											<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
											<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
											<option value="1"><?php echo $text_enabled; ?></option>
											<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>



							<?php if (isset($stores) && is_array($stores) && !empty($stores)) { ?>
								<div style="display: flex; align-items: center; margin-top: 4px; margin-bottom: 8px;">
									<div>
										<?php echo $language->get('entry_store'); ?>&nbsp;&nbsp;
									</div>

									<div class="input-group">
										<select class="form-control sc_select_other" id="asc_langmark_store_id" name="asc_langmark_store_id">
											<?php foreach ($stores as $store) { ?>
												<option value="<?php echo $store['store_id']; ?>" <?php if (isset($store_id) && $store_id == $store['store_id']) { ?> selected="selected" <?php } ?>><?php echo $store['url']; ?> - <?php echo $store['name']; ?></option>
											<?php } ?>
										</select>
									</div>


								</div>
							<?php } ?>

							<div id="tabs" class="htabs">
								<a href="#tab-menu" class="lm-tab-1"><?php echo $tab_text_seolang_menu; ?></a>
								<a href="#tab-options" class="lm-tab-1"><?php echo $tab_options; ?></a>
								<a href="#tab-other" class="lm-tab-1"><?php echo $tab_other; ?></a>
								<a href="#tab-ex" class="lm-tab-1"><?php echo $tab_ex; ?></a>
								<a href="#tab-pagination" class="lm-tab-1"><?php echo $tab_pagination; ?></a>
								<a href="#tab-install" class="lm-tab-1"> <?php echo $entry_install_update; ?></a>
							</div>

							<div id="tab-menu">
								<div class="flex200">

									<div>
										<div>
											<?php echo $language->get('entry_seolang_seolang_options'); ?>&nbsp;
										</div>
										<div class="input-group">
											<a href="<?php echo $url_seolang_seolang_options; ?>" class="markbuttono sc_button"><i class="fa fa-cog fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_langmark_widget'); ?></a>
										</div>
									</div>

									<div>
										<div>
											<?php echo $language->get('entry_seolang_seolang_adapter'); ?>&nbsp;
										</div>
										<div class="input-group">
											<a href="<?php echo $url_seolang_seolang_adapter; ?>" class="markbuttono sc_button"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_seolang_seolang_adapter'); ?></a>
										</div>
									</div>

								</div>
							</div>



							<div id="tab-options">
								<table class="mynotable" style="width: 100%; margin-bottom:20px; background: white; vertical-align: center;">
									<tr style="width: 100%;">
										<td style="width: 100%;">
											<div id="block_multi" style="width: 100%;">
												<div id="tabs-multi" class="htabs">
													<?php
													$multi_name_row_header = 0;
													if (!empty($asc_langmark['multi'])) {
														foreach ($asc_langmark['multi'] as $multi_name => $multi) {
													?>
															<a href="#tab-multi<?php echo $multi_name_row_header; ?>" id="tab-multi-row<?php echo $multi_name_row_header; ?>" class="lm-tab-2">
																<?php echo $multi_name; ?>&nbsp;&nbsp;&nbsp;
																<span onclick="$('#tab-multi-row<?php echo $multi_name_row_header; ?>').remove(); $('#tab-multi<?php echo $multi_name_row_header; ?>').remove(); return false;" class="markbutton button_purple nohref"><i class="fa fa-times" aria-hidden="true"></i></span>
															</a>

														<?php
															$multi_name_row_header++;
														}
													} else {
														?>

														<?php echo $text_multi_empty; ?>

													<?php
													}
													?>
													<a href="#tab-multi-add" id="tab-multi-row-add">
														<span onclick="multi_add(); return false;" class="markbutton button-green nohref"><i class="fa fa-plus" aria-hidden="true"></i></span>
													</a>
												</div>


												<script>
													multi_name_row = <?php echo $multi_name_row_header; ?>;

													function tab_multi_click() {
														$('a[href="#tab-multi' + (multi_name_row - 1) + '"]').click();
													}

													function multi_add() {

														html_tab = '<a href="#tab-multi' + multi_name_row + '" id="tab-multi-row' + multi_name_row + '" class="lm-tab-2">';
														html_tab += 'Region-' + multi_name_row + '&nbsp;&nbsp;&nbsp;';
														html_tab += '<span onclick="$(\'#tab-multi-row' + multi_name_row + '\').remove(); $(\'#tab-multi' + multi_name_row + '\').remove(); return false;" class="markbutton button_purple nohref"><i class="fa fa-times" aria-hidden="true"></i></span>';
														html_tab += '</a>';

														html_tab_content = '';
														html_tab_content += '';

														$('#tab-multi-row-add').before(html_tab);

														$.ajax({
															url: '<?php echo $url_add_multi; ?>',
															dataType: 'html',
															type: 'post',
															data: {
																multi_name_row: multi_name_row,
																store_id: '<?php echo $store_id; ?>'
															},
															beforeSend: function() {
																$('#tab-multi-row' + multi_name_row).append('<div id="add_multi_loading"><?php echo $language->get('text_loading_langmark'); ?></div>');
															},
															success: function(ajax_html) {

																$('#add_multi_loading').remove();
																$('#block_multi').append(ajax_html);
																$('#tabs-multi > a').tabs();
																setTimeout('tab_multi_click()', 500);
															},
															error: function(ajax_html) {
																$('#tab-multi-row' + multi_name_row).append('error');
															}
														});

														multi_name_row++;
													}
												</script>


												<?php
												if (function_exists('modification') && file_exists(modification(DIR_TEMPLATE . 'seolang/langmark_multi.tpl'))) {
													include(modification(DIR_TEMPLATE . 'seolang/langmark_multi.tpl'));
												} else {
													include(DIR_TEMPLATE . 'seolang/langmark_multi.tpl');
												}
												?>

											</div>
										</td>
									</tr>
								</table>

							</div>

							<div id="tab-pagination">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

									<tr>
										<td><?php echo $language->get('entry_pagination'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[pagination]">
													<?php if (isset($asc_langmark['pagination']) && $asc_langmark['pagination']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td><?php echo $language->get('entry_seo_pagination'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[seo_pagination]">
													<?php if (isset($asc_langmark['seo_pagination']) && $asc_langmark['seo_pagination']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>





									<tr>
										<td><?php echo $language->get('entry_url_close_slash'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[url_close_slash]">
													<?php if (isset($asc_langmark['url_close_slash']) && $asc_langmark['url_close_slash']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td class="left"><?php echo $language->get('entry_pagination_prefix'); ?></td>
										<td class="left">
											<div class="input-group">
												<span class="input-group-addon"></span>

												<input type="text" class="form-control" name="asc_langmark_<?php echo $store_id ?>[pagination_prefix]" value="<?php if (isset($asc_langmark['pagination_prefix'])) echo $asc_langmark['pagination_prefix']; ?>" size="20" />
											</div>
										</td>
									</tr>

									<?php
									$multi_name_row_header = 0;
									if (!empty($asc_langmark['multi'])) {
										foreach ($asc_langmark['multi'] as $multi_name => $multi) {
									?>
											<tr>
												<td class="left">
													<?php echo $language->get('entry_title_pagination'); ?> (<?php echo $multi['name']; ?>)
												</td>
												<td>

													<div style="clear: both; margin-top:5px;">
														<div class="input-group">
															<span class="input-group-addon"></span>
															<input type="text" class="form-control" name="asc_langmark_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][pagination_title]" value="<?php if (isset($asc_langmark['multi'][$multi_name]['pagination_title']) && $asc_langmark['multi'][$multi_name]['pagination_title'] != '') {
																																																				echo $multi['pagination_title'];
																																																			} else {
																																																				echo '';
																																																			} ?>">
														</div>
													</div>

												</td>

											</tr>
									<?php }
									} ?>


									<?php if (SC_VERSION > 15) { ?>

										<tr>
											<td><?php echo $language->get('entry_remove_description_status'); ?></td>
											<td>
												<div class="input-group lm-select-toggle">
													<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[description_status]">
														<?php if (isset($asc_langmark['description_status']) && $asc_langmark['description_status']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
														<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td colspan="3" style="text-align: center; background-color: #EEE;">
												&nbsp;
											</td>
											<td></td>
										</tr>

										<tr>
											<td class="left">
												<?php echo $language->get('entry_desc_types'); ?>
											</td>



											<td>
												<div style="float: left;">

													<table id="desc_types" class="list">
														<thead>
															<tr>
																<td class="left"><?php echo $language->get('entry_id'); ?></td>
																<td><?php echo $language->get('entry_title_template'); ?></td>
																<td><?php echo $language->get('entry_title_values'); ?></td>
																<td></td>
															</tr>

														</thead>

														<?php if (isset($asc_langmark['desc_type']) && !empty($asc_langmark['desc_type'])) { ?>
															<?php foreach ($asc_langmark['desc_type'] as $desc_type_id => $desc_type) { ?>
																<?php $desc_type_row = $desc_type_id; ?>
																<tbody id="desc_type_row<?php echo $desc_type_row; ?>">
																	<tr>
																		<td class="left">
																			<input type="text" name="asc_langmark_<?php echo $store_id ?>[desc_type][<?php echo $desc_type_id; ?>][type_id]" value="<?php if (isset($desc_type['type_id'])) echo $desc_type['type_id']; ?>" size="3">
																		</td>

																		<td class="right">

																			<div style="margin-bottom: 3px;">
																				<input type="text" name="asc_langmark_<?php echo $store_id ?>[desc_type][<?php echo $desc_type_id; ?>][title]" value="<?php if (isset($desc_type['title'])) echo $desc_type['title']; ?>" style="width: 300px;">
																			</div>

																		</td>

																		<td class="right">

																			<div style="margin-bottom: 3px;">
																				<textarea name="asc_langmark_<?php echo $store_id ?>[desc_type][<?php echo $desc_type_id; ?>][vars]" style="width: 300px;"><?php if (isset($desc_type['title'])) echo $desc_type['vars']; ?></textarea>
																			</div>

																		</td>

																		<td class="left"><a onclick="$('#desc_type_row<?php echo $desc_type_row; ?>').remove();" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>
																	</tr>

																</tbody>

															<?php } ?>
														<?php } ?>
														<tfoot>
															<tr>
																<td colspan="4">
																	<a onclick="addDescType();" class="markbutton nohref floatright"><?php echo $language->get('entry_add_rule'); ?></a>
																</td>
															</tr>
														</tfoot>
													</table>


												</div>
											</td>
										</tr>


									<?php } ?>


									<tr>
										<td></td>
										<td></td>
									</tr>
								</table>
							</div>


							<div id="tab-other">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>


									<tr>
										<td><?php echo $entry_access; ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[access]">
													<?php if (isset($asc_langmark['access']) && $asc_langmark['access']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>
									<tr>
										<td><?php echo $language->get('entry_hreflang_status'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[hreflang_status]">
													<?php if (isset($asc_langmark['hreflang_status']) && $asc_langmark['hreflang_status']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<tr>
										<td><?php echo $language->get('entry_xdefault_status'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[xdefault_status]">
													<?php if (isset($asc_langmark['xdefault_status']) && $asc_langmark['xdefault_status']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>


									<tr>
										<td><?php echo $language->get('entry_currency_switch'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[currency_switch]">
													<?php if (isset($asc_langmark['currency_switch']) && $asc_langmark['currency_switch']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>


									<tr>
										<td><?php echo $language->get('entry_cache_diff'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[cache_diff]">
													<?php if (isset($asc_langmark['cache_diff']) && $asc_langmark['cache_diff']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>






									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>



									<tr>
										<td><?php echo $language->get('entry_use_link_status'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[use_link_status]">
													<?php if (isset($asc_langmark['use_link_status']) && $asc_langmark['use_link_status']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>


									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>

									<tr>
										<td><?php echo $language->get('entry_commonhome_status'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[commonhome_status]">
													<?php if (isset($asc_langmark['commonhome_status']) && $asc_langmark['commonhome_status']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>




									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
										</td>
										<td></td>
									</tr>

									<tr>
										<td><?php echo $language->get('entry_two_status'); ?></td>
										<td>
											<div class="input-group lm-select-toggle">
												<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[two_status]">
													<?php if (isset($asc_langmark['two_status']) && $asc_langmark['two_status']) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
													<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
													<?php } ?>
												</select>
											</div>
										</td>
									</tr>

									<?php if (SC_VERSION > 23) { ?>

										<tr>
											<td colspan="3" style="text-align: center; background-color: #EEE;">&nbsp;
											</td>
											<td></td>
										</tr>

										<tr>
											<td><?php echo $entry_redirect_new; ?></td>
											<td>
												<div class="input-group lm-select-toggle">
													<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[redirect_new]">
														<?php if (isset($asc_langmark['redirect_new']) && $asc_langmark['redirect_new']) { ?>
															<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
															<option value="0"><?php echo $text_disabled; ?></option>
														<?php } else { ?>
															<option value="1"><?php echo $text_enabled; ?></option>
															<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>



										<?php if (isset($asc_langmark['redirect_codes']) && is_array($asc_langmark['redirect_codes']) && !empty($asc_langmark['redirect_codes'])) { ?>
											<tr>
												<td><?php echo $entry_redirect_code; ?></td>
												<td>

													<div class="input-group">
														<select class="form-control lm-select-switch" name="asc_langmark_<?php echo $store_id ?>[redirect_code]">
															<?php foreach ($asc_langmark['redirect_codes'] as $num => $code) { ?>
																<option value="<?php echo $code; ?>" <?php if (isset($asc_langmark['redirect_code']) && $asc_langmark['redirect_code'] == $code) { ?> selected="selected" <?php } ?>><?php echo $code; ?></option>
															<?php } ?>
														</select>
													</div>
												</td>
											</tr>
										<?php } ?>


									<?php } ?>





								</table>
							</div>

							<div id="tab-ex">
								<table class="mynotable" style="margin-bottom:20px; background: white; vertical-align: center;">

									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">
											<?php echo $language->get('entry_ex_multilang'); ?> <span class="table-help-href">?</span>
										</td>
										<td></td>
									</tr>

									<tr>
										<td class="left">
											<?php echo $language->get('entry_ex_multilang_route'); ?>
										</td>
										<td>
											<div style="float: left;">
												<div class="input-group">
													<span class="input-group-addon"></span>
													<textarea class="form-control" cols="50" rows="3" name="asc_langmark_<?php echo $store_id ?>[ex_multilang_route]"><?php if (isset($asc_langmark['ex_multilang_route'])) {
																																											echo $asc_langmark['ex_multilang_route'];
																																										} else {
																																											echo '';
																																										} ?></textarea>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td class="left">
											<?php echo $language->get('entry_ex_multilang_uri'); ?>
										</td>
										<td>
											<div style="float: left;">
												<div class="input-group">
													<span class="input-group-addon"></span>
													<textarea class="form-control" cols="50" rows="3" name="asc_langmark_<?php echo $store_id ?>[ex_multilang_uri]"><?php if (isset($asc_langmark['ex_multilang_uri'])) {
																																										echo $asc_langmark['ex_multilang_uri'];
																																									} else {
																																										echo '';
																																									} ?></textarea>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td colspan="3" style="text-align: center; background-color: #EEE;">
											<?php echo $language->get('entry_ex_url'); ?> <span class="table-help-href">?</span>
										</td>
										<td></td>
									</tr>

									<tr>
										<td class="left">
											<?php echo $language->get('entry_ex_url_route'); ?>
										</td>
										<td>
											<div style="float: left;">
												<div class="input-group">
													<span class="input-group-addon"></span>
													<textarea class="form-control" cols="50" rows="3" name="asc_langmark_<?php echo $store_id ?>[ex_url_route]"><?php if (isset($asc_langmark['ex_url_route'])) {
																																									echo $asc_langmark['ex_url_route'];
																																								} else {
																																									echo '';
																																								} ?></textarea>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td class="left">
											<?php echo $language->get('entry_ex_url_uri'); ?>
										</td>
										<td>
											<div style="float: left;">
												<div class="input-group">
													<span class="input-group-addon"></span>
													<textarea class="form-control" cols="50" rows="3" name="asc_langmark_<?php echo $store_id ?>[ex_url_uri]"><?php if (isset($asc_langmark['ex_url_uri'])) {
																																									echo $asc_langmark['ex_url_uri'];
																																								} else {
																																									echo '';
																																								} ?></textarea>
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td></td>
										<td></td>
									</tr>
								</table>
							</div>




							<div id="tab-install">

								<div>


									<div>
										<div id="create_tables"></div>
										<a href="#" onclick="
						$.ajax({
							url: '<?php echo $url_create; ?>',
							dataType: 'html',
							beforeSend: function()
								{
									$('#create_tables').html('<?php echo $language->get('text_loading_main'); ?>');
								},

							success: function(json) {
								$('#create_tables').html(json);
								setTimeout('delayer()', 2000);
							},
							error: function(json) {
							$('#create_tables').html('error');
							}
						}); return false;" class="markbuttono" style=""><?php echo $url_create_text; ?></a>

										<?php if (isset($text_update) && $text_update != '') { ?>
											<div style="font-size: 18px; color: red;"><?php echo $text_update; ?></div>
										<?php } ?>

									</div>

									<div>
										<a href="#" onclick="
				$.ajax({
					url: '<?php echo $url_delete; ?>',
					dataType: 'html',
					beforeSend: function()
							{
							$('#create_tables').html('<?php echo $language->get('text_loading_main'); ?>');
							},
					success: function(json) {
						$('#create_tables').html(json);
						//setTimeout('delayer()', 2000);
					},
					error: function(json) {
					$('#create_tables').html('error');
					}
				}); return false;" class="mbuttonr" style=""><?php echo $url_delete_text; ?></a>
									</div>


									<div>

										<div style="display: flex; flex-direction: column; align-items: flex-start;	flex-wrap: wrap; align-content: space-between; justify-content: space-between; 	width: 100%; margin-top: 4px; margin-bottom: 11px;">

											<div style="display: flex; align-items: top; margin-bottom: 11px;">

												<div style="width: 160px;">
													<?php echo $language->get('entry_lm_backup'); ?>&nbsp;
												</div>

												<div class="input-group">
													<a href="#" id="lm_backup" onclick="
										$.ajax({
										url: '<?php echo $url_backup; ?>&lm_backup=1&store_id=<?php echo $store_id; ?>',
										dataType: 'html',
										beforeSend: function()
										{
											$('#div_lm_backup').html('<?php echo $language->get('text_loading_main'); ?>').show();
										},
										success: function(content) {
											if (content) {
												content_array = JSON.parse(content);

												console.log(content_array);

												$('#div_lm_backup').html(content_array['text']);
												setTimeout(hide_messages, 3000, '#div_lm_backup');
												if (content_array['success']) {
													window.location = '<?php echo $url_backup; ?>&store_id=<?php echo $store_id; ?>';
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

											<div style="display: flex; align-items: top; margin-bottom: 11px;">
												<div style="width: 160px;">
													<?php echo $language->get('entry_lm_restore'); ?>&nbsp;&nbsp;
												</div>
												<div class="input-group">
													<a id="lm_restore" class="markbuttono sc_button"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $language->get('text_lm_url_restore'); ?></a>
													<div id="div_lm_restore">&nbsp;</div>
												</div>
											</div>
										</div>


									</div>


									<?php if ($store_id != 0) { ?>
										<div>
											<a href="#" onclick="
				$.ajax({
					url: '<?php echo $url_store_id_related; ?>',
					dataType: 'html',
					beforeSend: function()
							{
								$('#create_tables').html('<?php echo $language->get('text_loading_main'); ?>');
							},
					success: function(json) {
						$('#create_tables').html(json);
					},
					error: function(json) {
					$('#create_tables').html('error');
					}
				}); return false;" class="mbuttonr" style=""><?php echo $url_store_id_repated_text; ?></a>
										</div>
									<?php } ?>



								</div>

							</div>



						</form>








						<style>
							.sticky-back {
								background-color: #E1E1E1;
								box-shadow: 0 0 16px rgba(0, 0, 0, 0.3) !important;
							}

							#sticky.stick {
								position: fixed;
								top: 0;
								z-index: 10000;
							}
						</style>


						<script>
							function sticky_relocate() {
								var window_top = $(window).scrollTop();
								var div_top = $('#sticky-anchor').offset().top;

								if (window_top > div_top) {
									$('#sticky').addClass('stick');
									$('#sticky').addClass('sticky-back');
									$('#sticky').css({
										"right": "0px"
									});

								} else {
									$('#sticky').removeClass('stick');
									$('#sticky').removeClass('sticky-back');
									$('#sticky').css({
										"margin-left": "0px"
									});
								}
							}

							$(function() {
								div_left = $('#sticky').offset().left - 60;
								$(window).scroll(sticky_relocate);
								sticky_relocate();
							});
						</script>
						<script>
							function delayer() {
								window.location = 'index.php?route=seolang/langmark&<?php echo $token_name; ?>=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>';
							}
						</script>











						<?php if (SC_VERSION > 15) { ?>
							<script type="text/javascript">
								var array_desc_type_row = Array();
								array_desc_type_row.push(0);
								<?php
								foreach ($asc_langmark['desc_type'] as $indx => $desc_type) {
								?>
									array_desc_type_row.push(<?php echo $indx; ?>);
								<?php
								}
								?>

								var desc_type_row = <?php echo $desc_type_row + 1; ?>;

								function addDescType() {

									var aindex = -1;
									for (i = 0; i < array_desc_type_row.length; i++) {
										flg = jQuery.inArray(i, array_desc_type_row);
										if (flg == -1) {
											aindex = i;
										}
									}
									if (aindex == -1) {
										aindex = array_desc_type_row.length;
									}
									desc_type_row = aindex;
									array_desc_type_row.push(aindex);

									html = '<tbody id="desc_type_row' + desc_type_row + '">';
									html += '  <tr>';
									html += '  <td class="left">';
									html += ' 	<input type="text" name="asc_langmark_<?php echo $store_id ?>[desc_type][' + desc_type_row + '][type_id]" value="' + desc_type_row + '" size="3">';
									html += '  </td>';

									html += '  <td class="right">';


									html += '	<div style="margin-bottom: 3px;">';
									html += '		<input type="text" name="asc_langmark_<?php echo $store_id ?>[desc_type][' + desc_type_row + '][title]" value="" style="width: 300px;">';
									html += '	</div>';
									html += '  </td>';

									html += ' <td class="right">';

									html += ' <div style="margin-bottom: 3px;">';
									html += ' <textarea name="asc_langmark_<?php echo $store_id ?>[desc_type][' + desc_type_row + '][vars]" style="width: 300px;">description</textarea>';
									html += ' </div>';

									html += ' </td>';

									html += '  <td class="left"><a onclick="$(\'#desc_type_row' + desc_type_row + '\').remove(); array_desc_type_row.remove(desc_type_row);" class="markbutton button_purple nohref"><?php echo $button_remove; ?></a></td>';

									html += '  </tr>';
									html += '</tbody>';

									$('#desc_types tfoot').before(html);

									desc_type_row++;
								}
							</script>
						<?php } ?>

						<script>
							form_submit = function() {
								$('#form').submit();
								return false;
							}
							$('.langmark_save').bind('click', form_submit);
						</script>

						<script>
							$('#tabs a').tabs();
						</script>

						<script>
							$('#tabs-multi > a').tabs();
						</script>

						<script type="text/javascript">
							function odd_even() {
								var kz = 0;
								$('table tr').each(function(i, elem) {
									$(this).removeClass('odd');
									$(this).removeClass('even');
									if ($(this).is(':visible')) {
										kz++;
										if (kz % 2 == 0) {
											$(this).addClass('odd');
										}
									}
								});
							}

							$(document).ready(function() {
								odd_even();

								$('.htabs a').click(function() {
									odd_even();
								});

								$('.vtabs a').click(function() {
									odd_even();
								});

							});

							function input_select_change() {

								$('input').each(function() {
									if (!$(this).hasClass('no_change')) {
										$(this).removeClass('sc_select_enable');
										$(this).removeClass('sc_select_disable');

										if ($(this).val() != '') {
											$(this).addClass('sc_select_enable');
										} else {
											$(this).addClass('sc_select_disable');
										}
									}
								});

								$('select').each(function() {
									if (!$(this).hasClass('no_change')) {
										$(this).removeClass('sc_select_enable');
										$(this).removeClass('sc_select_disable');

										this_val = $(this).find('option:selected').val()

										if (this_val == '1') {
											$(this).addClass('sc_select_enable');
										}

										if (this_val == '0' || this_val == '') {
											$(this).addClass('sc_select_disable');
										}

										if (this_val != '0' && this_val != '1' && this_val != '') {
											$(this).addClass('sc_select_other');
										}
									}
								});
							}


							$('.table-help-href').on('click', function() {
								$('.help').toggle();
							});


							$(document).ready(function() {
								$('.help').hide();

								input_select_change();

								$("select")
									.change(function() {
										input_select_change();

									});

								$("input")
									.blur(function() {
										input_select_change();
									});


							});
						</script>

						<script>
							$('#lm_restore').on('click', function() {
								$('#lm-form-upload').remove();

								$('body').prepend('<form enctype="multipart/form-data" id="lm-form-upload" style="display: none;"><input type="file" name="file" /></form>');

								$('#lm-form-upload input[name=\'file\']').trigger('click');

								if (typeof timer != 'undefined') {
									clearInterval(timer);
								}
								timer = setInterval(function() {
									if ($('#lm-form-upload input[name=\'file\']').val() != '') {
										clearInterval(timer);
										$.ajax({
											url: '<?php echo $url_restore; ?>&store_id=<?php echo $store_id; ?>',
											type: 'post',
											data: new FormData($('#lm-form-upload')[0]),
											cache: false,
											contentType: false,
											processData: false,
											beforeSend: function() {
												$('#div_lm_restore').html('<?php echo $language->get('text_loading_main_without'); ?>').show();
											},
											success: function(content) {
												if (content) {
													content_array = JSON.parse(content);
													$('#div_lm_restore').html(content_array['text']);
													setTimeout(hide_messages, 3000, '#div_lm_backup');
													if (content_array['success']) {
														$('#langmark_cache_remove').trigger('click');
														window.location = '<?php echo $url_langmark; ?>&lm_restore=1';
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
						</script>

						<script>
							function hide_messages(sel) {
								$(sel).html('&nbsp;');
							}
						</script>
						<script>
							$('#asc_langmark_store_id').change(function() {
								var store_id = $(this).val();
								window.location = 'index.php?route=seolang/langmark&<?php echo $token_name; ?>=<?php echo $token; ?>&store_id=' + store_id;
							});
						</script>


						<style>
							.flex-box {
								display: flex;
								align-items: center;
								align-content: stretch;
								justify-content: space-between;
							}

							.flex-box>div {
								width: 33.3%;
							}
						</style>
						<style>
							#tab-install {
								display: flex;
								flex-direction: column;
								align-items: flex-start;
								flex-wrap: wrap;
								align-content: space-between;
								justify-content: space-between;
								width: 100%;
								margin-top: 4px;
								margin-bottom: 11px;

							}

							#tab-install>div {
								display: flex;
								align-items: top;
								margin-bottom: 11px;
							}

							#tab-install>div>div {
								margin-right: 21px;
							}
						</style>


					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (SC_VERSION > 15) { ?>
	</div>
<?php } ?>
<?php if (SC_VERSION < 20) { ?>
	<style>
		#footer {
			margin-top: 0px;
		}
	</style>
<?php } ?>
<script>
	$('.alert').on('click', function() {
		$(this).hide(1000)
	});
	setTimeout(function() {
		$('.alert').hide(1000);
	}, 2500, '.alert');
	$('.success').on('click', function() {
		$(this).hide(1000)
	});
	setTimeout(function() {
		$('.success').hide(1000);
	}, 3500, '.success');
</script>
<script>
	function tab_history(line) {

		string_lm_tabs_click = localStorage.getItem('lm_tabs_click_' + line);

		if (string_lm_tabs_click == null) {
			var array_lm_tabs_click = [];
		} else {
			var array_lm_tabs_click = JSON.parse(string_lm_tabs_click);

			array_lm_tabs_click.forEach(function(item, index, array) {
				$('a[href="' + item + '"]').click();
				console.log(item);
			});
		}

		$('.lm-tab-' + line).on('click', function() {
			lm_tab_href = $(this).attr('href');
			array_lm_tabs_click.push(lm_tab_href);
			if (array_lm_tabs_click.length > 3) {
				array_lm_tabs_click.shift();
			}
			console.log(lm_tab_href);
			localStorage.setItem('lm_tabs_click_' + line, JSON.stringify(array_lm_tabs_click));
		});
	}

	tab_history(1);
	tab_history(2);
</script>
<script>
	const customToggles = document.querySelectorAll('.lm-select-toggle');
	add_switcher(customToggles);
</script>

<?php if (isset($footer)) echo $footer; ?>