function get_brightness($hex) { //src: http://www.webmasterworld.com/forum88/9769.htm
	// returns brightness value from 0 to 255

	// strip off any leading #
	$hex = $hex.replace('#', '');
	
	$c_r = hexdec($hex.substr(0, 2));
	$c_g = hexdec($hex.substr(2, 2));
	$c_b = hexdec($hex.substr(4, 2));

	return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

function hexdec (hex_string) { //src: http://phpjs.org/functions/hexdec:423
	// Returns the decimal equivalent of the hexadecimal number  
	// 
	// version: 1109.2015
	// discuss at: http://phpjs.org/functions/hexdec    // +   original by: Philippe Baumann
	// *     example 1: hexdec('that');
	// *     returns 1: 10
	// *     example 2: hexdec('a0');
	// *     returns 2: 160
	
	hex_string = (hex_string + '').replace(/[^a-f0-9]/gi, '');
	return parseInt(hex_string, 16);
}

function displayColor(jObj, hex)
{
	if(hex == null)
		hex = jObj.val();
		
	// strip off any leading #
	hex = hex.replace('#', '');

	jObj.css(
	{
		backgroundColor: '#' + hex,
		color: get_brightness(hex) > 130 ? '#000000' : '#ffffff'
	});
				
	jObj.val('#' + hex);
}

function colorPickerise(jObj)
{
	jObj.ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val('#' + hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		},
		onChange: function (hsb, hex, rgb, el) {
			displayColor($(el), hex);
		}
	}).bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
		displayColor($(this));
	}).each(function(i){
		displayColor($(this));
	});;
}
