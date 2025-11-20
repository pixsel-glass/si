<?php
include('seolang.php');

$_['ocmod_langmark_name'] = $_['seolang_model'] ;
$_['ocmod_langmark_version'] = $_['seolang_version'] ;
$_['ocmod_langmark_mod'] = 'langmark';
$_['ocmod_langmark_author'] = '';
$_['ocmod_langmark_link'] = '';
$_['ocmod_langmark_html'] = 'Модификатор для ' . $_['seolang_model'] . ' успешно установлен';

$_['text_widget_langmark'] = $_['seolang_model'] . ' ' . $_['seolang_version'];
$_['widget_langmark_version'] = $_['seolang_version'];
$_['text_mod_add_langmark'] = '';
$_['text_widget_langmark_settings'] = $_['text_widget_langmark'];

$_['order_langmark'] = '0';

$_['text_separator'] = ' > ';

$_['entry_langmark_widget_status'] = 'Статус модуля';
$_['entry_langmark_widget_status_scripts'] = 'Скрипты (scripts js) <br>тела списка продуктов';
$_['entry_langmark_widget_content'] = 'CSS селектор тела списка продуктов';
$_['entry_langmark_widget_breadcrumb'] = 'CSS селектор хлебных крошек';
$_['entry_langmark_widget_h1'] = 'CSS селектор мета тега H1';
$_['entry_langmark_widget_install_success'] = 'Таблица виджета Метки продуктов успешно установлена<br>';
$_['entry_langmark_widget_install'] = 'Подключение виджета SEO мультиязык - успешно<br>';
$_['entry_langmark_widget_types'] = 'Удаляемые элементы <br>из шаблона';
$_['entry_number'] = 'Номер';
$_['entry_add_langmark_widget_type'] = 'Добавить элемент';
$_['entry_url_langmark'] = 'Страница настроек модуля';


$_['entry_anchor_templates'] = 'Шаблоны привязки';
$_['entry_anchor_value'] = 'Текущее значение';
$_['entry_anchor_templates_clear'] = 'Очистить';

$_['entry_anchor_templates_tab'] = 'Во вкладке (по умолчанию)';
$_['entry_box_begin_templates'] = 'Блок (начальный HTML код) шаблоны';
$_['entry_box_end_templates'] = 'Блок (закрывающий HTML-код) шаблоны';
$_['entry_box_begin_templates_tab'] = 'Блок (начальный HTML код) шаблон в вкладке (по умолчанию)';
$_['entry_box_end_templates_tab'] = 'Блок (закрывающий HTML-код) шаблон в вкладке (по умолчанию)';
$_['entry_box_begin_templates_empty'] = 'Блок (начальный HTML код) шаблон пустой блок (по умолчанию)';
$_['entry_box_end_templates_empty'] = 'Блок (закрывающий HTML-код) шаблон пустой блок (по умолчанию)';
$_['entry_box_begin_value'] = 'Текущее значение';
$_['entry_box_end_value'] = 'Текущее значение';

$_['entry_anchor_templates_html'] = 'html шаблон';
$_['entry_anchor_templates_prepend'] = 'prepend шаблон';
$_['entry_anchor_templates_append'] = 'append шаблон';
$_['entry_anchor_templates_before'] = 'before шаблон';
$_['entry_anchor_templates_after'] = 'after шаблон';
$_['text_anchor_templates_selector'] = 'ВВЕДИТЕ СЕЛЕКТОР TAG, #ID, .CLASS';

$_['text_adapter_edit'] = 'Адаптировать мультиязык';
$_['entry_replace_text'] = 'Значения для замены';
$_['entry_replace_text_na'] = 'на';


$_['entry_load_template'] = 'Загрузить образец шаблона';
$_['entry_load_template_new'] = 'Загрузить измененный шаблон';

$_['html_help_adapter'] = <<<EOF
Уберите лишние теги &lt;form ...&gt; &lt;/form&gt; &lt;input ...&gt;<br>
Добавьте или измените тегам &lt;a&gt; атрибут href, он должен быть href="&lt;?php echo \$language[<strong style="color: green;">'url'</strong>]; ?&gt;"<br>
Внизу, то что нашел AI и попытается заменить

EOF;
/*********************************************************************/

$_['url_text_seolang'] = 'Виджеты';
$_['url_back_text']  = 'В настройки модуля';
$_['url_modules_text'] = 'В список модулей';

$_['tab_main']  = 'Главная страница';
$_['entry_main_title'] = 'Заголовок главной страницы <br><span class="vhelp">Мета-тег: title</span>';
$_['entry_main_description']  = 'Описание главной страницы <br><span class="vhelp">Мета-тег: description</span>';
$_['entry_main_keywords']  = 'Ключевые слова главной страницы <br><span class="vhelp">Мета-тег: keywords</span>';

$_['tab_ex']  = 'Исключения';
$_['entry_ex_multilang']  = 'В маршрутизаторе <span class="help">(разделитель - перевод каретки PHP_EOL)</span>';
$_['entry_ex_multilang_route'] = 'Исключения для route';
$_['entry_ex_multilang_uri']  = 'Исключения для uri';
$_['entry_ex_url']  = 'В формирователе префиксов <span class="help">(разделитель - перевод каретки PHP_EOL)</span>';
$_['entry_ex_url_route']  = 'Исключения для route';
$_['entry_ex_url_uri'] = 'Исключения для uri';
$_['entry_add']  = 'Добавить';

$_['entry_url_close_slash'] = 'Закрывать URL списка слешем "/"';

$_['entry_main_prefix_status'] = 'Убрать языковый префикс (если он установлен) для главной страницы. Остальные страницы будут с префиксом<br>
<span class="vhelp">Включать только в том случае, если у вас для всех "языков" установлен префикс и вы хотите для какого-то "языка" использовать главную без префикса</span>
';


$_['text_pagination_title'] = 'страница';
$_['text_pagination_title_russian'] = 'страница';
$_['text_pagination_title_ukraine'] = 'сторінка';

$_['text_widget_html'] = 'Языковый HTML, HTML вставка';

$_['text_loading_small'] = '<div style=&#92;\'color: #008000;&#92;\'>Загружается...<i class=&#92;\'fa fa-refresh fa-spin&#92;\'></i></div>';
$_['text_loading'] = '<div>Загружается...<i class="fa fa-refresh fa-spin"></i></div>';
$_['text_loading_langmark'] = '<div>Загружается...<i class="fa fa-refresh fa-spin"></i></div>';



$_['text_update_text'] = 'Нажмите на кнопку.<br>Вы обновили модуль';
$_['text_module'] = 'Модули';
$_['text_add'] = 'Добавить';
$_['text_action'] = 'Действие:';
$_['text_success'] = 'Модуль успешно обновлен!';
$_['text_content_top'] = 'Содержание шапки';
$_['text_content_bottom']  = 'Содержание подвала';
$_['text_column_left'] = 'Левая колонка';
$_['text_column_right'] = 'Правая колонка';
$_['text_what_lastest'] = 'Последние записи';
$_['text_select_all'] = 'Выделить всё';
$_['text_unselect_all'] = 'Снять выделение';
$_['text_sort_order'] = 'Порядок';
$_['text_further'] = '...';
$_['text_error']  = 'Ошибка';
$_['text_layout_all'] = 'Все';
$_['text_enabled'] = 'Включено';
$_['text_disabled']  = 'Отключено';
$_['text_multi_empty'] = 'Зайдите в таб "Установка и обновление" и нажмите кнопку "Создание и обновление данных для модуля (при установке и обновлении модуля)"';

$_['entry_lang_default']  = 'Язык по умолчанию';

$_['entry_name'] = 'Имя';
$_['entry_prefix']  = 'Префикс';
$_['entry_prefix_main']   = 'Главный язык';

$_['entry_seomore_code']   = 'Код JS';

$_['entry_hreflang']  = 'Мета тег hreflang';
$_['entry_hreflang_status']   = 'Статус мета тег hreflang ';
$_['entry_commonhome_status']  = '<span class="vhelp">Убрать из URL Главной</span> <br>index.php?route=common/home ';
$_['entry_languages']  = 'Связанный язык';
$_['entry_access'] = 'Доступ';

$_['entry_remove_description_status'] = 'Убрать описание на <br>дополнительных страницах<br>пагинации';
$_['entry_add_rule']  = 'Добавить правило';
$_['entry_title_template']   = 'Имя файла шаблона';
$_['entry_desc_types'] = 'Правила, согласно которым <br>в шаблонах (имя файла)<br>будет убрано описание <br>на дополнительных <br>страницах пагинации';

$_['entry_pagination'] = 'Пагинация';
$_['entry_jazz'] = 'Поддержка ЧПУ формирователя Jazz';
$_['entry_pagination_prefix']  = 'Название переменной пагинации';
$_['entry_title_pagination']  = 'Заголовок пагинации';
$_['entry_currencies'] = 'Связанная валюта';

$_['entry_title_list_latest']  = 'Заголовок';
$_['entry_editor'] = 'Графический редактор';
$_['entry_switch'] = 'Включить модуль';
$_['entry_title_prefix'] = 'Языковый префикс<span class="help">Поставьте языковый префикс,<br>например для английского языка <b>en</b><br>(url будет иметь вид: http://site.com/en )<br>Если вы хотите чтобы url с префиксом<br>заканчивался слешем<br>(пример: http://site.com/en/),<br>то поставьте префикс <b>en<ins style="color:green; text-decoration: none;">/</ins></b><br>или оставьте поле <b>пустым</b><br>если у вас этот язык стоит <b>по умолчанию</b></span>';
$_['entry_about'] = 'О модуле';
$_['entry_category_status'] = 'Показывать категорию';
$_['entry_cache_widgets']  = 'Полное кеширование виджетов<br/><span class="help">При полном кешировании виджетов <br>скорость обработки и вывода быстрее в 2-5 раз <br>в зависимости от количества виджетов <br>используемых на странице</span>';
$_['entry_reserved']  = 'Зарезервировано';
$_['entry_service']  = 'Сервис';
$_['entry_langfile']  = 'Языковый пользовательский файл<br><span class="help">формат: <b>папка/файл</b> без расширения</span>';
$_['entry_widget_cached']  = 'Кешировать виджет<br><span class="help">Имеет больший приоритет, чем полное кеширование <br>всех виджетов в общих настройках, <br>иногда кешировать виджет не надо, <br>если у вас в шаблонах есть логика добавления <br>JS скриптов и CSS стилей в документ</span>';

$_['entry_anchor'] = '<b>Привязка</b><br><span class="help" style="line-height: 13px;">привязка к блоку через jquery<br>пример для default темы opencart:<br>$(\'<b>#language</b>\').html(langmarkdata);<br>где langmarkdata (переменная javascript)<br>результат выполнения html блока</span>';


$_['entry_layout'] = 'Схемы:';

$_['entry_html']  = <<<EOF
<b>HTML, PHP, JS код</b><br><span class="help" style="line-height: 13px;">Понимает выполнение PHP кода<br>
Переменные:<br>
\$languages - массив, имеющий структуру:<br>
 [код языка] => Array<br>
  (<br>
&nbsp;&nbsp;  [language_id] => id языка<br>
&nbsp;&nbsp;  [name] => имя языка<br>
&nbsp;&nbsp;  [code] => код языка<br>
&nbsp;&nbsp;  [locale] => locale языка<br>
&nbsp;&nbsp;  [image] => изображение языка<br>
&nbsp;&nbsp;  [directory] => папка<br>
&nbsp;&nbsp;  [filename] => имя языкового файла<br>
&nbsp;&nbsp;  [sort_order] => порядок<br>
&nbsp;&nbsp;  [status] => статус<br>
&nbsp;&nbsp;  [url] => url текущей страницы для языка<br>
  )<br>
<br>
\$text_language - заголовок<br>
для языкового блока
<br>
<br>
\$language_code - текущий код языка
<br>
\$language_prefix - текущий префикс языка
</span>
EOF;

$_['entry_position']  = 'Расположение:';
$_['entry_status'] = 'Статус:';
$_['entry_sort_order'] = 'Порядок:';

$_['entry_template']  = '<b>Шаблон</b>';
$_['entry_what']  = 'what';
$_['entry_install_update'] = 'Установка и обновление';


$_['tab_general'] = 'Схемы';
$_['tab_list'] = 'Виджеты';
$_['tab_options'] = 'Настройки';
$_['tab_pagination']  = 'Пагинация';

$_['button_add_list'] = 'Добавить виджет';
$_['button_update']  = 'Изменить';
$_['button_clone_widget']  = 'Клонировать виджет';
$_['button_continue'] = "Далее";

$_['error_delete_old_settings'] = '<div style="color: red; text-align: left; text-decoration: none;">Пока нельзя удалять настройки старых версий<br><ins style="text-align: left; text-decoration: none; font-size: 13px; color: red;">(пересохраните "настройки", "схемы" и "виджеты", <br>только после этого нажмите эту кнопку)</ins></div>';
$_['error_permission'] = 'У Вас нет прав для изменения модуля!';
$_['error_addfields_name'] = 'Не правильное имя дополнительного поля';

$_['access_777']  = 'Не установлены права на файл<br>Установите права 777 на этот файл вручную.';
$_['text_install_ok'] = 'Данные успешно обновлены';
$_['text_install_already'] = 'Данные присутствуют';
$_['hook_not_delete'] = 'Данную схему нельзя удалять, она нужна для сервисных функций модуля (seo)<br>В случае, если вы случайно удалили, добавьте такую же схему с такими же параметрами<br>';
$_['type_list']  = 'Виджет:';
$_['text_about']  = <<<EOF

EOF;

$_['tab_other']  = 'Прочие';
$_['entry_two_status'] = 'Исправление "повторяющихся слешей //" в URL';

$_['entry_prefix_switcher'] = 'Вывод в переключателе языков';
$_['entry_prefix_switcher_stores'] = 'Вывод в переключателе языков всех магазинов';
$_['entry_hreflang_switcher']  = 'Вывод в мета теге hreflang';
$_['entry_hreflang_switcher_stores']  = 'Вывод в мета теге hreflang всех магазинов';

$_['entry_shortcodes']  = 'Шорткоды';

$_['entry_currency_switch'] = 'При смене языка принудительно <br>переключать валюту согласно настроек, <br>в не зависимости от пользовательских <br>переключений валюты на других языках <br>домена или поддомена';

$_['entry_use_link_status'] = 'Использовать штатный <br>алгоритм формирования ЧПУ';

$_['text_shortcodes_in'] = 'Шорткод для замены';
$_['text_shortcodes_out'] = 'Замена';
$_['text_shortcodes_action'] = 'Действие';
$_['url_create_text'] = '<div style="text-align: center; text-decoration: none;">Создание и обновление<br>данных для модуля<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(при установке и обновлении модуля)</ins></div>';
$_['url_delete_text'] = '<div style="text-align: center; text-decoration: none;">Удаление всех<br>настроек модуля<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">(все настройки будут удалены)</ins></div>';


$_['entry_copy_rules'] = 'Скопировать правила';

$_['entry_store_id_related'] = 'Связанный магазин';

$_['url_store_id_repated_text'] = '<div style="text-align: center; text-decoration: none;">Привязать все категории, товары,<br>производителей, информационные страницы (статьи)<br><ins style="text-align: center; text-decoration: none; font-size: 13px;">к этому магазину</ins></div>';

$_['entry_title_values'] = 'Переменные';
$_['entry_cache_diff'] = 'Раздельный кеш <br>(для регионов вне мультимагазинов, <br>на одном магазине, с одинаковыми товарами)';

$_['entry_langswitch_replace'] = 'Заменять стандартный <br>переключатель';
$_['entry_lm_image_status'] = 'Статус изображения языка';
$_['entry_cookie'] = 'Одноразовая реакция <br>на cookie';
$_['entry_cookie_set'] = 'Установить cookie <br>после выбора';
$_['entry_lm_text_close'] = 'Текст кнопки "Закрыть"';

$_['entry_lm_redirect'] = 'Перенаправление на главный язык <br>при закрытии popup окна';
$_['entry_lm_autoredirect'] = 'Перенаправление на главный язык <br><b>автоматическое</b>';


$_['entry_widget_status'] = 'Статус';
$_['entry_store'] = 'Магазин';
$_['entry_xdefault_status'] = 'Статус x-default';

$_['text_langmark_widget'] = 'Виджеты';
$_['entry_id'] = 'ID';
$_['entry_seo_pagination'] = 'SEO URL сроки пагинации<br><span class="helpshow">вроде .../page-2</span>';

$_['entry_multi_sort'] = 'Порядок сортировки';


