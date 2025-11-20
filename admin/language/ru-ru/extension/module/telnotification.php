<?php

$_['heading_title'] = "Оповещения в Telegram";
$_['text_success']  = "Настройки сохранены";
$_['text_extension'] = "Модули";
$_['text_home'] = "Marketplace";
$_['error_permission'] = "Ошибка: у Вас недостаточно прав для этой операции";
$_['button_save'] = "Сохранить";
$_['button_cancel'] = "Отменить";
$_['text_edit'] = "Редактировать";
$_['entry_status'] = "Статус:";
$_['text_enabled'] = "Включен";
$_['text_disabled'] = "Отключен";
$_['bot_id_label'] = "ID бота:";
$_['recipient_name'] = "Имя";
$_['recipient_id'] = "ID получателя";
$_['button_remove'] = "Удалить";
$_['text_recipient_placeholder_name'] = "Имя";
$_['text_recipient_placeholder_id'] = "Введите ID получателя";
$_['textarea_message_label'] = "Сообщение, которое получит менеджер:";
$_['textarea_message_description'] = "Используйте эти переменные для подстановки значений:<br/><br/> &nbsp<b>{invoiceOrder}</b> - номер заказа;<br/>
																					 &nbsp<b>{orderedBy}</b> - заказ оформил менеджер;<br/>
																					 &nbsp<b>{products}</b> - заказанные товары;<br/>
																					 &nbsp<b>{recipientName}</b> - имя получателя;<br/>
																					 &nbsp<b>{email}</b> - email покупателя;<br/>
																					 &nbsp<b>{telephone}</b> - номер телефона покупателя;<br/>
																					 &nbsp<b>{paymentMethod}</b> - метод оплаты;<br/>
																					 &nbsp<b>{shippingMethod}</b> - метод доставки;<br/>
																					 &nbsp<b>{country}</b> - страна;<br/>
																					 &nbsp<b>{orderStatus}</b> - статус заказа;<br/>
																					 &nbsp<b>{opcNotCallMe}</b> - звонок менеджера;<br/>
																					 &nbsp<b>{opcInfakt}</b> - фактура;<br/>
																					 &nbsp<b>{opcVat}</b> - VAT;<br/>
																					 &nbsp<b>{orderSum}</b> - сумма заказа;<br/>
																					 &nbsp<b>{shippingAddress}</b> - адрес доставки;<br/>
																					 &nbsp<b>{comment}</b> - комментарий;";
$_['textarea_message_example'] = "Новый заказ {invoiceOrder}
Заказано: {products}
Метод оплаты: {paymentMethod}
Метод доставки: {shippingMethod}
Адрес доставки: {shippingAddress}
Имя получателя: {recipientName}
Телефон: {telephone}
Статус заказа: {orderStatus}";

$_['textarea_quickorder_label'] = "Сообщение про быстрый заказ:";
$_['textarea_quickorder_description'] = "Используйте эти переменные для подстановки значения:<br/><br/> &nbsp<b>{invoiceOrder}</b> - номер заказа;<br/>
																					 &nbsp<b>{product}</b> - заказаный товар;<br/>
																					 &nbsp<b>{quantity}</b> - Количество;<br/>
																					 &nbsp<b>{recipientName}</b> - имя получателя;<br/>
																					 &nbsp<b>{email}</b> - email получателя;<br/>
																					 &nbsp<b>{telephone}</b> - телефон получателя;<br/>
																					 &nbsp<b>{orderSum}</b> - сумма заказа;<br/>
																					 &nbsp<b>{comment}</b> - комментарий;";
$_['textarea_quickorder_example'] = "Заказано: {product}
Количество: {quantity}
Получатель: {recipientName}
Email: {email}
Телефон: {telephone}
Комментарий: {comment}";

$_['textarea_callback_label'] = "Сообщение про обратную связь:";
$_['textarea_callback_description'] = "Используйте эти переменные для подстановки значения:<br/><br/> &nbsp<b>{name}</b> - имя пользователя;<br/>
																					 &nbsp<b>{telephone}</b> - телефон;<br/>
																					 &nbsp<b>{comment}</b> - комментарий;";
$_['textarea_callback_example'] = "Имя: {name}
Телефон: {telephone}
Комментарий: {comment}";

$_['textarea_newuser_label'] = "Сообщение про нового пользователя:";
$_['textarea_newuser_description'] = "Используйте эти переменные для подстановки значения:<br/><br/> &nbsp<b>{name}</b> - имя пользователя;<br/>
																					 &nbsp<b>{type}</b> - тип пользователя;<br/>
																					 &nbsp<b>{email}</b> - email;<br/>
																					 &nbsp<b>{telephone}</b> - телефон;";
$_['textarea_newuser_example'] = "Имя: {name}
Email: {email}
Телефон: {telephone}";

$_['textarea_contact_label'] = "Сообщение на странице Контакты:";
$_['textarea_contact_description'] = "Используйте эти переменные для подстановки значения:<br/><br/> &nbsp<b>{name}</b> - имя пользователя;<br/>
																					 &nbsp<b>{email}</b> - email;<br/>
																					 &nbsp<b>{message}</b> - Сообщение;";
$_['textarea_contact_example'] = "Имя: {name}
Email: {email}
Сообщение: {message}";

$_['textarea_orderstatus_label'] = "Сообщение про изменение статуса заказа:";
$_['textarea_orderstatus_description'] = "Используйте эти переменные для подстановки значения:<br/><br/> &nbsp<b>{invoiceOrder}</b> - номер заказа;<br/>
																					 &nbsp<b>{orderStatus}</b> - статус заказа;<br/>
																					 &nbsp<b>{comment}</b> - комментарий;";
$_['textarea_orderstatus_example'] = "Номер заказа {invoiceOrder}
Статус заказа: {orderStatus}
Комментарий: {comment}";

$_['textarea_pricelevel_label'] = "Сообщение про изменение уровня цен клиента:";
$_['textarea_pricelevel_description'] = "Используйте эти переменные для подстановки значения:<br/><br/> &nbsp<b>{name}</b> - имя пользователя;<br/>
																					 &nbsp<b>{level}</b> - уровень цен;";
$_['textarea_pricelevel_example'] = "Имя: {name}
Уровень цен: {level}";