<?php
/**
 * NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
 *
 * El uso de este software está sujeto a las Condiciones de uso de software que
 * se incluyen en el paquete en el documento "Aviso Legal.pdf". También puede
 * obtener una copia en la siguiente url:
 * http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
 *
 * Redsys es titular de todos los derechos de propiedad intelectual e industrial
 * del software.
 *
 * Quedan expresamente prohibidas la reproducción, la distribución y la
 * comunicación pública, incluida su modalidad de puesta a disposición con fines
 * distintos a los descritos en las Condiciones de uso.
 *
 * Redsys se reserva la posibilidad de ejercer las acciones legales que le
 * correspondan para hacer valer sus derechos frente a cualquier infracción de
 * los derechos de propiedad intelectual y/o industrial.
 *
 * Redsys Servicios de Procesamiento, S.L., CIF B85955367
 */
$_['heading_title']		= 'Redsys';
 
// Text
$_['text_payment']		= 'Оплата';
$_['text_success']		= 'Изменения примененились успешно!';
$_['text_redsys']       = '<a href="http://www.redsys.es" target="_blank"><img src="view/image/extension/payment/Redsys.png" style="border: 1px solid #EEEEEE;" width="94px" /></a>';
$_['text_real']         = 'Real';//'https://sis.redsys.es/sis/realizarPago';
$_['text_sisd']         = 'Sis-d';//'https://sis-d.redsys.es/sis/realizarPago';
$_['text_sisi']         = 'Sis-i';//'https://sis-i.redsys.es:25443/sis/realizarPago';
$_['text_sist']       	= 'Sis-t';//'https://sis-t.redsys.es:25443/sis/realizarPago';
$_['text_all_zones']       	= 'Все зоны';

// Entry
$_['entry_entorno']	 = 'Redsys состояние:';
$_['entry_nombre']   = 'Commerce name:';
$_['entry_fuc']      = 'Commerce number (FUC):';
$_['entry_tipopago'] = 'Типы оплаты разрешены:';
$_['entry_clave256'] = 'Секретный ключ шифрования (SHA-256):';
$_['entry_term']     = 'Терминал номер:';
$_['entry_moneda']   = 'Тип валюты:';
//$_['entry_trans'] 	 = 'Tipo de transacción :';
$_['entry_log'] 	 = 'Активировать журнал:';
$_['entry_error_pedido'] = 'Сохраняйте запрос, если возникает ошибка:';
$_['entry_activar_3ds']  = 'Активировать дополнительные данные для EMV 3DS:';

$_['entry_notif']  	 = 'Уведомление о HTTP (неактивное не обрабатывает заказ и не опустошит корзину):';
$_['entry_error'] 	 = 'В случае ошибки разрешить выбрать другое средство оплаты:';
$_['entry_idiomas']  = 'Активировать языки TPV:';
 
$_['entry_status']   = 'Статус:';
$_['entry_order_status'] = 'Статус заказа:';
$_['entry_sort_order']   = 'Сортировка:';

$_['entry_total']        = 'Всего: общая проверка общая сумма Заказ должен достичь до того, как этот метод станет активным.';
$_['entry_geo_zone']     = 'Гео зона:';
 
// Error
$_['error_permission']	= 'Предупреждение: у вас нет разрешений на изменение Redsys!';
$_['error_nombre']		= 'Необходимо написать имя торговли!';
$_['error_fuc']			= 'Необходимо написать FUC торговли!';
$_['error_clave256']	= 'Необходимо написать ключ для торговли!';
$_['error_term']		= 'Необходимо написать торговый терминал!';
$_['error_trans']		= 'Необходимо правильно написать тип торговой транзакции!';

$_['entry_completo']	 = 'Статус для выполненных заказов:';
$_['entry_cancelado']	 = 'Статус отмененных заказов:';

?>