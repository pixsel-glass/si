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
								<div style="display: flex; align-items: center; margin-bottom: 10px;">
								   <div style="width: 160px;">
								   	 <?php echo $entry_seolang_access; ?>&nbsp;
								   </div>
								   <div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="seolang_settings_<?php echo $store_id ?>[access]">
										   <?php if (isset($seolang_settings_store['access']) && $seolang_settings_store['access']) { ?>
										   <option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
										   <option value="0"><?php echo $text_seolang_disabled; ?></option>
										   <?php } else { ?>
										   <option value="1"><?php echo $text_seolang_enabled; ?></option>
										   <option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
										   <?php } ?>
										</select>
								   </div>
								</div>


								<div style="display: flex; align-items: center; margin-bottom: 10px;">
								   <div style="width: 160px;">
								   	 <?php echo $entry_seolang_menu_status; ?>&nbsp;
								   </div>
								   <div class="input-group lm-select-toggle">
										<select class="form-control lm-select-switch" name="seolang_settings[menu_status]" id="seolang_settings_menu_status">
										   <?php if (isset($seolang_settings['menu_status']) && $seolang_settings['menu_status']) { ?>
										   <option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
										   <option value="0"><?php echo $text_seolang_disabled; ?></option>
										   <?php } else { ?>
										   <option value="1"><?php echo $text_seolang_enabled; ?></option>
										   <option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
										   <?php } ?>
										</select>
								   </div>
								</div>


								<div id="block_seolang_settings_menu_order" style="display: flex; align-items: center; margin-bottom: 10px;">
								   <div style="min-width: 160px;">
								   	 <?php echo $entry_seolang_menu_order; ?>&nbsp;
								   </div>
									<div class="input-group">
										<span class="input-group-addon">#</span>
											<input type="text" style="width: 55px;" class="form-control" name="seolang_settings[menu_order]" id="seolang_settings_menu_order" value="<?php if (isset($seolang_settings['menu_order'])) { echo $seolang_settings['menu_order']; } else { echo ''; } ?>">
									</div>
								</div>

<script>
$('#seolang_settings_menu_status').change(function() {
    $('#block_seolang_settings_menu_order').hide();
    if ($('#seolang_settings_menu_status option:selected').val() == 1) {
    	$('#block_seolang_settings_menu_order').show();
    }
  })
  .trigger('change');
</script>


								<div style="display: flex; align-items: center; margin-bottom: 10px;">
								   <div style="width: 160px;">
								   	 <?php echo $language->get('entry_seolang_positions'); ?>&nbsp;
								   </div>

								   <div class="input-group">
										<a id="seolang_positions_show" onclick="$('#position-block').show(); $('#seolang_positions_show').hide(); $('#seolang_positions_hide').show(); return false;" class="markbuttono sc_button"><?php echo $language->get('entry_seolang_show'); ?></a>
										<a id="seolang_positions_hide" onclick="$('#position-block').hide(); $('#seolang_positions_hide').hide(); $('#seolang_positions_show').show();  return false;" class="markbuttono sc_button" style="display: none;"><?php echo $language->get('entry_seolang_hide'); ?></a>
								   </div>



								</div>

            <div id="position-block" style="display: none;">
		   <table id="position_types" class="list">
					   <thead>
				             <tr>
				                <td class="left"><?php echo $language->get('entry_seolang_position'); ?></td>
				                <td class="left"><?php echo $language->get('entry_seolang_position_controller'); ?></td>
				                <td class="left"><?php echo $language->get('entry_seolang_position_name'); ?></td>
				                <td><?php echo $language->get('entry_seolang_name'); ?></td>
				                <td></td>
				             </tr>

				      </thead>

				      <?php if (isset($seolang_settings['position_type']) && !empty($seolang_settings['position_type'])) { ?>
				      <?php

				      foreach ($seolang_settings['position_type'] as $position_type_id => $position_type) { ?>
				      <?php  $position_type_row = $position_type_id; ?>
				      <tbody id="position_type_row<?php echo $position_type_row; ?>">
				          <tr>
				               <td class="left">
								<div class="input-group">
								<input type="text" class="form-control" name="seolang_settings[position_type][<?php echo $position_type_id; ?>][type_id]" value="<?php if (isset($position_type['type_id'])) echo $position_type['type_id']; ?>" size="20">
				                </div>
				               </td>

				               <td class="left">
								<div class="input-group">
								<input type="text" class="form-control" name="seolang_settings[position_type][<?php echo $position_type_id; ?>][controller]" value="<?php if (isset($position_type['controller'])) echo $position_type['controller']; ?>" size="60">
				               </div>
				               </td>

				               <td class="left">
								<div class="input-group">
								<input type="text" class="form-control" name="seolang_settings[position_type][<?php echo $position_type_id; ?>][name]" value="<?php if (isset($position_type['name'])) echo $position_type['name']; ?>" size="40">
				               </div>
				               </td>

								<td class="right">
								 <?php foreach ($languages as $lang) { ?>
							<div class="input-group">
							<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?>&nbsp;&nbsp;<img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>" ></span>
										<input type="text" class="form-control" name="seolang_settings[position_type][<?php echo $position_type_id; ?>][title][<?php echo $lang['language_id']; ?>]" value="<?php if (isset($position_type['title'][$lang['language_id']])) echo $position_type['title'][$lang['language_id']]; ?>" style="width: 160px;">
 							</div>


				                 <?php } ?>
								</td>

				                <td class="left"><a onclick="$('#position_type_row<?php echo $position_type_row; ?>').remove();" class="markbutton button_purple nohref  sc_button"><?php echo $button_remove; ?></a></td>
				              </tr>
				            </tbody>

				            <?php } ?>
				            <?php } ?>
				            <tfoot>
				              <tr>
                                <td colspan="6" class="left" style="text-align: right;"><div style="text-align: center;"><a onclick="addpositionType();" class="markbutton nohref sc_button"><?php echo $language->get('entry_seolang_add_position_type'); ?></a></div></td>
				              </tr>
				            </tfoot>
				          </table>
            </div>




								<div style="display: flex; align-items: center; margin-bottom: 10px;">
								   <div style="width: 160px;">
								   	 <?php echo $language->get('entry_seolang_complete'); ?>&nbsp;
								   </div>

								   <div class="input-group">
										<a id="seolang_complete_show" onclick="$('#complete-block').show(); $('#seolang_complete_show').hide(); $('#seolang_complete_hide').show(); return false;" class="markbuttono sc_button"><?php echo $language->get('entry_seolang_show'); ?></a>
										<a id="seolang_complete_hide" onclick="$('#complete-block').hide(); $('#seolang_complete_hide').hide(); $('#seolang_complete_show').show();  return false;" class="markbuttono sc_button" style="display: none;"><?php echo $language->get('entry_seolang_hide'); ?></a>
								   </div>



								</div>



            <div id="complete-block" style="display: none;">
		   <table id="complete-block-table" class="list">
					   <thead>
				             <tr>
				                <td class="left"></td>
				                <td class="left"><?php echo $language->get('entry_seolang_complete_choice'); ?></td>
				             </tr>

				      </thead>


				            <tr class="">
				 			 <td><?php echo $language->get('entry_seolang_complete_status'); ?>
				 			  <?php foreach ($order_statuses as $order_status) { ?>

								 <?php if (isset($seolang_settings['complete_status']) && in_array($order_status['order_status_id'], $seolang_settings['complete_status'])) { ?>
				                    <div class="color_green"><?php echo $order_status['name']; ?></div>
				                 <?php } ?>


				 			  <?php } ?>
				 			 </td>
				              <td>
				               <div class="scrollbox">
				                  <?php  $class = 'even'; ?>
				                  <?php foreach ($order_statuses as $order_status) { ?>
				                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
				                  <div class="<?php echo $class; ?>">
				                    <?php if (isset($seolang_settings['complete_status']) && in_array($order_status['order_status_id'], $seolang_settings['complete_status'])) { ?>
				                    <input type="checkbox" name="seolang_settings[complete_status][]" value="<?php echo $order_status['order_status_id']; ?>" checked="checked" />
				                    <?php echo $order_status['name']; ?>
				                    <?php } else { ?>
				                    <input type="checkbox" name="seolang_settings[complete_status][]" value="<?php echo $order_status['order_status_id']; ?>" />
				                    <?php echo $order_status['name']; ?>
				                    <?php } ?>
				                  </div>
				                  <?php } ?>
				                </div>
				               </td>
				            </tr>

                  </table>
                 </div>



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

				</div>
				<div>
					<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_widgets_options'); ?></span>
				</div>
				<div>

				</div>

			</div>

							</div>



<script type="text/javascript">

var array_position_type_row = Array();

<?php
 foreach ($seolang_settings['position_type'] as $indx => $position_type) {
?>

array_position_type_row.push(<?php echo $indx; ?>);
<?php
}
?>

function addpositionType() {

	var aindex = -1;

	for(i = 0; i < array_position_type_row.length; i++) {
		flg = jQuery.inArray(i, array_position_type_row);
		if (flg == -1) {
			aindex = i;
			break;
		}
	}

	if (aindex == -1) {
		aindex = array_position_type_row.length;
	}
	position_type_row = aindex;
	array_position_type_row.push(aindex);

    html  = '<tbody id="position_type_row' + position_type_row + '">';
	html += '  <tr>';

    html += '  <td class="left">';
	html += ' 	<input type="text" name="seolang_settings[position_type]['+ position_type_row +'][type_id]" value="column_'+ position_type_row +'" size="20">';
    html += '  </td>';


    html += '  <td class="left">';
	html += ' 	<input type="text" name="seolang_settings[position_type]['+ position_type_row +'][controller]" value="common/column_'+ position_type_row +'" size="20">';
    html += '  </td>';

    html += '  <td class="left">';
	html += ' 	<input type="text" name="seolang_settings[position_type]['+ position_type_row +'][name]" value="column_'+ position_type_row +'" size="20">';
    html += '  </td>';


 	html += '  <td class="right">';
 	<?php foreach ($languages as $lang) { ?>

	html += '	<div class="input-group">';
	html += '	<span class="input-group-addon"><?php echo strtoupper($lang['code_short']); ?>&nbsp;&nbsp;<img src="<?php echo $lang['image']; ?>" title="<?php echo $lang['name']; ?>" ></span>';
	html += '		<input type="text" class="form-control" name="seolang_settings[position_type]['+ position_type_row +'][title][<?php echo $lang['language_id']; ?>]" value="" style="width: 300px;">';
	html += '	</div>';


	<?php } ?>
    html += '  </td>';
    html += '  <td class="left"><a onclick="$(\'#position_type_row'+position_type_row+'\').remove(); array_position_type_row.remove(position_type_row);" class="markbutton button_purple nohref sc_button"><?php echo $button_remove; ?></a></td>';




	html += '  </tr>';
	html += '</tbody>';

	$('#position_types tfoot').before(html);

	position_type_row++;
}
</script>

<div id="tabs-options-widgets" class="htabs">
</div>  


