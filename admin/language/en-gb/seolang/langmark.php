<?php
include('seolang.php');

$_['ocmod_langmark_name'] = $_['seolang_model'];
$_['ocmod_langmark_version'] = $_['seolang_version'];
$_['ocmod_langmark_mod'] = 'langmark';
$_['ocmod_langmark_author'] = 'support.opencartadmin.com';
$_['ocmod_langmark_link'] = 'https://support.opencartadmin.com';
$_['ocmod_langmark_html'] = 'Modification for ' . $_['seolang_model'] . ' succesfull installed';


$_['text_widget_langmark'] = $_['seolang_model'] . ' ' . $_['seolang_version'];
$_['widget_langmark_version'] = $_['seolang_version'];
$_['text_mod_add_langmark'] = '';
$_['text_widget_langmark_settings'] = $_['text_widget_langmark'];

$_['order_langmark'] = '0';

$_['text_separator'] = ' > ';

$_['entry_langmark_widget_status'] = 'Status of widget';
$_['entry_langmark_widget_status_scripts']  = 'Scripts (js) <br>body list products';
$_['entry_langmark_widget_content'] = 'CSS selector body list products';
$_['entry_langmark_widget_breadcrumb'] = 'CSS selector breadcrumbs';
$_['entry_langmark_widget_h1'] = 'CSS selector meta tag H1';
$_['entry_langmark_widget_install_success'] = 'Settings widget SEO multilang is installed successfully<br>';
$_['entry_langmark_widget_install'] = 'Connect widget SEO multilang  - successfully<br>';
$_['entry_langmark_widget_types'] = 'Deleted element <br>from a template';
$_['entry_number'] = 'Number';
$_['entry_add_langmark_widget_type'] = 'Add item';
$_['entry_url_langmark'] = 'Page options of module';


$_['entry_anchor_templates'] = 'Anchor templates';
$_['entry_anchor_value'] = 'Current value';
$_['entry_anchor_templates_clear'] = 'Clear input';

$_['entry_anchor_templates_tab'] = 'In tab (default)';

$_['entry_box_begin_templates'] = 'Block (initial HTML code) templates';
$_['entry_box_end_templates'] = 'Block (closing HTML code) templates';
$_['entry_box_begin_templates_tab'] = 'Block (initial HTML code) templates in tab (default)';
$_['entry_box_end_templates_tab'] = 'Block (closing HTML code) templates in tab (default)';
$_['entry_box_begin_templates_empty'] = 'Block (initial HTML code) templates empty block (default)';
$_['entry_box_end_templates_empty'] = 'Block (closing HTML code) templates empty block (default)';
$_['entry_box_begin_value'] = 'Current value';
$_['entry_box_end_value'] = 'Current value';

$_['entry_anchor_templates_html'] = 'html template';
$_['entry_anchor_templates_prepend'] = 'prepend template';
$_['entry_anchor_templates_append'] = 'append template';
$_['entry_anchor_templates_before'] = 'before template';
$_['entry_anchor_templates_after'] = 'after template';
$_['text_anchor_templates_selector'] = 'TYPE TAG, #ID, .CLASS SELECTOR';

$_['text_adapter_edit'] = 'Adapter multilang';
$_['entry_anchor_templates_default'] = 'Value for #language';
$_['entry_replace_text'] = 'Value to replace';
$_['entry_replace_text_na'] = 'on';
$_['entry_load_template'] = 'Load sample template';
$_['entry_load_template_new'] = 'Load customized template';

$_['html_help_adapter'] = <<<EOF
Remove excess tags &lt;form ...&gt; &lt;/form&gt; &lt;input ...&gt;<br>
Add or change the tags &lt;a&gt; the href attribute, it needs to be href="&lt;?php echo \$language[<strong style="color: green;">'url'</strong>]; ?&gt;<br>
Downstairs, we found the AI and try to replace

EOF;

/********************************************************/

$_['url_text_seolang']          = 'Widgets';
$_['url_create_text'] 				= '<div style="text-align: center; text-decoration: none;">Create and update<br>data for the module<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(if installation and upgrade module)</ins></div>';
$_['url_delete_text'] 				= '<div style="text-align: center; text-decoration: none;">Remove all<br>module settings<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(all settings layouts and widgets will be removed)</ins></div>';
$_['url_back_text'] 				= 'Module configuration';
$_['url_modules_text'] 				= 'Modules';

$_['tab_main'] = 'Main page';
$_['entry_main_title'] = 'Title of the page <br><span class="help">Meta tag: title</span>';
$_['entry_main_description'] = 'Description <br><span class="help">Meta tag: description</span>';
$_['entry_main_keywords'] = 'Keywords of page <br><span class="help">Meta tag: keywords</span>';

$_['tab_ex'] = 'Exception';
$_['entry_ex_multilang'] = 'In controller <br> router';
$_['entry_ex_multilang_route'] = 'Exception route';
$_['entry_ex_multilang_uri'] = 'Exceptions for uri';
$_['entry_ex_url'] = 'In controller <br>shaper of prefixes';
$_['entry_ex_url_route'] = 'Exception route';
$_['entry_ex_url_uri'] = 'Exceptions for uri';
$_['entry_add']     			= 'Add';
$_['entry_lang_default']          	= 'Default language';

$_['entry_url_close_slash'] = 'Close the URL list with a slash "/"';


$_['text_pagination_title'] 		= 'page';
$_['text_pagination_title_russian'] = 'page';
$_['text_pagination_title_ukraine'] = 'site';

$_['text_widget_html'] 				= 'Language HTML, HTML insert';

$_['text_loading_small'] = '<div style=&#92;&#8217;color: #008000;&#92;&#8217;>Loading...<i class=&#92;&#8217;fa fa-refresh fa-spin&#92;&#8217;></i></div>';
$_['text_loading'] = '<div>Loading...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_loading_langmark'] = '<div>Loading...<i class="fa fa-refresh fa-spin"></i></div>';

$_['text_update_text'] 					= 'Click on the button.<br>Have you updated the module';
$_['text_module'] 					= 'Modules';
$_['text_add'] 						= 'Add';
$_['text_action'] 					= 'Action:';
$_['text_success'] 					= 'Module has been successfully updated!';
$_['text_content_top']				= 'Content-caps';
$_['text_content_bottom'] 			= 'Contents of the basement';
$_['text_column_left'] 				= 'Left column';
$_['text_column_right'] 			= 'Right column';
$_['text_what_lastest'] 			= 'Last post';
$_['text_select_all'] 				= 'Select all';
$_['text_unselect_all']				= 'deselect';
$_['text_sort_order'] 				= 'Order';
$_['text_further'] 					= '...';

$_['text_layout_all'] 				= 'All';
$_['text_enabled'] 					= 'Enabled';
$_['text_disabled'] 				= 'Disabled';
$_['text_error'] 				= 'Error';
$_['text_multi_empty'] = 'Go to the tab "Installation and Update" and click button "Create and update data for a module (when installing and updating a module)';

$_['entry_pagination'] 				= 'Pagination';
$_['entry_pagination_prefix'] 		= 'Name of the variable pagination';
$_['entry_title_pagination'] 		= 'Title pagination';
$_['entry_currencies'] = 'Connected currency';
$_['entry_jazz']          = 'Support Jazz URL controller';

$_['entry_main_prefix_status'] = 'Remove language prefix <br>(if installed) <br>for the main page. the <br>Other pages <br>will be prefixed with';
$_['entry_name']          		= 'Name';
$_['entry_prefix']          		= 'Prefix';
$_['entry_prefix_main']        	= 'Main language';
$_['entry_hreflang']          		= 'Meta tag hreflang';
$_['entry_hreflang_status']   = 'Status meta tag hreflang';
$_['entry_commonhome_status']   = 'Remove index.php?route=common/home ';
$_['entry_languages']          	= 'Related language';
$_['entry_access']        		= 'Access';

$_['entry_remove_description_status'] = 'Remove a description with <br>additional pages<br> of pagination';
$_['entry_add_rule'] = 'Add rule';
$_['entry_title_template'] = 'File name template';
$_['entry_desc_types'] = 'Rules <br>in templates (file name)<br>will be removed description <br>on additional <br>pages pagination';

$_['entry_title_list_latest'] 		= '<b>Title</b>';
$_['entry_editor'] 					= 'image editor';
$_['entry_switch'] 					= 'Enable module';
$_['entry_title_prefix'] 			= 'Language prefix<span class="help">Select the language prefix,<br>for example, for English <b>en</b><br>(the url would be: http://site.com/en )<br>If you want the url with the prefix<br>ended with a slash<br>(example: http://site.com/en/),<br>to put the prefix <b>en<ins style="color:green; text-decoration: none;">/</ins></b><br>or leave the <b>empty</b><br>if you have this language is <b>default</b></span>';
$_['entry_about'] 					= 'Module';
$_['entry_category_status'] 		= 'Show category';
$_['entry_cache_widgets'] 			= 'Full caching widgets<br/><span class="help">With full caching widgets <br>processing speed and output faster 2-5 times <br>depending on the number of widgets <br>used in</span>';
$_['entry_reserved'] 				= 'Reserved';
$_['entry_service'] 				= 'Tools';
$_['entry_langfile'] 				= 'Language custom file<br><span class="help">format: <b>folder/file</b> extensions</span>';
$_['entry_widget_cached'] 			= 'Cache widget<br><span class="help">Has a higher priority than a full cache <br>all widgets in the General settings, <br>sometimes to cache the widget is not necessary, <br>if your template is the logic of adding <br>JS scripts and CSS styles in the document</span>';

$_['entry_anchor'] 					= '<b>Binding</b><br><span class="help" style="line-height: 13px;">binding to the unit via jquery<br>example for default opencart theme:<br>$(\'<b>#language</b>\').html(langmarkdata);<br>where langmarkdata (variable javascript)<br>the result of running the html block</span>';


$_['entry_layout'] 					= 'Layout';

$_['entry_position'] 				= 'Location:';
$_['entry_status'] 					= 'Status:';
$_['entry_sort_order'] 				= 'Order:';

$_['entry_template'] 				= '<b>Template</b>';
$_['entry_what'] 					= 'what';
$_['entry_install_update'] 			= 'Installation and upgrade';


$_['tab_general'] 					= 'Layouts';
$_['tab_list'] 						= 'Widgets';
$_['tab_options'] 					= 'Settings';
$_['tab_pagination'] 				= 'Pagination';

$_['button_add_list'] 				= 'Add widget';
$_['button_update'] 				= 'Change';
$_['button_clone_widget'] 			= 'Clone widget';
$_['button_continue'] 				= "Next";

$_['error_delete_old_settings'] 	= '<div style="color: red; text-align: left; text-decoration: none;">While you cannot delete settings old versions<br><ins style="text-align: left; text-decoration: none; font-size: 13px; color: red;">(re-save "settings", "layout" and "widgets", <br>only after this click this button)</ins></div>';
$_['error_permission'] 				= 'You Have no rights to change the module!';
$_['error_addfields_name'] 			= 'Is not the correct name of the additional field';

$_['access_777'] 					= 'Not set the permissions on the file<br>Set permissions 777 to the file manually.';

$_['text_install_ok']          = 'Data was successfully updated';
$_['text_install_already']     = 'Data was successfully updated';

$_['hook_not_delete'] 				= 'This layout cannot be removed, it need for the service module functions (seo)<br>if you have accidentally deleted, add the same layout with the same parameters<br>';
$_['type_list'] 					= 'Widget';

$_['entry_html'] 					= <<<EOF
EOF;

$_['text_about'] = <<<EOF
EOF;

$_['tab_other'] = 'Others';
$_['entry_two_status'] = 'Fix duplicate slashes in URL';

$_['entry_prefix_switcher'] = 'Output in the language switcher';
$_['entry_prefix_switcher_stores'] = 'Output in the language switcher all stores';
$_['entry_hreflang_switcher'] = 'Output in meta tag hreflang';
$_['entry_hreflang_switcher_stores'] = 'Output in meta tag hreflang all stores';

$_['entry_use_link_status'] = 'Use SEO URL generation algorithm';

$_['entry_shortcodes']   = 'Shortcodes';

$_['text_shortcodes_in'] = 'Shortcode for replace';
$_['text_shortcodes_out'] = 'Replace';
$_['text_shortcodes_action'] = 'Action';

$_['entry_copy_rules'] = 'Copy rules';

$_['entry_store_id_related'] = 'Related store';
$_['entry_title_values'] = 'Variables';
$_['entry_cache_diff'] = 'Different cache';

$_['entry_langswitch_replace'] = 'Replace standard <br>language switch';
$_['entry_lm_image_status'] = 'Language Image Status';
$_['entry_cookie'] = 'One time cookie reaction <br>';
$_['entry_cookie_set'] = 'Set cookie <br>after selection';
$_['entry_lm_text_close'] = 'Close button text';
$_['entry_lm_redirect'] = 'Redirect to main language<br> on popup window closing';
$_['entry_lm_autoredirect'] = 'Redirect to main language <br><b>automatic</b>';

$_['entry_widget_status'] = 'Status';
$_['entry_store'] = 'Store';
$_['entry_currency_switch'] = 'When changing the language, <br>forcibly switch the currency according to the settings, <br>regardless of user currency switching in other languages <br>of the domain or subdomain';
$_['entry_xdefault_status'] = 'Status x-default';
$_['entry_id'] = 'ID';
$_['text_langmark_widget'] = 'Widgets';
$_['entry_seo_pagination'] = 'SEO URL pagination address string<br><span class="helpshow">like .../page-2</span><br><span class="helpshow">(Not recommended)</span>';

$_['entry_multi_sort'] = 'Sort order';

$_['entry_redirect_new'] = 'Redirect (old URLs without prefix) <br>to new URLs with language prefix<br><span class="lm-help">Enable <b>only if</b> <br>your old URLs are already <br>indexed by search engines. <br>Do not enable on a new store</span>';
$_['entry_redirect_code'] = 'Redirect code <br><span class="lm-help">301 (permanent) or <br>302 (temporary)</span>';
$_['entry_ex_url_amp'] = 'Replace in URL <br>& for &amp;amp;';
