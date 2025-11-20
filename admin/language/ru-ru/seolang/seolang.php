<?php
include('version.php');

$_['text_seolang_title'] = 'SEO мультиязык';

$_['seolang_model_code'] = 'seolang';
$_['order_seolang'] = '0';
$_["ico_seolang"] = '<i class="uflag-ukflag"></i>'; 
$_['ocmod_seolang_author'] = '';
$_['ocmod_seolang_link'] = '';
$_['seolang_model_settings'] = '';
$_['heading_title'] = '<strong style="color:#41637d">DEV-OPENCART.COM —</strong> <b>SEO мультиязык PRO</b> <a href="https://dev-opencart.com" target="_blank" title="Dev-opencart.com - Модули и шаблоны для Opencart"><img style="margin-left:15px;height:35px;margin-top:10px;margin-bottom:10px;" src="https://dev-opencart.com/logob.svg" alt="Dev-opencart.com - Модули и шаблоны для Opencart"/></a>';
$_['widget_seolang_version'] = $_['seolang_version'];
$_['heading_title_seolang'] = $_['seolang_model'];
$_['heading_dev'] = 'Разработчик <a href="' . $_['ocmod_seolang_link'] . '" target="_blank">' . $_['ocmod_seolang_author'] . '</a><br>&copy; 2011-' . date('Y') . ' All Rights Reserved';

$_['error_text_seolang_permission'] = 'У Вас нет прав для изменения модуля!';
$_['error_text_seolang_modify'] = 'У вас нет прав на изменения модуля!';

$_['url_text_seolang_opencartadmin'] = $_['ocmod_seolang_link'];
$_['url_text_seolang_create_text'] = '<div style="text-align: center; text-decoration: none;">Создание и обновление<br>данных для модуля<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(при установке и обновлении модуля)</ins></div>';
$_['url_text_seolang_delete_text'] = '<div style="text-align: center; text-decoration: none;">Удаление всех<br>настроек модуля<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(все настройки будут удалены)</ins></div>';
$_['url_text_seolang_delete_sure_text'] = '<div style="text-align: center; text-decoration: none;">Вы уверены<br>что хотите удалить все настройки?<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(все настройки будут удалены)</ins></div>';
$_['url_text_seolang_create_text'] = '<div style="text-align: center; text-decoration: none;">Установка и обновление<br>модификаторов, данных модуля<br>(выполняется при установке или обновлении модуля)</div>';
$_['url_text_seolang_module_text'] = 'SEO LANG';
$_['url_text_seolang_ocmodrefresh'] = 'Обновить';
$_['url_text_seolang_cacheremove'] = 'Удалить кеш';

$_['ocmod_seolang_name'] = $_['seolang_model'];
$_['ocmod_seolang_name_15'] = $_['seolang_model'].' 15';
$_['ocmod_seolang_menu_name'] = $_['seolang_model'] . ' Menu';
$_['ocmod_seolang_menu_mod'] = $_['seolang_model_code'] . '_menu';
$_['ocmod_seolang_menu_html'] = $_['ocmod_seolang_menu_name'] . ' модификатор успешно установлен';
$_['ocmod_seolang_mod'] = $_['seolang_model_code'];
$_['ocmod_seolang_mod_15'] = $_['seolang_model_code'].'_15';


$_['ocmod_seolang_html'] = $_['ocmod_seolang_name'].' модификатор успешно установлен';
$_['ocmod_seolang_name'] = $_['seolang_model'];
$_['ocmod_seolang_version'] = $_['seolang_version'] ;


$_['ocmod_seolang_text_on'] = '<span style="color:green">включен</span>';
$_['ocmod_seolang_text_off'] = '<span style="color:red">выключен</span>';


$_['tab_text_seolang_options'] = 'Настройки';
if (SC_VERSION > 23) {
	$_['tab_text_seolang_position'] = 'Макеты и позиции';
} else {
	$_['tab_text_seolang_position'] = 'Схемы и позиции';	
}

$_['tab_text_seolang_doc'] = 'Документация';
$_['tab_text_seolang_menu'] = 'Меню';
$_['tab_text_seolang_main'] = 'Виджеты';
$_['tab_text_seolang_service'] = 'Сервис';
$_['tab_text_seolang_access'] = 'Доступы';

$_['entry_seolang_incont'] = 'Вывод через контроллер<br><span class="vhelp">Не включать если кнопка "Показать еще" <br>выводится и корректно работает</span>';
$_['entry_seolang_id'] = 'ID';
$_['entry_seolang_copy'] = 'Копировать';
$_['entry_seolang_install_update'] = 'Установка / Обновление';
$_['entry_seolang_position'] = 'Расположение';
$_['entry_seolang_copy_rules'] = 'Скопировать правила';
$_['entry_seolang_title_values'] = 'Переменные';
$_['entry_seolang_add_rule'] = 'Добавить';
$_['entry_seolang_widget_status'] = "Статус";
$_['entry_seolang_seolang_ocmodrefresh'] = 'Обновить кеш <br><span class="sc-color-clearcache">модификаторов</span>';
$_['entry_seolang_seolang_cacheremove'] = 'Удалить кеш <br><span class="sc-color-clearcache">файлов</span>';
$_['entry_seolang_store'] = 'Магазины:';
$_['entry_seolang_seolang_menu_status'] = 'Статус <i class="fa fa-dot-circle-o"></i> SEO LANG в меню';
$_['entry_seolang_seolang_menu_order'] = 'Порядок пункта <i class="fa fa-dot-circle-o"></i> SEO LANG в меню, после "номера" <br>пункта в меню <br>номер:';
$_['entry_seolang_seolang_widget_status'] = 'Статус модуля';
$_['entry_seolang_seolang_widget_install_success'] = 'Таблицы виджета ' . $_['seolang_model'] . ' успешно установлена<br>';
$_['entry_seolang_seolang_widget_install'] = 'Подключение виджета ' . $_['seolang_model'] . ' - успешно<br>';
$_['entry_seolang_seolang_widget_types'] = 'Удаляемые элементы <br>из шаблона';
$_['entry_seolang_number'] = 'Номер';
$_['entry_seolang_add_seolang_widget_type'] = 'Добавить элемент';
$_['entry_seolang_html'] = 'HTML';
$_['entry_seolang_add'] = 'Добавить';
$_['entry_seolang_lang_default'] = 'Язык по умолчанию';
$_['entry_seolang_name'] = 'Имя';
$_['entry_seolang_access'] = 'Доступ';
$_['entry_seolang_add_rule'] 	= 'Добавить правило';
$_['entry_seolang_title_template'] 	= 'Имя файла шаблона';
$_['entry_seolang_editor'] = 'Графический редактор';
$_['entry_seolang_switch'] = 'Включить модуль';
$_['entry_seolang_about'] = 'О модуле';
$_['entry_seolang_category_status'] = 'Показывать категорию';
$_['entry_seolang_reserved'] = 'Зарезервировано';
$_['entry_seolang_service'] = 'Сервис';
$_['entry_seolang_layout'] = 'Макеты:';
$_['entry_seolang_status'] = 'Статус:';
$_['entry_seolang_sort_order'] = 'Порядок:';
$_['entry_seolang_template'] = 'Шаблон';
$_['entry_seolang_install_update'] = 'Установка и обновление';
$_['entry_seolang_show'] = 'Показать';
$_['entry_seolang_positions'] = 'Позиции';
$_['entry_seolang_hide'] = 'Скрыть';
$_['entry_seolang_uri'] = 'URI';
$_['entry_seolang_add_position_type']	= 'Добавить, не стандартную,<br> имеющуюся в opencart, <br>пользовательскую позицию';
$_['entry_seolang_layouts'] = 'Макеты';
$_['entry_seolang_menu_status'] = 'Меню статус';
$_['entry_seolang_menu_order'] = 'Порядок в меню';
$_['entry_seolang_widgets_options'] = 'Глобальные настройки виджетов';
$_['entry_seolang_customer_groups'] = 'Группы покупателей';
$_['entry_seolang_complete_status'] = 'Статус купившего товар:<br /><span class="vhelp">Статус заказа, при котором покупатель <br>получает статус купившего "этот" товар</span>';
$_['entry_seolang_complete'] = 'Статус купившего товар';
$_['entry_seolang_complete_choice'] = 'Отметьте статусы заказа для купивших товар';
$_['entry_seolang_position_types']	= 'Позиции / Пользовательские позиции';
$_['entry_seolang_position_controller']	= 'Контроллер обработки';
$_['entry_seolang_position_name']	= 'Имя переменной вывода';
$_['entry_seolang_sort'] = 'Порядок';
$_['entry_seolang_show_pro_settings'] = 'Показать PRO настройки';
$_['entry_seolang_hide_pro_settings'] = 'Скрыть PRO настройки';

$_['text_seolang_uri_template'] = 'По "слову" в URI';
$_['text_seolang_uri'] = 'URI (URL без протокола и домена)<br><span class="vhelp">Не заполняйте если используете макеты</span>';
$_['text_seolang_error_name'] = 'Имя виджета содержит недопустимые символы<br><span class="vhelp">Допустимые символы: a-zA-Z0-9-_<br>нельзя использовать кириллицу и т.п.</span>';
$_['text_seolang_status'] = 'Статус';
$_['text_seolang_mod_add_seolang'] = $_['seolang_model'].' модификатор установлен<br>';
$_['text_seolang_seolang_success'] = 'Успешно';
$_['text_seolang_ocmodrefresh_successfully'] = '<span style="color:green">Модификаторы успешно обновлены</span>';
$_['text_seolang_ocmodrefresh_success'] = 'Модификаторы успешно обновлены';
$_['text_seolang_ocmodrefresh_error'] = '<span style="color:red">Ошибка обновления модификаторов</span>';
$_['text_seolang_ocmodrefresh_fail'] = 'Не удалось обновить';
$_['text_seolang_ocmod'] = 'модификатор';
$_['text_seolang_cacheremove'] = 'Удалить кеш';
$_['text_seolang_cacheremove_success'] = 'Выполнено успешно';
$_['text_seolang_cacheremove_fail'] = 'Не удалось удалить';
$_['text_seolang_seolang_about'] = 'О модуле';
$_['text_seolang_default_store'] = 'Основной магазин';
$_['text_seolang_loading_main'] = '<div style=&#92;\'color: #008000;&#92;\'>Загружается...<i class=&#92;\'fa fa-refresh fa-spin&#92;\'></i></div>';
$_['text_seolang_loading_main_without'] = '<div style="color: #008000">Загружается...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_seolang_faq'] = '';
$_['text_seolang_separator'] = ' > ';
$_['text_seolang_status_on'] = 'включено';
$_['text_seolang_status_off'] = 'выключено';
$_['text_seolang_seolang_status_on'] = $_['text_seolang_title'] . ' <span style="margin-left: 6px; color: #eeffee;"> '.$_['text_seolang_status_on'] .'</span>';
$_['text_seolang_seolang_status_off'] = $_['text_seolang_title'] . ' <span style="margin-left: 6px; color: #fccccc;"> '.$_['text_seolang_status_off'] .' </span>';
$_['text_seolang_ocmod_refresh'] = 'Обновить&nbsp;модификаторы';
$_['text_seolang_close'] = 'Закрыть';
$_['text_seolang_loading_small'] = '<div style=&#92;\'color: #008000;&#92;\'>Загружается...<i class=&#92;\'fa fa-refresh fa-spin&#92;\'></i></div>';
$_['text_seolang_loading'] = '<div>Загружается...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_seolang_loading_seolang'] = '<div>Загружается...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_seolang_update_text'] = 'Нажмите на кнопку.<br>Вы обновили или установили модуль';
$_['text_seolang_module'] = 'Модули';
$_['text_seolang_add'] = 'Добавить';
$_['text_seolang_action'] = 'Действие:';
$_['text_seolang_success'] = 'Модуль успешно обновлен!';
$_['text_seolang_content_top'] = 'Содержание шапки';
$_['text_seolang_content_bottom'] = 'Содержание подвала';
$_['text_seolang_column_left'] = 'Левая колонка';
$_['text_seolang_column_right'] = 'Правая колонка';
$_['text_seolang_what_lastest'] = 'Последние записи';
$_['text_seolang_select_all'] = 'Выделить всё';
$_['text_seolang_unselect_all'] = 'Снять выделение';
$_['text_seolang_sort_order'] = 'Порядок';
$_['text_seolang_further'] = '...';
$_['text_seolang_error'] = 'Ошибка';
$_['text_seolang_layout_all'] = 'Все';
$_['text_seolang_enabled'] = 'Включено';
$_['text_seolang_disabled'] = 'Отключено';
$_['text_seolang_multi_empty'] = 'Зайдите в таб "Установка и обновление" и нажмите кнопку "Создание и обновление данных для модуля (при установке и обновлении модуля)"';
$_['text_seolang_install_ok'] = 'Данные успешно обновлены';
$_['text_seolang_install_already'] = 'Данные присутствуют';
$_['text_seolang_check_ver'] = 'Проверить новую версию';
$_['text_seolang_server_date_state'] = 'По состоянию на';
$_['text_seolang_current_version_text'] = '<div style="color: #306793;">Ваша текущая версия</div>';
$_['text_seolang_last_version_text'] = '<div style="color: #306793;">Последняя версия</div>';
$_['text_seolang_update_yes'] = '<div style="color: red;">Рекомендуется обновить модуль</div>';
$_['text_seolang_update_no'] = '<div style="color: green;">Обновление не требуется, у вас самая последняя версия модуля</div>';
$_['text_seolang_error_text_seolang_server_connect'] = 'Ошибка соединения с сервером';
$_['text_seolang_update_version_begin'] = "<div style='background: #F7FFF2; width: auto; border: 1px solid #E2EDDC; padding: 10px;'>Последняя доступная версия модуля: <span style='font-size: 21px;'>";
$_['text_seolang_update_version_end'] = "</span></div>";
$_['text_seolang_new_version'] = "<div style='background: #FFCFCE; border: 2px solid red; padding: 10px;'>Установленная версия модуля: <b><span style='color: red;'>" . $_['seolang_version'] . "</span></b><br>"."Последняя версия модуля: <span style='color: green;'><b>";
$_['text_seolang_new_version_end'] = '</b></span><br>Рекомендуется: <span style="color: green;"><b>обновите модуль до последней версии</b></span></div>';
$_['text_seolang_group_reg'] = 'Зарегистрированные';
$_['text_seolang_group_order'] = 'Купившие товар в магазине';
$_['text_seolang_group_order_this'] = 'Купившие "этот" товар в магазине';
$_['text_seolang_group_all'] = 'Все группы покупателей';
$_['text_seolang_error_server_connect'] = 'Ошибка соединения с сервером';

$_['seolang_ocas'] = $_['ocmod_seolang_link'] . '/index.php?route=record/ver';

/* Add backup */
$_['entry_lm_backup'] = 'Настройки <br><span style="color: green;">cохранить</span>';
$_['entry_lm_restore'] = 'Настройки <br><span style="color: green;">восстановить</span>';

$_['text_lm_url_backup'] = 'Сохранить';
$_['text_lm_url_restore'] = 'Восстановить';

$_['text_lm_backup_success'] = '<span style="color: green;">Настройки сохранены</span>';
$_['text_lm_restore_success'] = '<span style="color: green">Настройки восстановлены</span>';

$_['text_lm_backup_fail'] = 'Не удалось сохранить настройки';
$_['text_lm_restore_fail'] = 'Не удалось восстановить настройки';

$_['text_lm_backup_access'] = '<span style="color: red;">У вас нет прав доступа</span>';
$_['text_lm_restore_access'] = '<span style="color: red;">У вас нет прав доступа</span>';

$_['text_lm_settings_no_format'] = '<span style="color: red;">Неправильный формат настроек</span>';
$_['text_lm_json_error'] = '<span style="color: red;">Ошибка декодирования JSON</span>';
$_['text_lm_error_filetype'] = '<span style="color: red;">Неправильный тип файла</span>';
/* backup */
/* Menu */
$_['entry_seolang_seolang_options'] = 'Настройки виджетов<br>' . $_['seolang_model_settings'];
$_['text_seolang_seolang_options'] = 'Настройки';

$_['entry_seolang_langmark_options'] = 'Настройки<br>' . $_['seolang_model_settings'];
$_['text_seolang_langmark_options'] = 'Настройки';

$_['entry_seolang_seolang_adapter'] = 'Адаптер<br>переключателя языка';
$_['text_seolang_seolang_adapter'] = 'Адаптация';
$_['text_seolang_widgets'] = 'Виджеты';
/* Menu */

/* Icons */
$_['ocmod_seolang_name_15'] = $_['seolang_model'].' 15';
$_['ocmod_seolang_icons_name'] = $_['seolang_model'] . " CSS";
$_['ocmod_seolang_icons_mod'] = $_['seolang_model_code'] . '_icons';
$_['ocmod_seolang_icons_html'] = $_['ocmod_seolang_icons_name'] . ' модификатор успешно установлен';
/* Icons */

$_['text_seolang_ocmod_none'] = $_['text_seolang_ocmod'] . ' не установлен';

$_['text_seolang_device'] = 'Устройства';
$_['text_seolang_device_all'] = 'Все устройства';
$_['text_seolang_device_comp'] = 'Компьютер';
$_['text_seolang_device_mob'] = 'Мобильные устройства';
$_['text_seolang_device_smart'] = 'Смартфоны';
$_['text_seolang_device_pad'] = 'Планшеты';