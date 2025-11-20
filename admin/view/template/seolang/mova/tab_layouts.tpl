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
					<div class="seolang-flex-5">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_layouts'); ?></span>
					</div>
					<div class="seolang-flex-5">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_position'); ?></span>
					</div>
					<div class="seolang-flex-5">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_uri'); ?></span>
					</div>

					<div class="seolang-flex-5">
						<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('text_seolang_device'); ?></span>
					</div>
					
					<div class="seolang-flex-5">
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
					<div class="seolang-flex-5">
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
					

					<div class="seolang-flex-5">
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

					<div class="seolang-flex-5">
						<div style="width: 100%;">

							<div class="input-group" style="width: 100%;">
								<input type="text" class="form-control" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][uri]" id="seolang_settings_<?php echo $store_id ?>_multi_<?php echo $multi_name; ?>_uri" value="<?php if (isset($seolang_settings_store['multi'][$multi_name]['uri'])) {
																																																																echo $seolang_settings_store['multi'][$multi_name]['uri'];
																																																															} else {
																																																																echo '';
																																																															} ?>">
							</div>
							<div class="seolang-margin-top-6">
								<?php echo $language->get('text_seolang_uri'); ?>
							</div>
							<div class="input-group lm-select-toggle">
								<select class="form-control lm-select-switch" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][uri_template]">
									<?php if (isset($seolang_settings_store['multi'][$multi_name]['uri_template']) && $seolang_settings_store['multi'][$multi_name]['uri_template']) { ?>
										<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
										<option value="0"><?php echo $text_seolang_disabled; ?></option>
									<?php } else { ?>
										<option value="1"><?php echo $text_seolang_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="seolang-margin-top-6">
								<?php echo $language->get('text_seolang_uri_template'); ?>
							</div>

						</div>

					</div>


					<div class="seolang-flex-5">

						<div class="input-group jetcache-text-center">
							<select class="form-control sc_select_other" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][device]">
								<!-- all -->	
								<option value="0" <?php if (isset($seolang_settings_store['multi'][$multi_name]['device']) && (int)$seolang_settings_store['multi'][$multi_name]['device'] == 0) { ?>selected="selected"<?php } ?>><?php echo $language->get('text_seolang_device_all'); ?></option>
								
								<!-- comp -->
								<option value="1" <?php if (isset($seolang_settings_store['multi'][$multi_name]['device']) && (int)$seolang_settings_store['multi'][$multi_name]['device'] == 1) { ?>selected="selected"<?php } ?>><?php echo $language->get('text_seolang_device_comp'); ?></option>
								
								<!-- mobile -->
								<option value="2" <?php if (isset($seolang_settings_store['multi'][$multi_name]['device']) && (int)$seolang_settings_store['multi'][$multi_name]['device'] == 2) { ?>selected="selected"<?php } ?>><?php echo $language->get('text_seolang_device_mob'); ?></option>
								
								<!-- smart -->
								<option value="3" <?php if (isset($seolang_settings_store['multi'][$multi_name]['device']) && (int)$seolang_settings_store['multi'][$multi_name]['device'] == 3) { ?>selected="selected"<?php } ?>><?php echo $language->get('text_seolang_device_smart'); ?></option>
								
								<!-- pad -->
								<option value="4" <?php if (isset($seolang_settings_store['multi'][$multi_name]['device']) && (int)$seolang_settings_store['multi'][$multi_name]['device'] == 4) { ?>selected="selected"<?php } ?>><?php echo $language->get('text_seolang_device_pad'); ?></option>
							</select>
						</div>

					</div>



					<div class="seolang-flex-5">

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