<script>
	tab_options_widget_html = $('#tabs-options-widgets').html();

	tab_options_widget_html = tab_options_widget_html + '<a href="#tab-options-<?php echo $widget; ?>" class="lm-tab-2"><?php echo $language->get('entry_seolang_widget_' . $widget); ?></a>';

	$('#tabs-options-widgets').html(tab_options_widget_html);
</script>

<div id="tab-options-<?php echo $widget; ?>">


	<div class="sh-block-layouts" style="
					display: flex;
					flex-wrap: wrap;
					align-content: space-between;
					justify-content: space-between;
					width: 100%;
					margin-top: 4px; margin-bottom: 8px;
					background-color: #F4F6F7;
					padding: 8px;
					">
		<div>

			<div style="
									display: flex;
									flex-wrap: wrap;
									align-content: space-between;
									justify-content: space-between;
									width: 100%;
									margin-top: 4px; margin-bottom: 8px;
									">

				<div style="display: flex; align-items: center;">
					<div style="width: 160px;">
						<?php echo $language->get('text_seolang_status'); ?> <?php echo $language->get('entry_seolang_widget_' . $widget); ?>&nbsp;
					</div>
					<div class="input-group lm-select-toggle">
						<select class="form-control lm-select-switch" name="seolang_settings[widget_<?php echo $widget; ?>_status]">
							<?php if (isset($seolang_settings['widget_' . $widget . '_status']) && $seolang_settings['widget_' . $widget . '_status']) { ?>
								<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
								<option value="0"><?php echo $text_seolang_disabled; ?></option>
							<?php } else { ?>
								<option value="1"><?php echo $text_seolang_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

			</div>




		</div>


	</div>


	<div style="clear: both;">
		<div class="seolang-margin-top-16">
			<?php echo strip_tags($entry_seolang_mova_bots); ?>
		</div>
		<div class="input-group">
			<span class="input-group-addon"><?php echo $text_seolang_mova_bots; ?></span>
			<textarea class="form-control" cols="50" rows="8" name="seolang_settings[bots]"><?php if (isset($seolang_settings['bots']) && $seolang_settings['bots'] != '') {
																								echo $seolang_settings['bots'];
																							} else {
																								echo '';
																							} ?></textarea>
		</div>
	</div>


	<div style="clear: both;">
		<div>
			<div class="seolang-margin-top-16">
				<?php echo $language->get('entry_seolang_mova_bots_delay_mobile'); ?>
			</div>
			<div class="input-group">
				<input type="text" class="form-control" name="seolang_settings[bots_delay_mobile]" value="<?php if (isset($seolang_settings['bots_delay_mobile']) && $seolang_settings['bots_delay_mobile'] != '') {
																												echo $seolang_settings['bots_delay_mobile'];
																											} else {
																												echo '';
																											} ?>">
			</div>
		</div>
	</div>
	<div style="clear: both;">
		<div>
			<div class="seolang-margin-top-16">
				<?php echo $language->get('entry_seolang_mova_bots_delay_desktop'); ?>
			</div>
			<div class="input-group">
				<input type="text" class="form-control" name="seolang_settings[bots_delay_desktop]" value="<?php if (isset($seolang_settings['bots_delay_desktop']) && $seolang_settings['bots_delay_desktop'] != '') {
																												echo $seolang_settings['bots_delay_desktop'];
																											} else {
																												echo '';
																											} ?>">
			</div>
		</div>
	</div>



	<div style="clear: both;">
		<div class="seolang-margin-top-16">
			<?php echo $language->get('entry_seolang_mova_cache_diff'); ?>
		</div>
		<div class="input-group lm-select-toggle">
			<select class="form-control lm-select-switch" name="seolang_settings[cache_diff]">
				<?php if (isset($seolang_settings['cache_diff']) && $seolang_settings['cache_diff']) { ?>
					<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
					<option value="0"><?php echo $text_seolang_disabled; ?></option>
				<?php } else { ?>
					<option value="1"><?php echo $text_seolang_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<?php if (SC_VERSION > 23) { ?>
		<div style="clear: both;">
			<div class="seolang-margin-top-16">
				<?php echo $language->get('entry_seolang_mova_equal'); ?>
			</div>
			<div class="input-group lm-select-toggle">
				<select class="form-control lm-select-switch" name="seolang_settings[mova_equal]">
					<?php if (isset($seolang_settings['mova_equal']) && $seolang_settings['mova_equal']) { ?>
						<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
						<option value="0"><?php echo $text_seolang_disabled; ?></option>
					<?php } else { ?>
						<option value="1"><?php echo $text_seolang_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if (SC_VERSION > 23) { ?>
		<div style="clear: both;">
			<div class="seolang-margin-top-16">
				<?php echo $language->get('entry_seolang_mova_onekeyword'); ?>
			</div>
			<div class="input-group lm-select-toggle">
				<select class="form-control lm-select-switch" name="seolang_settings[mova_onekeyword]">
					<?php if (isset($seolang_settings['mova_onekeyword']) && $seolang_settings['mova_onekeyword']) { ?>
						<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
						<option value="0"><?php echo $text_seolang_disabled; ?></option>
					<?php } else { ?>
						<option value="1"><?php echo $text_seolang_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>



	<div style="clear: both;">
		<div class="seolang-margin-top-16">
			<?php echo $entry_seolang_mova_ex_gets; ?>
		</div>
		<div class="input-group">
			<span class="input-group-addon"><?php echo $text_seolang_mova_ex_gets; ?></span>
			<textarea class="form-control" cols="50" rows="8" name="seolang_settings[ex_gets]"><?php if (isset($seolang_settings['ex_gets']) && $seolang_settings['ex_gets'] != '') {
																									echo $seolang_settings['ex_gets'];
																								} else {
																									echo '';
																								} ?></textarea>
		</div>
	</div>




	<div style="clear: both;">
		<div class="seolang-margin-top-16">
			<?php echo $language->get('entry_seolang_mova_debug'); ?>
		</div>
		<div class="input-group lm-select-toggle">
			<select class="form-control lm-select-switch" name="seolang_settings[debug]">
				<?php if (isset($seolang_settings['debug']) && $seolang_settings['debug']) { ?>
					<option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
					<option value="0"><?php echo $text_seolang_disabled; ?></option>
				<?php } else { ?>
					<option value="1"><?php echo $text_seolang_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
				<?php } ?>
			</select>
		</div>
	</div>



</div>