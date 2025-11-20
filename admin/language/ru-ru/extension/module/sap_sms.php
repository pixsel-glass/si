<?php
$_['heading_title']	= '<strong><a style="color:#1b850c;" href="http://doc.sapas.pw" target="_blank">SAP</a></strong> - SMS / OTP';
$_['mobule_title']	= 'SAP - SMS / OTP&nbsp;v%s';

$_['text_extension']						= 'Модули';
$_['text_success']							= 'Настройки успешно изменены!';
$_['text_edit']								= 'Редактирование модуля SAP - SMS / OTP';
$_['text_on']								= 'Вкл';
$_['text_off']								= 'Выкл';
$_['text_get']								= 'GET';
$_['text_post']								= 'POST';
$_['text_activate_module']					= 'Пожалуйста, активируйте модуль.';
$_['text_enter_license_key']				= 'Введите ключ лицензии';
$_['text_enter_license_name']				= 'Введите имя';
$_['text_activate']							= 'АКТИВИРОВАТЬ';
$_['text_module_activate']					= 'Модуль активирован, спасибо.';
$_['text_status']							= 'Статус';
$_['text_method']							= 'Метод';
$_['text_url']								= 'Url';
$_['text_add_parameter']					= 'Добавить пареметр';
$_['text_parameter_key']					= 'Ключ параметра';
$_['text_parameter_value']					= 'Значение параметра';
$_['text_remove']							= 'Удалить';
$_['text_otp_length']						= 'Длина OTP';
$_['text_resendtime']						= 'Время повторной отправки';
$_['text_show_country_code']				= 'Показать код страны';
$_['text_show_country_code_help']			= '&nbsp;Отображение кода страны с флагом в поле Телефон.';
$_['text_restrict_country_code']			= 'Ограничить отправку SMS по коду страны';
$_['text_restrict_country_code_help']		= '&nbsp;Введите через запятую коды стран, в которые вы хотите отправить SMS. Например, если вы хотите отправить SMS клиентам из стран Украина и США, введите <b>ua,us</b>. Оставьте поле пустым, чтобы отправлять SMS на все номера.<br>Коды стран — это коды стран формата <b>Alpha 2</b>, которые можно найти <a href="https://www.nationsonline.org/oneworld/country_code_list.htm" target="_newtab">здесь</a> или <b>Код ISO (2)</b> которые вы можно найти по пути <b>Система -> Локализация -> Страны</b>.<br>Добавляйте коды стран через запятую маленькими буквами!<br><span class="text-danger">Клиенты из стран, не добавленных в список, не получат никаких SMS с OTP, не увидят проверку OTP и будут регистрироваться напрямую.</span>';
$_['text_log']								= 'Log';
$_['text_otpstatus']						= 'OTP Статус';
$_['text_loginbtnstatus']					= 'Авторизация с OTP';
$_['text_registerbtnstatus']				= 'Регистрация (подтверждение) с OTP';
$_['text_checkoutbtnstatus']				= 'Оформление заказа (подтверждение) с OTP';
$_['text_otpmsg']							= 'OTP cообщение для входа';
$_['text_regotpmsg']						= 'OTP cообщение для проверки мобильного при регистрации или оформления заказа';
$_['text_resend_success']					= 'SMS сообщение отправленно повторно';
$_['text_del_success']						= 'Отчет о SMS удален';
$_['text_clear_success']					= 'Все отчеты о SMS удалены';
$_['text_shortcode_help']					= '<code>{store_name}</code> - название магазина<br><code>{store_host}</code> - хост магазина<br><code>{otp}</code> - код OTP';
$_['text_masking']							= 'Маска телефона';
$_['text_mask']								= 'Маска ввода';
$_['text_mask_with_country_code']			= '&nbsp;Пример маски когда включен <b>Показать код страны</b> (99) 999-99-9?99<br><i>Цыфры после ? не обязательны для ввода.</i>';
$_['text_mask_without_country_code']		= '&nbsp;Пример маски когда выключен <b>Показать код страны</b> 380 (99) 999-99-99?9<br><i>Цыфры после ? не обязательны для ввода.</i><br><span class="text-danger">После кода страны обязательно должен быть пробел!</span>';
$_['text_min_characters']					= 'Мин. длина телефона';
$_['text_max_characters']					= 'Макс. длина телефона';
$_['text_send_json']						= 'JSON';
$_['text_send_json_help']					= '&nbsp;Передача данных в виде JSON объектов.';
$_['text_resendtime_help']					= '&nbsp;Продолжительность, по истечении которой кнопка повторной отправки активируется после отправки OTP.<br>Введите 0, чтобы отключить эту функцию';
$_['text_sec']								= 'сек.';
$_['text_min']								= 'мин.';
$_['text_expire_time']						= 'Срок действия OTP';
$_['text_expire_time_help']					= '&nbsp;Продолжительность, по истечении которой срок действия OTP истекает.<br>Введите 0, если вы не хотите, чтобы срок действия OTP истекал';
$_['text_enable_otp_request_limit']			= 'Ограничить запрос OTP';
$_['text_enable_otp_request_limit_help']	= '&nbsp;Если \'Вкл\', то пользователь может запрашивать OTP только ограниченное количество раз подряд.';
$_['text_otp_request_limit']				= 'Количество OTP-запросов';
$_['text_otp_request_limit_help']			= '&nbsp;Количество раз подряд, когда пользователь может запросить OTP, после этого номер телефона будет заблокирован на какое-то время.';
$_['text_otp_request_block_duration']		= 'Длительность блокировки';
$_['text_otp_request_block_duration_help']	= '&nbsp;Продолжительность, в течение которой OTP не может быть запрошен для проверки номера телефона после достижения лимита запросов OTP';
$_['text_enable_wrong_otp_limit']			= 'Ограничение ошибочных попыток OTP';
$_['text_enable_wrong_otp_limit_help']		= '&nbsp;Если \'Вкл\', то пользователь может ввести неправильный OTP только ограниченное количество раз после этого номер телефона будет заблокирован';
$_['text_wrong_otp_limit']					= 'Количество ошибочных попыток OTP';
$_['text_wrong_otp_limit_help']				= '&nbsp;Сколько раз подряд клиент может ввести неверный OTP после этого номер телефона будет заблокирован на какое-то время';
$_['text_wrong_otp_block_duration_help']	= '&nbsp;Продолжительность, в течение которой номер телефона будет заблокирован после достижения неправильного лимита OTP';
$_['text_phone_blacklist']					= 'Черный список телефонов';
$_['text_phone_blacklist_help']				= '&nbsp;Введите через запятую номера телефонов на которые не будут отправляться SMS с OTP.<br>Номера вводить нужно в таком формате 971234567';
$_['text_sms_notifications']				= 'SMS уведомления';
$_['text_admin_mobile']						= 'Телефон администратора';
$_['text_admin_mobile_help']				= '&nbsp;Номер телефона нужно указывать в международном формате. Пример: 380999999999<br>Можете задать несколько номеров через запятую.';
$_['text_new_order']						= 'Новый заказ';
$_['text_new_order_customer']				= '&nbsp;(Новый заказ)';
$_['text_new_order_help']					= '&nbsp;SMS шаблон администратору при создании нового заказа.';
$_['text_send']								= 'Ваше сообщение было успешно отправлено %s получателям из %s!';
$_['text_send_success']						= 'Ваше сообщение успешно отправлено!';
$_['text_to']								= 'Кому';
$_['text_customer_all']						= 'Все покупатели';
$_['text_customer_group']					= 'Группа покупателей';
$_['text_customers']						= 'Покупатели';
$_['text_customer']							= 'Покупатель';
$_['text_phone']							= 'Произвольные номера телефонов';
$_['text_products']							= 'Товары';
$_['text_product']							= 'Товар';
$_['text_mobile_numbers']					= 'Номера телефонов';
$_['text_enter_mobile_numbers']				= 'Введите номер(а) телефона';
$_['text_message']							= 'Сообщение';
$_['text_total_characters']					= 'Всего символов:&nbsp;';
$_['text_gsm_message']						= 'SMS-сообщение может содержать до 160 символов. Пожалуйста, проверьте своего поставщика SMS-шлюза, поддерживает ли он длинные SMS.';
$_['text_sending']							= 'Отправка...';
$_['text_sending_sms']						= '&nbsp;Отправить sms';
$_['text_shortcodes1']						= '<code>{store_url}</code> - URL магазина<br/>
<code>{order_id}</code> - Номер заказа<br/>
<code>{order_status}</code> - Статус заказа<br/>
<code>{order_date}</code> - Дата заказа<br/>
<code>{order_total}</code> - Сумма заказа<br/>
<code>{order_total_noshipping}</code> - Сумма заказа без доставки<br/>
<code>{store_name}</code> - Название магазина<br/>
<code>{invoice_no}</code> - Номер счета<br/>
<code>{invoice_prefix}</code> - Префикс счета<br/>
<code>{firstname}</code> - Имя покупателя<br/>
<code>{lastname}</code> - Фамилия покупателя<br/>
<code>{customer_id}</code> - ID покупателя<br/>
<code>{email}</code> - Email покупателя<br/>
<code>{product_total}</code> - Всего товаров<br/>
<code>{comment}</code> - Комментарий<br/>
<code>{payment_method}</code> - Способ оплаты<br/>
<code>{shipping_method}</code> - Способ доставки';
$_['text_shortcodes2']						= '<code>{payment_firstname}</code> - Имя покупателя (оплаты)<br/>
<code>{payment_lastname}</code> - Фамилия покупателя (оплаты)<br/>
<code>{payment_company}</code> - Компания (оплаты)<br/>
<code>{payment_address_1}</code> - Адрес 1 (оплаты)<br/>
<code>{payment_address_2}</code> - Адрес 2 (оплаты)<br/>
<code>{payment_postcode}</code> - Индекс (оплаты)<br/>
<code>{payment_city}</code> - Город (оплаты)<br/>
<code>{payment_country}</code> - Страна (оплаты)<br/>
<code>{shipping_cost}</code> - Стоимость доставки<br/>
<code>{shipping_firstname}</code> - Имя покупателя (доставка)<br/>
<code>{shipping_lastname}</code> - Фамилия покупателя (доставка)<br/>
<code>{shipping_company}</code> - Компания<br/>
<code>{shipping_address_1}</code> - Адрес 1 (доставка)<br/>
<code>{shipping_address_2}</code> - Адрес 2 (доставка)<br/>
<code>{shipping_postcode}</code> - Индекс (доставка)<br/>
<code>{shipping_city}</code> - Город (доставка)<br/>
<code>{shipping_country}</code> - Страна (доставка)';

$_['tab_setting']		= 'Настройки';
$_['tab_sms']			= 'SMS уведомления';
$_['tab_sms_admin']		= 'SMS администратору';
$_['tab_sms_order']		= 'SMS о статусе заказа';
$_['tab_otp']			= 'OTP';
$_['tab_setting_otp']	= 'Настройка OTP';
$_['tab_message_otp']	= 'Сообщения OTP';
$_['tab_sms_bulk']		= 'Массовые SMS';
$_['tab_report']		= 'SMS Отчет';

$_['column_id']			= 'SMS ID';
$_['column_telephone']	= 'Телефон';
$_['column_message']	= 'Сообщение';
$_['column_date']		= 'Дата';
$_['column_status']		= 'Статус';
$_['column_balance']	= 'Баланс';

$_['button_resend']		= 'Отправить SMS повторно';
$_['button_shortcodes']	= 'Шорткоды';
$_['button_clear']		= 'Очистить';

$_['error_permission']			= 'У вас нет прав на внесение изменений в модуль SAP - SMS / OTP!';
$_['error_warning_permission']	= 'У вас нет разрешения на отправку сообщения!';
$_['error_module_disabled']		= 'Модуль отключен!';
$_['error_message_required']	= 'Поле сообщение не может быть пустым!';