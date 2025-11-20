<?php

$_['heading_title'] = "Notification to Telegram";
$_['text_success']  = "Settings is saved";
$_['text_extension'] = "Modules";
$_['text_home'] = "Marketplace";
$_['error_permission'] = "Error: You haven't permission for this operation";
$_['button_save'] = "Save";
$_['button_cancel'] = "Cancel";
$_['text_edit'] = "Edit Settings";
$_['entry_status'] = "Module status:";
$_['text_enabled'] = "Enable";
$_['text_disabled'] = "Disable";
$_['bot_id_label'] = "Bot id:";
$_['recipient_name'] = "Name";
$_['recipient_id'] = "Recipient ID";
$_['button_remove'] = "Remove ";
$_['text_recipient_placeholder_name'] = "Name";
$_['text_recipient_placeholder_id'] = "Input recipient ID";
$_['textarea_message_label'] = "The message that the manager will receive:";
$_['textarea_message_description'] = "Use these variables to substitude values:<br/><br/> &nbsp<b>{invoiceOrder}</b> - order number;<br/>
																					 &nbsp<b>{products}</b> - ordered products;<br/>
																					 &nbsp<b>{recipientName}</b> - name of recipient;<br/>
																					 &nbsp<b>{email}</b> - customer email;<br/>
																					 &nbsp<b>{telephone}</b> - customer telephone;<br/>
																					 &nbsp<b>{paymentMethod}</b> - payment method;<br/>
																					 &nbsp<b>{shippingMethod}</b> - shipping method;<br/>
																					 &nbsp<b>{orderStatus}</b> - order status;<br/>
																					 &nbsp<b>{opcNotCallMe}</b> - manager call;<br/>
																					 &nbsp<b>{shippingAddress}</b> - shipping address;";
$_['textarea_message_example'] = "New order {invoiceOrder}
Products: {products}
Payment method: {paymentMethod}
Shipping method: {shippingMethod}
Shipping address: {shippingAddress}
Recipient: {recipientName}
Email: {email}
Phone: {telephone}
Order status: {orderStatus}";

$_['textarea_quickorder_label'] = "Fast order message:";
$_['textarea_quickorder_description'] = "Use these variables to substitute the value:<br/><br/> &nbsp<b>{product}</b> - ordered product;<br/>
																					 &nbsp<b>{quantity}</b> - Quantity;<br/>
																					 &nbsp<b>{recipientName}</b> - user name;<br/>
																					 &nbsp<b>{email}</b> - user email;<br/>
																					 &nbsp<b>{telephone}</b> - user phone;<br/>
																					 &nbsp<b>{comment}</b> - comment;";
$_['textarea_quickorder_example'] = "Ordered: {product}
Quantity: {quantity}
Recipient: {recipientName}
Email: {email}
Phone: {telephone}
Comment: {comment}";

$_['textarea_callback_label'] = "Feedback message:";
$_['textarea_callback_description'] = "Use these variables to substitute the value:<br/><br/> &nbsp<b>{name}</b> - user name;<br/>
																					 &nbsp<b>{telephone}</b> - phone;<br/>
																					 &nbsp<b>{comment}</b> - comment;";
$_['textarea_callback_example'] = "Name: {name}
Phone: {telephone}
Comment: {comment}";

$_['textarea_newuser_label'] = "Message about a new user:";
$_['textarea_newuser_description'] = "Use these variables to substitute the value:<br/><br/> &nbsp<b>{name}</b> - user name;<br/>
																					 &nbsp<b>{email}</b> - email;<br/>
																					 &nbsp<b>{telephone}</b> - phone;";
$_['textarea_newuser_example'] = "Name: {name}
Email: {email}
Phone: {telephone}";

$_['textarea_contact_label'] = "Message on the Contacts page:";
$_['textarea_contact_description'] = "Use these variables to substitute the value:<br/><br/> &nbsp<b>{name}</b> - user name;<br/>
																					 &nbsp<b>{email}</b> - email;<br/>
																					 &nbsp<b>{message}</b> - Message;";
$_['textarea_contact_example'] = "Name: {name}
Email: {email}
Message: {message}";

$_['textarea_orderstatus_label'] = "Message about order status change:";
$_['textarea_orderstatus_description'] = "Use these variables to substitute the value:<br/><br/> &nbsp<b>{invoiceOrder}</b> - order number;<br/>
																					 &nbsp<b>{orderStatus}</b> - order status;<br/>
																					 &nbsp<b>{comment}</b> - comment;";
$_['textarea_orderstatus_example'] = "Order number {invoiceOrder}
Order status: {orderStatus}
Comment: {comment}";

$_['textarea_pricelevel_label'] = "Message about changes in client price levels:";
$_['textarea_pricelevel_description'] = "Use these variables to substitute the value:<br/><br/> &nbsp<b>{name}</b> - user name;<br/>
																					 &nbsp<b>{level}</b> - price level;";
$_['textarea_pricelevel_example'] = "Name: {name}
Price level: {level}";