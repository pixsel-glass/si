<?php
if (!isset($multi_name_row)) {
	$multi_name_row = 0;
}
if (!empty($seolang_settings_store['multi'])) {
?>

	<style>
		.flex-box-left {
			display: flex;
			flex-flow: row wrap;
		}

		.flex-box-left>div {
			margin-right: 20px;
		}
	</style>

	<div id="tab-multi<?php echo $multi_name_row; ?>">
		<div style="clear: both; height: 1px; line-height: 1px;">&nbsp;</div>
		<div id="tabs-tab-multi<?php echo $multi_name_row; ?>" class="htabs">
			<a href="#tab-multi-options-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $tab_text_seolang_options; ?></a>
			<a href="#tab-multi-position-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $tab_text_seolang_position; ?></a>
			<a href="#tab-multi-access-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $tab_text_seolang_access; ?></a>
		</div>

		<div id="tab-multi-options-<?php echo $multi_name_row; ?>">

			<table id="multi_name_row<?php echo $multi_name_row; ?>" style="width: 100%;">
				<tr>
					<td colspan="3" style="text-align: center; background-color: #F4F6F7;">

						<div class="flex-box">
							<div>
								&nbsp;
							</div>

							<div>
								<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_widget_' . $multi['widget']); ?></span>
							</div>

							<div style="text-align: right;">

								<a onclick="$('#tab-multi-row<?php echo $multi_name_row; ?>').remove(); $('#tab-multi<?php echo $multi_name_row; ?>').remove(); return false;" class="markbutton button_purple nohref">
									<?php echo $button_remove; ?>
									<span class="markbutton button_purple nohref"><i class="fa fa-times" aria-hidden="true"></i></span>
								</a>

								<a onclick="multi_add('<?php echo $multi_name; ?>'); return false;" id="tab-multi-row-copy-<?php echo $multi_name_row; ?>" class="yellowbutton markbutton nohref">
									<?php echo $entry_seolang_copy; ?>
									<span class="yellowbutton markbutton nohref"><i class="fa fa-plus" aria-hidden="true"></i></span>
								</a>

								<a onclick="multi_add(); return false;" id="tab-multi-row-add-<?php echo $multi_name_row; ?>" class="markbutton button-green nohref">
									<?php echo $entry_seolang_add; ?>
									<span class="markbutton button-green nohref"><i class="fa fa-plus" aria-hidden="true"></i></span>
								</a>

							</div>
						</div>

					</td>

				</tr>

				<tr>
					<td>

						<div style="clear: both;">
							<div class="seolang-margin-top-6">
								<?php echo $entry_seolang_status; ?>
							</div>
							<div class="input-group lm-select-toggle">
								<select class="form-control lm-select-switch" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][status]">
									<?php if (isset($seolang_settings_store['multi'][$multi_name]['status']) && $seolang_settings_store['multi'][$multi_name]['status']) { ?>
										<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
										<option value="0"><?php echo $text_seolang_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_seolang_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
									<?php } ?>
								</select>



							</div>

						</div>


						<div style="clear: both;">
							<div class="seolang-margin-top-6">
								<?php echo $entry_seolang_name; ?>
							</div>
							<?php
							if (isset($error_name) && $error_name == $multi['name']) {
							?>
								<div class="error">
									<?php echo $text_seolang_error_name; ?>
								</div>
							<?php
							}
							?>

							<div class="input-group seolang_cmswidget_settings">
								<span class="input-group-addon"><?php echo $language->get('entry_seolang_id'); ?>: <span class="seolang_name_id"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['cmswidget'])) {
																																						echo $seolang_settings_store_widgets_data[$multi_name]['cmswidget'];
																																					} else {
																																						echo '';
																																					} ?></span></span>
								<input type="text" class="form-control seolang_name" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][name]" id="seolang_settings_<?php echo $store_id ?>_multi_<?php echo $multi_name; ?>_name" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['name'])) {
																																																																				echo $multi_name;
																																																																			} else {
																																																																				echo '';
																																																																			} ?>">
								<input type="hidden" class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][widget]" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['widget'])) {
																																												echo $seolang_settings_store['multi'][$multi_name]['widget'];
																																											} else {
																																												echo 'seolang';
																																											} ?>">
								<input type="hidden" class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][store_id]" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['store_id'])) {
																																													echo $seolang_settings_store['multi'][$multi_name]['store_id'];
																																												} else {
																																													echo 0;
																																												} ?>">
								<input type="hidden" class="form-control seolang_cmswidget" name="<?php echo $multi_name; ?>[cmswidget]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['cmswidget'])) {
																																					echo $seolang_settings_store_widgets_data[$multi_name]['cmswidget'];
																																				} else {
																																					echo '';
																																				} ?>">
							</div>
						</div>




						<div id="tabs-tab-multi-<?php echo $multi_name_row; ?>" class="htabs seolang-margin-top-6">
							<a href="#tab-multi-other-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $entry_seolang_tab_other; ?></a>
							<a href="#tab-multi-html-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $entry_seolang_html; ?></a>
							<a href="#tab-multi-popup-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $entry_seolang_popup; ?></a>
							<a href="#tab-multi-scripts-<?php echo $multi_name_row; ?>" class="lm-tab-2"><?php echo $entry_seolang_scripts; ?></a>
						</div>

						<div id="tab-multi-popup-<?php echo $multi_name_row; ?>">


							<div style="clear: both;" class="flex200">
								<div>
									
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_mova_widget_title_status; ?>
									</div>
									
									
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[comp][title_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['comp']['title_status']) && $seolang_settings_store_widgets_data[$multi_name]['comp']['title_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1" <?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['comp']['title_status'])) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_enabled; ?></option>
												<option value="0" <?php if (isset($seolang_settings_store_widgets_data[$multi_name]['comp']['title_status']) && !$seolang_settings_store_widgets_data[$multi_name]['comp']['title_status']) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
										<div class="lm-no-animation">
											<?php echo $language->get('entry_seolang_widget_mova_device_comp'); ?>
										</div>										
									</div>
									<div>&nbsp;&nbsp;&nbsp;&nbsp;</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[mobile][title_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['title_status']) && $seolang_settings_store_widgets_data[$multi_name]['mobile']['title_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1" <?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['title_status'])) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_enabled; ?></option>
												<option value="0" <?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['title_status']) && !$seolang_settings_store_widgets_data[$multi_name]['mobile']['title_status']) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
										<div class="lm-no-animation">
											<?php echo $language->get('entry_seolang_widget_mova_device_mobile'); ?>
										</div>										
									</div>									
								
								</div>

							</div>



							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_mova_widget_footer_status; ?>
									</div>
									
									<div class="input-group lm-select-toggle">
									
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[comp][footer_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['comp']['footer_status']) && $seolang_settings_store_widgets_data[$multi_name]['comp']['footer_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1" <?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['comp']['footer_status'])) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_enabled; ?></option>
												<option value="0" <?php if (isset($seolang_settings_store_widgets_data[$multi_name]['comp']['footer_status']) && !$seolang_settings_store_widgets_data[$multi_name]['comp']['footer_status']) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
										<div class="lm-no-animation">
											<?php echo $language->get('entry_seolang_widget_mova_device_comp'); ?>
										</div>											
									</div>
									
									<div>&nbsp;&nbsp;&nbsp;&nbsp;</div>

									<div class="input-group lm-select-toggle">
									
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[mobile][footer_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['footer_status']) && $seolang_settings_store_widgets_data[$multi_name]['mobile']['footer_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1" <?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['footer_status'])) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_enabled; ?></option>
												<option value="0" <?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['footer_status']) && !$seolang_settings_store_widgets_data[$multi_name]['mobile']['footer_status']) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
										
										<div class="lm-no-animation">
											<?php echo $language->get('entry_seolang_widget_mova_device_mobile'); ?>
										</div>											
									</div>

								</div>
							</div>

							
							<div style="clear: both;" class="flex200">
								<div>
									
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_position'); ?>
									</div>


									<div class="input-group">

										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[comp][position]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['comp']['position']) && $seolang_settings_store_widgets_data[$multi_name]['comp']['position'] != '') {
																																																	echo $seolang_settings_store_widgets_data[$multi_name]['comp']['position'];
																																																} else {
																																																	echo '';
																																																} ?>">
										<div>
											<?php echo $language->get('entry_seolang_widget_mova_device_comp'); ?>
										</div>	
								</div>

									<div>&nbsp;&nbsp;&nbsp;&nbsp;</div>																																							

									<div class="input-group">

										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[mobile][position]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['position']) && $seolang_settings_store_widgets_data[$multi_name]['mobile']['position'] != '') {
																																																	echo $seolang_settings_store_widgets_data[$multi_name]['mobile']['position'];
																																																} else {
																																																	echo '';
																																																} ?>">
										<div>
											<?php echo $language->get('entry_seolang_widget_mova_device_mobile'); ?>
										</div>									
								</div>


								</div>
							</div>


							<div style="clear: both;" class="flex200">
								<div>

									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_window_width'); ?>
									</div>
									<div class="input-group">

										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[comp][window_width]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['comp']['window_width']) && $seolang_settings_store_widgets_data[$multi_name]['comp']['window_width'] != '') {
																																																		echo $seolang_settings_store_widgets_data[$multi_name]['comp']['window_width'];
																																																	} else {
																																																		echo '';
																																																	} ?>">
										<div>
											<?php echo $language->get('entry_seolang_widget_mova_device_comp'); ?>
										</div>									
									</div>
									
									<div>&nbsp;&nbsp;&nbsp;&nbsp;</div>	

									<div class="input-group">

										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[mobile][window_width]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile']['window_width']) && $seolang_settings_store_widgets_data[$multi_name]['mobile']['window_width'] != '') {
																																																		echo $seolang_settings_store_widgets_data[$multi_name]['mobile']['window_width'];
																																																	} else {
																																																		echo '';
																																																	} ?>">
										<div>
											<?php echo $language->get('entry_seolang_widget_mova_device_mobile'); ?>
										</div>
									</div>



								</div>
							</div>



							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_window_opacity'); ?>
									</div>
									<div class="input-group">
										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[window_opacity]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['window_opacity']) && $seolang_settings_store_widgets_data[$multi_name]['window_opacity'] != '') {
																																																		echo $seolang_settings_store_widgets_data[$multi_name]['window_opacity'];
																																																	} else {
																																																		echo '';
																																																	} ?>">
									</div>
								</div>
							</div>


							<div style="clear: both;" class="flex200">
								<div>

									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_redirect; ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[redirect]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['redirect']) && $seolang_settings_store_widgets_data[$multi_name]['redirect']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>

								</div>
							</div>


							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_mova_widget_dark_back'); ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[dark_back]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['dark_back']) && $seolang_settings_store_widgets_data[$multi_name]['dark_back']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1" <?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['dark_back'])) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_enabled; ?></option>
												<option value="0" <?php if (isset($seolang_settings_store_widgets_data[$multi_name]['dark_back']) && !$seolang_settings_store_widgets_data[$multi_name]['dark_back']) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>

							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">

										<?php echo $language->get('entry_seolang_widget_mova_current_store_id'); ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[current_store_id]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['current_store_id']) && $seolang_settings_store_widgets_data[$multi_name]['current_store_id']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>

							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">

										<?php echo $language->get('entry_seolang_widget_mova_pointer'); ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[pointer]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['pointer']) && $seolang_settings_store_widgets_data[$multi_name]['pointer']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>




						</div>



						<div id="tab-multi-other-<?php echo $multi_name_row; ?>">


							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_langswitch_replace; ?>
									</div>

									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[langswitch_replace]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['langswitch_replace']) && $seolang_settings_store_widgets_data[$multi_name]['langswitch_replace']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1" <?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['langswitch_replace'])) { ?> selected="selected" <?php } ?>><?php echo $text_seolang_enabled; ?></option>
												<option value="0" <?php if (isset($seolang_settings_store_widgets_data[$multi_name]['langswitch_replace']) && !$seolang_settings_store_widgets_data[$multi_name]['langswitch_replace']) { ?>selected="selected" <?php } ?>><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>

							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_template'); ?>
									</div>
									<div class="input-group">
										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[template]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['template']) && $seolang_settings_store_widgets_data[$multi_name]['template'] != '') {
																																																	echo $seolang_settings_store_widgets_data[$multi_name]['template'];
																																																} else {
																																																	echo '';
																																																} ?>">
										<script type="text/javascript">
											$('input[name=\'<?php echo $multi_name; ?>[template]\']').autocomplete({
												minLength: 0,
												autoFocus: true,
												delay: 0,
												search: '',
												'source': function(request, response) {
													$.ajax({
														type: 'POST',
														url: 'index.php?route=seolang/mova/mova/autotemplate&<?php echo $token_name; ?>=<?php echo $token; ?>&filter_name=' + $('input[name=\'<?php echo $multi_name; ?>[template]\']').val(),
														dataType: 'json',
														data: {
															'langswitch_replace': $('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\']').find(":selected").val()
														},
														error: function() {
															console.log('Error request');
														},

														success: function(json) {
															/*
															json.unshift({
																name: '',
																label: ''
															});
															*/
															response($.map(json, function(item) {
																return {
																	label: item['label'],
																	value: item['name']
																}
															}));
														}
													});
												},
												'select': function(item) {
													$('input[name=\'<?php echo $multi_name; ?>[template]\']').val(item['value']);
												}
											});
											<?php if (SC_VERSION < 20) { ?>
												$('body').on('click', 'input[name=\'<?php echo $multi_name; ?>[template]\']', function() {
													$(this).trigger("keydown", {
														which: 80
													});
												});
											<?php } ?>
										</script>
									</div>
								</div>
							</div>



							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_cookie'); ?>
									</div>
									<div class="input-group">
										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[cookie]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['cookie']) && $seolang_settings_store_widgets_data[$multi_name]['cookie'] != '') {
																																																echo $seolang_settings_store_widgets_data[$multi_name]['cookie'];
																																															} else {
																																																echo '';
																																															} ?>">
									</div>
								</div>
							</div>

							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_cookie_set'); ?>
									</div>
									<div class="input-group">
										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[cookie_set]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['cookie_set']) && $seolang_settings_store_widgets_data[$multi_name]['cookie_set'] != '') {
																																																	echo $seolang_settings_store_widgets_data[$multi_name]['cookie_set'];
																																																} else {
																																																	echo '';
																																																} ?>">
									</div>
								</div>
							</div>


							<script type="text/javascript">
								$('input[name=\'<?php echo $multi_name; ?>[cookie]\']').autocomplete({
									minLength: 0,
									delay: 0,
									search: '',
									'source': function(request, response) {

										$.ajax({
											type: 'POST',
											url: 'index.php?route=seolang/mova/mova/autocookie&<?php echo $token_name; ?>=<?php echo $token; ?>&filter_name=',
											dataType: 'json',
											data: {
												'langswitch_replace': $('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\']').find(":selected").val(),
												'cookie_auto': $('input[name=\'<?php echo $multi_name; ?>[cookie_auto]\']').val()
											},
											error: function() {
												console.log('Error request');
											},

											success: function(json) {

												response($.map(json, function(item) {
													return {
														label: item['name'],
														value: item['name']
													}
												}));
											}
										});
									},
									'select': function(item) {
										$('input[name=\'<?php echo $multi_name; ?>[cookie]\']').val(item['value']);

									}
								});
								<?php if (SC_VERSION < 20) { ?>
									$('body').on('click', 'input[name=\'<?php echo $multi_name; ?>[cookie]\']', function() {
										$(this).trigger("keydown", {
											which: 80
										});
									});
								<?php } ?>
							</script>

							<script type="text/javascript">
								$('input[name=\'<?php echo $multi_name; ?>[cookie_set]\']').autocomplete({
									minLength: 0,
									delay: 0,
									search: '',
									'source': function(request, response) {

										$.ajax({
											type: 'POST',
											url: 'index.php?route=seolang/mova/mova/autocookie&<?php echo $token_name; ?>=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
											dataType: 'json',
											data: {
												'langswitch_replace': $('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\']').find(":selected").val(),
												'cookie_auto': $('input[name=\'<?php echo $multi_name; ?>[cookie_auto]\']').val()
											},
											error: function() {
												console.log('Error request');
											},

											success: function(json) {

												response($.map(json, function(item) {
													return {
														label: item['name'],
														value: item['name']
													}
												}));
											}
										});
									},
									'select': function(item) {
										$('input[name=\'<?php echo $multi_name; ?>[cookie_set]\']').val(item['value']);
									}
								});
								<?php if (SC_VERSION < 20) { ?>
									$('body').on('click', 'input[name=\'<?php echo $multi_name; ?>[cookie_set]\']', function() {
										$(this).trigger("keydown", {
											which: 80
										});
									});
								<?php } ?>
							</script>



							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_image_status'); ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[image_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['image_status']) && $seolang_settings_store_widgets_data[$multi_name]['image_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>



							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_autoredirect; ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[autoredirect]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['autoredirect']) && $seolang_settings_store_widgets_data[$multi_name]['autoredirect']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>


							<script>
								$(document).ready(function() {

									$('select[name=\'<?php echo $multi_name; ?>[autoredirect]\']').change(function() {
										if ($('select[name=\'<?php echo $multi_name; ?>[autoredirect]\']').find(":selected").val() == 1) {
											$('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\'] option[value=0]').prop('selected', true);
										}
									});

									$('select[name=\'<?php echo $multi_name; ?>[bots_status]\']').change(function() {
										if ($('select[name=\'<?php echo $multi_name; ?>[bots_status]\']').find(":selected").val() == 1) {
											$('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\'] option[value=0]').prop('selected', true);
										}
									});

									$('select[name=\'<?php echo $multi_name; ?>[redirect]\']').change(function() {
										if ($('select[name=\'<?php echo $multi_name; ?>[redirect]\']').find(":selected").val() == 1) {
											$('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\'] option[value=0]').prop('selected', true);
										}
									});

									$('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\']').change(function() {
										if ($('select[name=\'<?php echo $multi_name; ?>[langswitch_replace]\']').find(":selected").val() == 1) {
											$('select[name=\'<?php echo $multi_name; ?>[autoredirect]\'] option[value=0]').prop('selected', true);
											$('select[name=\'<?php echo $multi_name; ?>[bots_status]\'] option[value=0]').prop('selected', true);
											$('select[name=\'<?php echo $multi_name; ?>[redirect]\'] option[value=0]').prop('selected', true);
										}
									});
								});
							</script>


							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_bots_status; ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[bots_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['bots_status']) && $seolang_settings_store_widgets_data[$multi_name]['bots_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>




							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_ex_gets_status; ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[ex_gets_status]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['ex_gets_status']) && $seolang_settings_store_widgets_data[$multi_name]['ex_gets_status']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>





							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $entry_seolang_autoredirect_langs_ex; ?>
									</div>

									<div>
										<div class="scrollbox" style="text-align: left; width: 100% !important; height: 100% !important; ">
											<?php $class = 'odd'; ?>
											<?php foreach ($languages as $lang) { ?>
												<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
												<div class="<?php echo $class; ?>">
													<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['autoredirect_langs_ex']) && in_array($lang['language_id'], $seolang_settings_store_widgets_data[$multi_name]['autoredirect_langs_ex'])) { ?>
														<input type="checkbox" name="<?php echo $multi_name; ?>[autoredirect_langs_ex][]" value="<?php echo $lang['language_id']; ?>" checked="checked" />
														<?php echo $lang['name']; ?>
													<?php } else { ?>
														<input type="checkbox" name="<?php echo $multi_name; ?>[autoredirect_langs_ex][]" value="<?php echo $lang['language_id']; ?>" />
														<?php echo $lang['name']; ?>
													<?php } ?>
												</div>
											<?php } ?>

										</div>
										<a onclick="$(this).parent().find(':checkbox').prop('checked', true);" class="nohref"><?php echo $language->get('text_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);" class="nohref"><?php echo $language->get('text_unselect_all'); ?></a>
									</div>

								</div>
							</div>




							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_cookie_auto'); ?>
									</div>
									<div class="input-group">
										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[cookie_auto]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['cookie_auto']) && $seolang_settings_store_widgets_data[$multi_name]['cookie_auto'] != '') {
																																																		echo $seolang_settings_store_widgets_data[$multi_name]['cookie_auto'];
																																																	} else {
																																																		echo '';
																																																	} ?>">
									</div>
								</div>
							</div>

							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">
										<?php echo $language->get('entry_seolang_widget_mova_cookie_auto_days'); ?>
									</div>
									<div class="input-group">
										<input <?php if (SC_VERSION < 20) { ?><?php } else { ?>type="text" <?php } ?> class="form-control" name="<?php echo $multi_name; ?>[cookie_auto_days]" value="<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['cookie_auto_days']) && $seolang_settings_store_widgets_data[$multi_name]['cookie_auto_days'] != '') {
																																																			echo $seolang_settings_store_widgets_data[$multi_name]['cookie_auto_days'];
																																																		} else {
																																																			echo '';
																																																		} ?>">
									</div>
								</div>
							</div>


							<div style="clear: both;" class="flex200">
								<div>
									<div class="seolang-margin-top-6">

										<?php echo $language->get('entry_seolang_widget_mova_mobile_detect'); ?>
									</div>
									<div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[mobile_detect]">
											<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['mobile_detect']) && $seolang_settings_store_widgets_data[$multi_name]['mobile_detect']) { ?>
												<option value="0"><?php echo $text_seolang_disabled; ?></option>
												<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
											<?php } else { ?>
												<option value="1"><?php echo $text_seolang_enabled; ?></option>
												<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>


						</div>


						<div id="tab-multi-html-<?php echo $multi_name_row; ?>">

							<div class="seolang-margin-top-6" style="clear: both;">
								<div id="tabs-multi-<?php echo $multi_name_row; ?>" class="htabs">
									<?php
									foreach ($languages as $lang) {
									?>
										<a href="#tab-lang-<?php echo $multi_name_row; ?>-<?php echo $lang['language_id']; ?>" class="lm-tab-2"><?php echo strtoupper($lang['code_short']); ?> <img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></a>
									<?php } ?>
								</div>

								<?php
								foreach ($languages as $lang) {
								?>
									<div id="tab-lang-<?php echo $multi_name_row; ?>-<?php echo $lang['language_id']; ?>">

										<div class="seolang-margin-top-6">
											<?php echo $entry_seolang_mova_widget_title; ?>
										</div>

										<div class="input-group">
											<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?><br><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></span>
											<textarea class="form-control" cols="50" rows="1" name="<?php echo $multi_name; ?>[title][<?php echo $lang['language_id']; ?>]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['title'][$lang['language_id']]) && $seolang_settings_store_widgets_data[$multi_name]['title'][$lang['language_id']] != '') {
																																												echo $seolang_settings_store_widgets_data[$multi_name]['title'][$lang['language_id']];
																																											} else {
																																												echo '';
																																											} ?></textarea>
										</div>



										<div class="seolang-margin-top-6">
											<?php echo $entry_seolang_mova_widget_html; ?>
										</div>
										<div class="input-group">
											<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?><br><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></span>
											<textarea class="form-control" cols="50" rows="3" name="<?php echo $multi_name; ?>[html][<?php echo $lang['language_id']; ?>]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['html'][$lang['language_id']]) && $seolang_settings_store_widgets_data[$multi_name]['html'][$lang['language_id']] != '') {
																																												echo $seolang_settings_store_widgets_data[$multi_name]['html'][$lang['language_id']];
																																											} else {
																																												echo '';
																																											} ?></textarea>
										</div>


										<div class="seolang-margin-top-6">
											<?php echo $entry_seolang_mova_widget_lang_name; ?>
										</div>
										<div class="input-group">
											<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?><br><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></span>
											<textarea class="form-control" cols="50" rows="1" name="<?php echo $multi_name; ?>[lang_name][<?php echo $lang['language_id']; ?>]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['lang_name'][$lang['language_id']]) && $seolang_settings_store_widgets_data[$multi_name]['lang_name'][$lang['language_id']] != '') {
																																													echo $seolang_settings_store_widgets_data[$multi_name]['lang_name'][$lang['language_id']];
																																												} else {
																																													echo '';
																																												} ?></textarea>
										</div>


										<div class="seolang-margin-top-6">
											<?php echo $entry_seolang_mova_widget_lm_text_close; ?>
										</div>
										<div class="input-group">
											<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?><br><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></span>
											<textarea class="form-control" cols="50" rows="1" name="<?php echo $multi_name; ?>[lm_text_close][<?php echo $lang['language_id']; ?>]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['lm_text_close'][$lang['language_id']]) && $seolang_settings_store_widgets_data[$multi_name]['lm_text_close'][$lang['language_id']] != '') {
																																														echo $seolang_settings_store_widgets_data[$multi_name]['lm_text_close'][$lang['language_id']];
																																													} else {
																																														echo '';
																																													} ?></textarea>
										</div>


										<div class="seolang-margin-top-6">
											<?php echo $entry_seolang_mova_widget_code_custom; ?>
										</div>
										<div class="input-group">
											<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?><br><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></span>
											<textarea class="form-control" cols="50" rows="3" name="<?php echo $multi_name; ?>[code_custom][<?php echo $lang['language_id']; ?>]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['code_custom'][$lang['language_id']]) && $seolang_settings_store_widgets_data[$multi_name]['code_custom'][$lang['language_id']] != '') {
																																														echo $seolang_settings_store_widgets_data[$multi_name]['code_custom'][$lang['language_id']];
																																													} else {
																																														echo '';
																																													} ?></textarea>
										</div>






									</div>
								<?php } ?>




							</div>

						</div>




						<div id="tab-multi-scripts-<?php echo $multi_name_row; ?>">





							<div style="clear: both;">
								<div class="seolang-margin-top-6">
									<?php echo strip_tags($entry_seolang_widget_mova_anchor); ?>
								</div>

								<div class="input-group">
									<span class="input-group-addon"><?php echo $entry_seolang_widget_mova_anchor; ?></span>
									<textarea class="form-control" cols="50" rows="3" name="<?php echo $multi_name; ?>[anchor]" id="<?php echo $multi_name; ?>_anchor"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['anchor']) && $seolang_settings_store_widgets_data[$multi_name]['anchor'] != '') {
																																											echo $seolang_settings_store_widgets_data[$multi_name]['anchor'];
																																										} else {
																																											echo '';
																																										} ?></textarea>
								</div>

								<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['anchor_templates']) && is_array($seolang_settings_store_widgets_data[$multi_name]['anchor_templates']) && !empty($seolang_settings_store_widgets_data[$multi_name]['anchor_templates'])) { ?>

									<div>
										<?php echo $language->get('entry_seolang_widget_mova_anchor_templates'); ?>
									</div>

									<div>
										<div class="input-group"><select class="form-control" name="<?php echo $multi_name; ?>[anchor_templates]" id="<?php echo $multi_name; ?>_anchor_templates">

												<?php if (!isset($seolang_settings_store_widgets_data[$multi_name]['anchor'])) {
													$seolang_settings_store_widgets_data[$multi_name]['anchor'] = '';
												} ?>
												<option value="<?php echo $seolang_settings_store_widgets_data[$multi_name]['anchor']; ?>"><?php echo $language->get('entry_seolang_widget_mova_anchor_value'); ?></option>

												<?php foreach ($seolang_settings_store_widgets_data[$multi_name]['anchor_templates'] as $anchor_name => $anchor_template) { ?>
													<option value="<?php echo $anchor_template; ?>"><?php echo $anchor_name; ?></option>
												<?php } ?>

											</select></div>
									</div>
									<script>
										$('#<?php echo $multi_name; ?>_anchor_templates')
											.change(function() {
												var str = '';
												$('#<?php echo $multi_name; ?>_anchor_templates option:selected').each(function() {
													str = $(this).val();
												});

												$('#<?php echo $multi_name; ?>_anchor').html(str);

											});
									</script>

								<?php } ?>



							</div>

							<div style="clear: both;">
								<div class="seolang-margin-top-6">
									<?php echo $entry_seolang_widget_mova_doc_ready; ?>
								</div>
								<div class="input-group lm-select-toggle">
									<select class="form-control lm-select-switch" name="<?php echo $multi_name; ?>[doc_ready]">
										<?php if (isset($seolang_settings_store_widgets_data[$multi_name]['doc_ready']) && $seolang_settings_store_widgets_data[$multi_name]['doc_ready']) { ?>
											<option value="0"><?php echo $text_seolang_disabled; ?></option>
											<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
										<?php } else { ?>
											<option value="1"><?php echo $text_seolang_enabled; ?></option>
											<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div style="clear: both;">
								<div class="seolang-margin-top-6">
									<?php echo strip_tags($entry_seolang_widget_mova_reserved); ?>
								</div>
								<div class="input-group">
									<span class="input-group-addon"><?php echo $entry_seolang_widget_mova_reserved; ?></span>
									<textarea style="width: 20%;" class="form-control" cols="20" rows="2" name="<?php echo $multi_name; ?>[reserved]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['reserved']) && $seolang_settings_store_widgets_data[$multi_name]['reserved'] != '') {
																																							echo $seolang_settings_store_widgets_data[$multi_name]['reserved'];
																																						} else {
																																							echo '';
																																						} ?></textarea>
								</div>

							</div>


						</div>

					</td>

				</tr>
			</table>

		</div>

		<?php include('tab_layouts.tpl'); ?>
		<?php include('tab_access.tpl'); ?>

		<script>
			$('#tabs-tab-multi<?php echo $multi_name_row; ?> > a').tabs();
			$('#tabs-tab-multi-<?php echo $multi_name_row; ?> > a').tabs();
			$('#tabs-multi-<?php echo $multi_name_row; ?> a').tabs();
		</script>

	</div>
<?php

	$multi_name_row++;
} else {
?>


<?php
}
?>
<script>
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
	function set_cmswidget_id() {
		let cmswidget_array = [];
		let ki;
		let cmswidget_id;
		let seolang_cmswidget_val;

		$('.seolang_cmswidget_settings').each(function(index) {
			seolang_cmswidget_val = $(this).find('.seolang_cmswidget').val();

			cmswidget_array.push(Number(seolang_cmswidget_val));
		});

		for (let i = Number(Math.max.apply(null, cmswidget_array)); i > 0; i--) {
			if (cmswidget_array.includes(i) == false) {
				ki = i;
			}
		}

		if (ki > 0) {
			cmswidget_id = Number(ki);
		} else {
			cmswidget_id = Number(Math.max.apply(null, cmswidget_array) + 1);
		}

		$('.seolang_cmswidget_settings').each(function(index) {
			if ($(this).find('.seolang_cmswidget').val() == '') {
				$(this).find('.seolang_cmswidget').val(cmswidget_id);
				$(this).find('.seolang_name').val($(this).find('.seolang_name').val().replace('<?php echo $prefix; ?>', '') + cmswidget_id);
				$(this).find('.seolang_name_id').text(cmswidget_id);
				console.log(cmswidget_id);
			}
		});
	}
	set_cmswidget_id();
</script>

<?php
if (isset($add_multi) && $add_multi) {
?>
	<script>
		customTogglesMulti = document.querySelectorAll('#tab-multi<?php echo $multi_name_row - 1; ?> .lm-select-toggle');
		add_switcher(customTogglesMulti);
	</script>
<?php
}
?>