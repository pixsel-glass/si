<?php

if (!isset($multi_name_row)) {
	$multi_name_row = 0;
}

if (!empty($seolang_settings_store['multi'])) {
?>

	<div id="tab-multi<?php echo $multi_name_row; ?>">
		<div style="clear: both; height: 1px; line-height: 1px;">&nbsp;</div>
		<div id="tabs-tab-multi<?php echo $multi_name_row; ?>" class="htabs">
			<a href="#tab-multi-options-<?php echo $multi_name_row; ?>" class="lm-tab"><?php echo $tab_text_seolang_options; ?></a>
			<a href="#tab-multi-position-<?php echo $multi_name_row; ?>" class="lm-tab"><?php echo $tab_text_seolang_position; ?></a>
			<a href="#tab-multi-access-<?php echo $multi_name_row; ?>" class="lm-tab"><?php echo $tab_text_seolang_access; ?></a>
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
									<?php echo $entry_seolang_add; ?> <i class="fa fa-plus" aria-hidden="true"></i></span>
								</a>
							</div>
						</div>

					</td>

				</tr>

				<tr>
					<td>

						<div style="clear: both; margin-top:10px;">
							<div>
								<?php echo $entry_seolang_status; ?>
							</div>
							<div class="input-group">
								<select class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][status]">
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


						<div style="clear: both; margin-top:5px;">
							<div>
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
								<span class="input-group-addon"></span>
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
								<input type="hidden" class="form-control seolang_cmswidget" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][cmswidget]" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['store_id'])) {
																																																		echo $seolang_settings_store['multi'][$multi_name]['store_id'];
																																																	} else {
																																																		echo '';
																																																	} ?>">
							</div>
						</div>



						<div style="clear: both; margin-top:10px;">
							<div>
								<?php echo $entry_seolang_html_description; ?>
							</div>

							<div id="tabs-multi-<?php echo $multi_name_row; ?>" class="htabs">
								<?php
								foreach ($languages as $lang) {
								?>
									<a href="#tab-lang-<?php echo $multi_name_row; ?>-<?php echo $lang['language_id']; ?>" class="lm-tab"><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"> <?php echo $lang['name']; ?></a>
								<?php } ?>
							</div>


							<?php
							foreach ($languages as $lang) {
							?>

								<div id="tab-lang-<?php echo $multi_name_row; ?>-<?php echo $lang['language_id']; ?>">

									<div class="input-group">
										<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?><br><img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>"></span>
										<textarea class="form-control" cols="50" rows="10" name="<?php echo $multi_name; ?>[description][<?php echo $lang['language_id']; ?>]"><?php if (isset($seolang_settings_store_widgets_data[$multi_name]['description'][$lang['language_id']]) && $seolang_settings_store_widgets_data[$multi_name]['description'][$lang['language_id']] != '') {
																																													echo $seolang_settings_store_widgets_data[$multi_name]['description'][$lang['language_id']];
																																												} else {
																																													echo '';
																																												} ?></textarea>

									</div>
								</div>

							<?php } ?>

							<script>
								$('#tabs-multi-<?php echo $multi_name_row; ?> a').tabs();
							</script>

						</div>





					</td>

				</tr>
			</table>

		</div>

		<div id="tab-multi-position-<?php echo $multi_name_row; ?>">
			<div>


				<div class="sh-block-layouts" style="
	     			display: flex;
	     			flex-wrap: wrap;
					align-content: center;
					justify-content: center;
					 text-align: center;
					width: 100%;
					margin-top: 4px; margin-bottom: 8px;
					 background-color: #F4F6F7;
					 padding: 8px;

					">
					<div style="width: 25%;">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_layouts'); ?></span>
					</div>
					<div style="width: 25%;">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_position'); ?></span>
					</div>
					<div style="width: 25%;">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_uri'); ?></span>
					</div>
					<div style="width: 25%;">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_sort'); ?></span>
					</div>


				</div>




				<div class="sh-block-layouts" style="
	     			display: flex;
	     			display: -webkit-flex;
	     			flex-wrap: wrap;
					align-content: center;
					justify-content: space-between;

	                width: 100%;
					margin-top: 4px; margin-bottom: 8px;
					">
					<div class="seolang-flex-4">
						<div style="width: 100%;">
							<div class="scrollbox" style="width: 100%; height: 150px;">
								<?php $class = 'odd'; ?>
								<?php
								foreach ($layouts as $layout) { ?>
									<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?php echo $class; ?>">
										<?php
										if (isset($seolang_settings_store['multi'][$multi_name]['layout_id']) && is_array($seolang_settings_store['multi'][$multi_name]['layout_id'])) {
											$module_array = $seolang_settings_store['multi'][$multi_name]['layout_id'];
											$module['layout_id'] = $module_array;
										} else {
											$module_array = $module['layout_id'] = array();
										}
										if ((isset($module['layout_id']) && is_array($module['layout_id'])) && in_array($layout['layout_id'], $module['layout_id'])) { ?>
											<input type="checkbox" class="sc_select_enable no_change" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][layout_id][]" value="<?php echo $layout['layout_id']; ?>" checked="checked" />
											<?php echo $layout['name']; ?>
										<?php } else { ?>
											<input type="checkbox" class="no_change" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][layout_id][]" value="<?php echo $layout['layout_id']; ?>" />
											<?php echo $layout['name']; ?>
										<?php } ?>
									</div>
								<?php } ?>
							</div>

							<a href="#" onclick="$(this).parent().find(':checkbox').prop('checked', true); return false;" class="nohref"><?php echo $language->get('text_seolang_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);" class="nohref"><?php echo $language->get('text_seolang_unselect_all'); ?></a>
						</div>
					</div>

					<div class="seolang-flex-4">
						<div>
							<div class="input-group">
								<select class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][position]">

									<?php foreach ($seolang_settings['position_type'] as $desc_position => $type_position) {

										if (isset($seolang_settings_store['multi'][$multi_name]['position'])) {
											$module_position = $seolang_settings_store['multi'][$multi_name]['position'];
										} else {
											$module_position = '';
										}

									?>
										<option value="<?php echo $type_position['type_id']; ?>" <?php if (isset($module_position) && $module_position == $type_position['type_id']) { ?> selected="selected" <?php } ?>><?php if (isset($type_position['title'][$config_language_id])) {
																																																								echo $type_position['title'][$config_language_id];
																																																							} else {
																																																								echo $type_position['type_id'];
																																																							} ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<div class="seolang-flex-4">
						<div style="width: 100%;">

							<div class="input-group" style="width: 100%;">
								<input type="text" class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][uri]" id="seolang_settings_<?php echo $store_id ?>_multi_<?php echo $multi_name; ?>_uri" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['uri'])) {
																																																																echo $seolang_settings_store['multi'][$multi_name]['uri'];
																																																															} else {
																																																																echo '';
																																																															} ?>">
							</div>
							<div style="margin-top: 2px; margin-bottom: 10px;">
								<?php echo $language->get('text_seolang_uri'); ?>
							</div>
							<div class="input-group">
								<select class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][uri_template]">
									<?php if (isset($seolang_settings_store['multi'][$multi_name]['uri_template']) && $seolang_settings_store['multi'][$multi_name]['uri_template']) { ?>
										<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
										<option value="0"><?php echo $text_seolang_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_seolang_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
							<div style="margin-top: 2px;">
								<?php echo $language->get('text_seolang_uri_template'); ?>
							</div>
						</div>
					</div>


					<div class="seolang-flex-4">

						<div class="input-group">
							<input type="text" class="form-control" style="width: 60px;" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][sort_order]" id="seolang_settings_<?php echo $store_id ?>_multi_<?php echo $multi_name; ?>_sort_order" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['sort_order'])) {
																																																																								echo $seolang_settings_store['multi'][$multi_name]['sort_order'];
																																																																							} else {
																																																																								echo '';
																																																																							} ?>">
						</div>

					</div>

				</div>


			</div>
		</div>


		<div id="tab-multi-access-<?php echo $multi_name_row; ?>">
			<table>
				<tr>
					<td><?php echo $language->get('entry_seolang_customer_groups'); ?></td>
					<td>
						<div class="scrollbox">
							<?php $class = 'even'; ?>
							<?php if (!isset($seolang_settings_store['multi'][$multi_name]['access']['customer_groups'])) { ?>
								<?php foreach ($customer_groups as $customer_group) { ?>
									<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?php echo $class; ?>">
										<input type="checkbox" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][access][customer_groups][]" value="<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
										<?php echo $customer_group['name']; ?>
									</div>
								<?php } ?>

							<?php } else { ?>

								<?php foreach ($customer_groups as $customer_group) { ?>
									<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?php echo $class; ?>">
										<?php if (isset($seolang_settings_store['multi'][$multi_name]['access']['customer_groups']) && in_array($customer_group['customer_group_id'], $seolang_settings_store['multi'][$multi_name]['access']['customer_groups'])) { ?>
											<input type="checkbox" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][access][customer_groups][]" value="<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
											<?php echo $customer_group['name']; ?>
										<?php } else { ?>
											<input type="checkbox" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][access][customer_groups][]" value="<?php echo $customer_group['customer_group_id']; ?>" />
											<?php echo $customer_group['name']; ?>
										<?php } ?>
									</div>
								<?php } ?>

							<?php } ?>
						</div>
						<a onclick="$(this).parent().find(':checkbox').prop('checked', true);" class="nohref"><?php echo $language->get('text_seolang_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);" class="nohref"><?php echo $language->get('text_seolang_unselect_all'); ?></a>
					</td>
					</td>
				</tr>

			</table>
		</div>



		<script>
			$('#tabs-tab-multi<?php echo $multi_name_row; ?> > a').tabs();
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
	function set_cmswidget_id() {
		let cmswidget_array = [];
		let ki;
		let cmswidget_id;
		let seolang_cmswidget_val;

		$('.seolang_cmswidget_settings').each(function(index) {
			seolang_cmswidget_val = $(this).find('.seolang_cmswidget').val();

			cmswidget_array.push(Number(seolang_cmswidget_val));
		});

		for (let i = Math.max.apply(null, cmswidget_array); i > 0; i--) {
			if (cmswidget_array.includes(i) == false) {
				ki = i;
			}
		}

		if (ki > 0) {
			cmswidget_id = ki;
		} else {
			cmswidget_id = Math.max.apply(null, cmswidget_array) + 1;
		}

		$('.seolang_cmswidget_settings').each(function(index) {
			if ($(this).find('.seolang_cmswidget').val() == '') {
				$(this).find('.seolang_cmswidget').val(cmswidget_id);
				$(this).find('.seolang_name').val($(this).find('.seolang_name').val() + cmswidget_id);
				console.log(cmswidget_id);
			}
		});
	}
	set_cmswidget_id();
</script>