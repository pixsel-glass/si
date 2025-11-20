<?php if (count($languages) > 1) { ?>

<div class="box-language hidden-xs hidden-sm">
<div id="language">

<div class="btn-group">
<button class="btn-language-top dropdown-toggle" data-toggle="dropdown">
<?php foreach ($languages as $language) { ?>
<?php if ($language['current']) { ?>
<?php echo $language['name']; ?>
<?php } ?>
<?php } ?>
</button>
<ul class="dropdown-menu dropdown-menu-right ch-dropdown">
<li class="mob-title-language visible-xs"><?php echo $text_language; ?></li>
<?php foreach ($languages as $language) { ?>
<li <?php if ($language['current']) { ?>class="active"<?php } ?>>

<?php if ($language['main']) { ?>
<button class="btn-lang-select" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></button>
<?php } else { ?>
<button class="btn-lang-select" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></button>
<?php } ?> 

</li>
<?php } ?>
</ul>
</div>

<span class="mob-text-language"><?php echo $text_language; ?></span>

</div>
</div>
<script>
function lm_setCookie(name, value, options = {}) {
	options = {
	path: '/',
	...options
	};

	let date = new Date(Date.now() + (86400e3 * options.expires));
	date = date.toUTCString();
	options.expires = date;

	let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

	for (let optionKey in options) {
		updatedCookie += "; " + optionKey;
		let optionValue = options[optionKey];
		if (optionValue !== true) {
		updatedCookie += "=" + optionValue;
		}
	}
	document.cookie = updatedCookie;
}

function lm_deleteCookie(name) {
	lm_setCookie(name, "", {'max-age': -1});
}
</script>
<?php } ?>

