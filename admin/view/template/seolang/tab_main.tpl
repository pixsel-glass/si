                         <table class="mynotable" style="width: 100%; margin-bottom:20px; background: white; vertical-align: center;">
                              <tr style="width: 100%;">
                                 <td style="width: 100%;">
                                    <div id="block_multi" style="width: 100%;">
                                       <div id="tabs-multi" class="htabs">
                                          <?php
                                          $multi_name_row_header = 0;
                                          if (!empty($seolang_settings_store['multi'])) {
                                             foreach ($seolang_settings_store['multi'] as $multi_name => $multi) {
                                             ?>
                                          <a href="#tab-multi<?php echo $multi_name_row_header; ?>" id="tab-multi-row<?php echo $multi_name_row_header; ?>" class="lm-tab-2">
                                          <?php echo $multi_name; ?>&nbsp;&nbsp;&nbsp;
                                          <span onclick="$('#tab-multi-row<?php echo $multi_name_row_header; ?>').remove(); $('#tab-multi<?php echo $multi_name_row_header; ?>').remove(); return false;" class="markbutton button_purple nohref"><i class="fa fa-times" aria-hidden="true"></i></span>
                                          </a>
                                          <?php
  												if (isset($error_name) && $error_name == $multi['name']) {
	                                      ?>
								                  <script>
								                     $(document).ready(function(){
								                     	$('#tab-multi-row<?php echo $multi_name_row_header; ?>').click();
								                     });
								                  </script>
										  <?php
	                                            }
	                                            if (isset($new_name) && $new_name == $multi['name']) {
	                                      ?>
								                  <script>
								                     $(document).ready(function(){
								                     	$('#tab-multi-row<?php echo $multi_name_row_header; ?>').click();
								                     });
								                  </script>
										  <?php
	                                            }
                                             	$multi_name_row_header++;
                                             }
                                          } else {
                                             //echo $text_seolang_multi_empty;
										  } ?>
                                          <a href="#tab-multi-add" id="tab-multi-row-add">
                                          <?php echo $entry_seolang_add; ?>&nbsp;&nbsp;
                                          <span onclick="multi_add(); return false;" class="markbutton button-green nohref"><i class="fa fa-plus" aria-hidden="true"></i></span>
                                          </a>
                                       </div>

                                       <div id="tab-multi-add">
                                          <div class="flex-box">
                                             <div class="input-group" style="text-align: left;">
                                                <a onclick="multi_add(); return false;" id="tab-multi-row-add-<?php echo $multi_name_row_header; ?>" class="markbutton button-green nohref">
                                                	<?php echo $entry_seolang_add; ?>
                                                	<span class="markbutton button-green nohref"><i class="fa fa-plus" aria-hidden="true"></i></span>
                                                </a>
                                                <br><br>
												<select class="form-control" name="seolang_widget" id="id_seolang_widget">
								                 <?php foreach ($widgets as $widget_num => $widget) { ?>
								                   <option value="<?php echo $widget; ?>" <?php
								                   if (isset($seolang_widget) && $seolang_widget == $widget) { ?>selected="selected"<?php
								                   } ?>><?php echo $language->get('entry_seolang_widget_' . $widget); ?></option>
								                  <?php } ?>
								                 </select>
                                             </div>
                                          </div>
                                       </div>

                                       <script>
                                          multi_name_row = <?php echo $multi_name_row_header; ?>;

                                          function tab_seolang_multi_click() {
                                          	$('a[href="#tab-multi'+ (multi_name_row - 1) + '"]').click();
                                          }

                                          function multi_add(widget_name) {
                                            if (widget_name == '' || widget_name == undefined) {
                                            	widget_name = '';
                                            }
                                          	html_tab = '<a href="#tab-multi' + multi_name_row + '" id="tab-multi-row' + multi_name_row + '" class="lm-tab-2">';
                                          	// html_tab += 'WIDGET-' + multi_name_row + '&nbsp;&nbsp;&nbsp;';
                                          	html_tab += 'WIDGET' + '&nbsp;&nbsp;&nbsp;';
                                          	html_tab += '<span onclick="$(\'#tab-multi-row' + multi_name_row + '\').remove(); $(\'#tab-multi' + multi_name_row + '\').remove(); return false;" class="markbutton button_purple nohref"><i class="fa fa-times" aria-hidden="true"></i></span>';
                                          	html_tab += '</a>';

                                            html_tab_seolang_content = '';
                                            html_tab_seolang_content += '';

                                          	$('#tab-multi-row-add').before(html_tab);

                                          	$.ajax({
	                                          url: '<?php echo $url_add_multi; ?>',
	                                          dataType: 'html',
	                                          type: 'post',
	                                          data: {myform: $('#form').serialize(), widget_name: widget_name, seolang_widget: $('#id_seolang_widget :selected').val(), multi_name_row: multi_name_row, store_id: '<?php echo $store_id; ?>' },
	                                          beforeSend: function() {
	                                         	 $('#tab-multi-row' + multi_name_row).append('<div id="add_multi_loading"><?php echo $language->get('text_seolang_loading_seolang'); ?></div>');
	                                          },
	                                          success: function(ajax_html) {

	                                          	$('#add_multi_loading').remove();
	                                          	$('#block_multi').append(ajax_html);
	                                          	$('#tabs-multi > a').tabs();
	                                            setTimeout('tab_seolang_multi_click()', 0);
	                                          },
	                                          error: function(ajax_html) {
	                                         	 $('#tab-multi-row' + multi_name_row).append('error');
	                                          }
                                          });

                                          multi_name_row++;
                                          }
                                       </script>

                                       <?php
										if (!empty($seolang_settings_store['multi'])) {
											foreach ($seolang_settings_store['multi'] as $multi_name => $multi) {

												if (!isset($multi['widget'])) {
													$multi['widget'] = 'html';
												}

												if (function_exists('modification') && file_exists(modification(DIR_TEMPLATE . 'seolang/' . $multi['widget'] . '/multi.tpl')))  {
													include(modification(DIR_TEMPLATE . 'seolang/' . $multi['widget'] . '/multi.tpl'));
												} else {
													include(DIR_TEMPLATE . 'seolang/' . $multi['widget'] . '/multi.tpl');
												}
											}
										}
                                       ?>
                                    </div>
                                 </td>
                              </tr>
                           </table>

