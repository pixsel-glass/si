<?php
include('version.php');

$_['text_seolang_title'] = 'SEO multilang';
$_['seolang_model_code'] = 'seolang';
$_['order_seolang'] = '0';
$_["ico_seolang"] = '<i class="uflag-ukflag"></i>'; 

$_['ocmod_seolang_author'] = 'support.opencartadmin.com';
$_['ocmod_seolang_link'] = 'https://support.opencartadmin.com';

$_['seolang_model_settings'] = $_['heading_title'] = $_['seolang_model'] . ' ' . $_['seolang_version'];
$_['heading_title'] = '<span style="color: #5c8284; font-size: 15px; font-weight: 400;"><a href="' . $_['ocmod_seolang_link'] . '" style="color: #5c8284;" target="_blank" data-toggle="tooltip" title="" data-original-title="' . $_['ocmod_seolang_author'] . '">'  . $_['ico_seolang'] . '</a>  ' . $_['heading_title'] . '</span>';
$_['widget_seolang_version'] = $_['seolang_version'];
$_['heading_title_seolang'] = $_['seolang_model'];
$_['heading_dev'] = 'Developer <a href="' . $_['ocmod_seolang_link'] . '" target="_blank">' . $_['ocmod_seolang_author'] . '</a><br>&copy; 2011-' . date('Y') . ' All Rights Reserved';

$_['error_text_seolang_permission'] = 'At You no right for changes of the module!';
$_['error_text_seolang_modify'] = 'You don`t have rights to change the module!';

$_['url_text_seolang_opencartadmin'] = $_['ocmod_seolang_link'];
$_['url_text_seolang_create_text'] = '<div style="text-align: center; text-decoration: none;">Creating and updating <br>data for the module<br><ins style= "text-align: center; text-decoration: none; font-size: 13px;" >(when installing and updating the module)</ins></div>';
$_['url_text_seolang_delete_text'] = '<div style= "text-align: center; text-decoration: none;" >Deleting all <br>module settings<br><ins style= "text-align: center; text-decoration: none; font-size: 13px;" >(all settings will be deleted)</ins></div>';
$_['url_text_seolang_delete_sure_text'] = '<div style="text-align: center; text-decoration: none;">Are you sure you want to delete all settings?<br><ins style= "text-align: center; text-decoration: none; font-size: 13px;" >(all settings will be deleted)</ins></div>';
$_['url_text_seolang_create_text'] = '<div style="text-align: center; text-decoration: none;">Installing and updating<br>Modifications and module data<br>(performed when installing or updating a module)</div>';
$_['url_text_seolang_module_text'] = 'SEO LANG';
$_['url_text_seolang_ocmodrefresh'] = 'Refresh';
$_['url_text_seolang_cacheremove'] = 'Delete Cache';

$_['ocmod_seolang_name'] = $_['seolang_model'];
$_['ocmod_seolang_name_15'] = $_['seolang_model'].' 15';
$_['ocmod_seolang_menu_name'] = $_['seolang_model'] . ' Menu';
$_['ocmod_seolang_menu_mod'] = $_['seolang_model_code'] . '_menu';
$_['ocmod_seolang_menu_html'] = $_['ocmod_seolang_menu_name'] . ' modification installed successfully';
$_['ocmod_seolang_mod'] = $_['seolang_model_code'];
$_['ocmod_seolang_mod_15'] = $_['seolang_model_code'].'_15';
$_['ocmod_seolang_html'] = $_['ocmod_seolang_name'] . ' modification installed successfully';
$_['ocmod_seolang_name'] = $_['seolang_model'];
$_['ocmod_seolang_version'] = $_['seolang_version'] ;


$_['ocmod_seolang_text_on'] = '<span style="color:green">enabled</span>';
$_['ocmod_seolang_text_off'] = '<span style="color:red">disabled</span>';

$_['tab_text_seolang_options'] = 'Settings';
$_['tab_text_seolang_position'] = 'Layouts and Positions';
$_['tab_text_seolang_doc'] = 'Documentation';
$_['tab_text_seolang_menu'] = 'Menu';
$_['tab_text_seolang_main'] = 'Widgets';
$_['tab_text_seolang_service'] = 'Service';
$_['tab_text_seolang_access'] = 'Access';

$_['entry_seolang_incont'] = 'Output via the controller<br><span class="vhelp">is not enabled if the "Show more" button <br>is displayed and works correctly</span>';
$_['entry_seolang_id'] = 'ID';
$_['entry_seolang_copy'] = 'Copy';
$_['entry_seolang_install_update'] = 'Install / Update';
$_['entry_seolang_position'] = 'Position';
$_['entry_seolang_copy_rules'] = 'Copy Rules';
$_['entry_seolang_title_values'] = 'Variables';
$_['entry_seolang_add_rule'] = 'Add';
$_['entry_seolang_widget_status'] = "Status";
$_['entry_seolang_seolang_ocmodrefresh'] = 'Refresh<br> <span class="sc-color-clearcache">modifications</span>';
$_['entry_seolang_seolang_cacheremove'] = 'Delete cache <br><span class="sc-color-clearcache">files</span>';
$_['entry_seolang_store'] = 'Stores:';
$_['entry_seolang_seolang_menu_status'] = 'Status <i class="fa fa-dot-circle-o"></i> SEO LANG in the menu';
$_['entry_seolang_seolang_menu_order'] = 'The order of the item <i class=" fa fa-dot-circle-o "> < /i> SEO LANG in the menu, after the" number " <br>item in the menu <br>number:';
$_['entry_seolang_seolang_widget_status'] = 'Module Status';
$_['entry_seolang_seolang_widget_install_success'] = 'Widget Tables ' . $_['seolang_model'] . 'successfully installed<br>';
$_['entry_seolang_seolang_widget_install'] = 'Enabling the widget ' . $_['seolang_model'] . '- successful<br>';
$_['entry_seolang_seolang_widget_types'] = '<br>elements to delete from the template';
$_['entry_seolang_number'] = 'Number';
$_['entry_seolang_add_seolang_widget_type'] = 'Add an Item';
$_['entry_seolang_html'] = 'HTML';
$_['entry_seolang_add'] = 'Add';
$_['entry_seolang_lang_default'] = 'Default language';
$_['entry_seolang_name'] = 'Name';
$_['entry_seolang_access'] = 'Access';
$_['entry_seolang_add_rule']  = 'Add a Rule';
$_['entry_seolang_title_template']    = 'Template file Name';
$_['entry_seolang_editor'] = 'Image Editor';
$_['entry_seolang_switch'] = 'Enable the module';
$_['entry_seolang_about'] = 'About the module';
$_['entry_seolang_category_status'] = 'Show category';
$_['entry_seolang_reserved'] = 'Reserved';
$_['entry_seolang_service'] = 'Service';
$_['entry_seolang_layout'] = 'Layouts:';
$_['entry_seolang_status'] = 'Status:';
$_['entry_seolang_sort_order'] = 'Order:';
$_['entry_seolang_template'] = 'Template';
$_['entry_seolang_install_update'] = 'Install and Update';
$_['entry_seolang_show'] = 'Show';
$_['entry_seolang_positions'] = 'Positions';
$_['entry_seolang_hide'] = 'Hide';
$_['entry_seolang_uri'] = 'URI';
$_['entry_seolang_add_position_type'] = 'Add a non-standard <br> custom position available in opencart <br>';
$_['entry_seolang_layouts'] = 'Layouts';
$_['entry_seolang_menu_status'] = 'Status menu';
$_['entry_seolang_menu_order'] = 'Menu Order';
$_['entry_seolang_widgets_options'] = 'Global Widget Settings';
$_['entry_seolang_customer_groups'] = 'Customer Groups';
$_['entry_seolang_complete_status'] = 'Status of the buyer of the product:<br /><span class= "vhelp" >Order status, in which the buyer <br>gets the status of the buyer of "this " product</span>';
$_['entry_seolang_complete'] = 'Status of the buyer of the product';
$_['entry_seolang_complete_choice'] = 'Mark order statuses for those who purchased the product';
$_['entry_seolang_position_types']    = 'Positions / Custom Positions';
$_['entry_seolang_position_controller']   = 'Processing Controller';
$_['entry_seolang_position_name'] = 'Name of the output variable';
$_['entry_seolang_sort'] = 'Order';
$_['entry_seolang_show_pro_settings'] = 'Show PRO settings';
$_['entry_seolang_hide_pro_settings'] = 'Hide PRO Settings';

$_['text_seolang_uri_template'] = 'By "word" in the URI';
$_['text_seolang_uri'] = 'URI (URL without protocol and domain)<br><span class="vhelp">Don`t fill it out if you are using layouts</span>';
$_['text_seolang_error_name'] = 'The widget name contains invalid characters<br><span class= "vhelp">Acceptable characters: a-zA-Z0-9 - _<br>you can`t use the cyrillic alphabet, etc.</span>';
$_['text_seolang_status'] = 'Status';
$_['text_seolang_mod_add_seolang'] = $_['seolang_model'].'the modification is set to<br>';
$_['text_seolang_seolang_success'] = 'Successful';
$_['text_seolang_ocmodrefresh_successfully'] = '<span style="color:green ">Modifications refreshed successfully</span>';
$_['text_seolang_ocmodrefresh_success'] = 'Modifications refreshed successfully';
$_['text_seolang_ocmodrefresh_error'] = '<span style= "color:red" >Error refrashed modifications</span>';
$_['text_seolang_ocmodrefresh_fail'] = 'Couldn`t refrash';
$_['text_seolang_ocmod'] = 'modification';
$_['text_seolang_cacheremove'] = 'Delete Cache';
$_['text_seolang_cacheremove_success'] = 'Completed successfully';
$_['text_seolang_cacheremove_fail'] = 'Couldn`t delete';
$_['text_seolang_seolang_about'] = 'About the module';
$_['text_seolang_default_store'] = 'Main Store';
$_['text_seolang_loading_main'] = '<div style=&#92;\'color: #008000;&#92;\'>Loading...<i class=&#92;\'fa fa-refresh fa-spin&#92;\'></i></div>';
$_['text_seolang_loading_main_without'] = '<div style="color: #008000">Is loading...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_seolang_faq'] = '';
$_['text_seolang_separator'] = ' > ';
$_['text_seolang_status_on'] = 'enabled';
$_['text_seolang_status_off'] = 'disabled';
$_['text_seolang_seolang_status_on'] = $_['text_seolang_title'] . ' <span style="margin-left: 6px; color: #eeffee;"> '.$_['text_seolang_status_on'] .'</span>';
$_['text_seolang_seolang_status_off'] = $_['text_seolang_title'] . ' <span style="margin-left: 6px; color: #fccccc;"> '.$_['text_seolang_status_off'] .' </span>';
$_['text_seolang_ocmod_refresh'] = 'Refresh modifications';
$_['text_seolang_close'] = 'Close';
$_['text_seolang_loading_small'] = '<div style=&#92;\'color: #008000;&#92;\'>Loading...<i class=&#92;\'fa fa-refresh fa-spin&#92;\'></i></div>';
$_['text_seolang_loading'] = '<div>is loading...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_seolang_loading_seolang'] = '<div>is loading...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_seolang_update_text'] = 'Click on the button.<br>You have updated or installed the module';
$_['text_seolang_module'] = 'Modules';
$_['text_seolang_add'] = 'Add';
$_['text_seolang_action'] = 'Action:';
$_['text_seolang_success'] = 'The module has been updated successfully!';
$_['text_seolang_content_top'] = 'Header Content';
$_['text_seolang_content_bottom'] = 'Bottom Content';
$_['text_seolang_column_left'] = 'Left column';
$_['text_seolang_column_right'] = 'Right Column';
$_['text_seolang_what_lastest'] = 'Recent Entries';
$_['text_seolang_select_all'] = 'Select All';
$_['text_seolang_unselect_all'] = 'Deselect';
$_['text_seolang_sort_order'] = 'Order';
$_['text_seolang_further'] = '...';
$_['text_seolang_error'] = 'Error';
$_['text_seolang_layout_all'] = 'All';
$_['text_seolang_enabled'] = 'Enabled';
$_['text_seolang_disabled'] = 'Disabled';
$_['text_seolang_multi_empty'] = 'Go to the "Install and Update" tab and click " Create and update data for the module (when installing and updating the module)"';
$_['text_seolang_install_ok'] = 'Data updated successfully';
$_['text_seolang_install_already'] = 'Data present';

$_['text_seolang_check_ver'] = 'Check a new version';
$_['text_seolang_server_date_state'] = 'By current status on';
$_['text_seolang_current_version_text'] = '<div style="color: #306793;">Your current version version</div>';
$_['text_seolang_last_version_text'] = '<div style="color: #306793;">Last version</div>';
$_['text_seolang_update_yes'] = '<div style="color: red;">Recommended update module</div>';
$_['text_seolang_update_no'] = '<div style="color: green;">No update required. You have the latest version of the module</div>';
$_['text_seolang_error_text_seolang_server_connect'] = 'Mistake connections with by the server';
$_['text_seolang_update_version_begin'] = "<div style='background: #F7FFF2; width: auto; border: 1px solid #E2EDDC; padding: 10px;'>Last available information version of the module: <span style='font-size: 21px;'>";
$_['text_seolang_update_version_end'] = "</span></div>";
$_['text_seolang_new_version'] = "<div style='background: #FFCFCE; border: 2px solid red; padding: 10px;'>Installed version version of the module: <b><span style='color: red;'>" . $_['seolang_version'] . "</span></b><br>"."Last version of the module: <span style='color: green;'><b>";
$_['text_seolang_new_version_end'] = '</b></span><br>Recommended: <span style="color: green;"><b>update it module before the last one versions</b></span></div>';
$_['text_seolang_error_server_connect'] = 'Mistake connections with by the server';

$_['text_seolang_group_reg'] = 'Registered users';
$_['text_seolang_group_order'] = 'Who bought it product in in the store';
$_['text_seolang_group_order_this'] = 'Who bought it "this" product in in the store';
$_['text_seolang_group_all'] = 'All groups customers';


$_['seolang_ocas'] = $_['ocmod_seolang_link'] . '/index.php?route=record/ver';

/* Add backup */
$_['entry_lm_backup'] = 'Settings <br><span style="color: green;">save</span>';
$_['entry_lm_restore'] = 'Settings <br><span style="color: green;">restore</span>';

$_['text_lm_url_backup'] = 'Save';
$_['text_lm_url_restore'] = 'Restore';

$_['text_lm_backup_success'] = '<span style="color: green;">Settings saved</span>';
$_['text_lm_restore_success'] = '<span style="color: green">Settings restored</span>';

$_['text_lm_backup_fail'] = 'Failed to save settings';
$_['text_lm_restore_fail'] = 'Failed to restore settings';

$_['text_lm_backup_access'] = '<span style="color: red;">You have no access rights</span>';
$_['text_lm_restore_access'] = '<span style="color: red;">You have no access rights</span>';

$_['text_lm_settings_no_format'] = '<span style="color: red;">Incorrect settings format</span>';
$_['text_lm_json_error'] = '<span style="color: red;">Error decoding JSON</span>';
$_['text_lm_error_filetype'] = '<span style="color: red;">Incorrect file type</span>';
/* backup */

/* Menu */
$_['entry_seolang_seolang_options'] = 'Options widgets<br>' . $_['seolang_model_settings'];
$_['text_seolang_seolang_options'] = 'Options';

$_['entry_seolang_langmark_options'] = 'Options<br>' . $_['seolang_model_settings'];
$_['text_seolang_langmark_options'] = 'Options';

$_['entry_seolang_seolang_adapter'] = 'Adapter<br>language switcher';
$_['text_seolang_seolang_adapter'] = 'Adaptation';
$_['text_seolang_widgets'] = 'Widgets';
/* Menu */

/* Icons */
$_['ocmod_seolang_name_15'] = $_['seolang_model'].' 15';
$_['ocmod_seolang_icons_name'] = $_['seolang_model'] . " CSS";
$_['ocmod_seolang_icons_mod'] = $_['seolang_model_code'] . '_icons';
$_['ocmod_seolang_icons_html'] = $_['ocmod_seolang_icons_name'] . ' modification installed successfully';
/* Icons */
$_['text_seolang_ocmod_none'] = $_['text_seolang_ocmod'] . ' not installed';

$_['text_seolang_device'] = 'Devices';
$_['text_seolang_device_all'] = 'All devices';
$_['text_seolang_device_comp'] = 'Computers';
$_['text_seolang_device_mob'] = 'Mobile devices';
$_['text_seolang_device_smart'] = 'Smartphones';
$_['text_seolang_device_pad'] = 'Tablets';
