<?php if (count($languages) > 1) { ?>
	<div class="pull-left">
		<div id="form-language">
			<div class="open_btn btn-group">
				<button class="serv2_none btn btnh btn-link dropdown-toggle" data-toggle="dropdown">
					<?php foreach ($languages as $language) { ?>
						<?php if ($language['current']) { ?>
							<?php echo $language['name']; ?>
						<?php } ?>
					<?php } ?>
					<span class="hidden-xs hidden-sm hidden-md"><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
				<ul class="serv2_pos dropdown-menu">
					<?php foreach ($languages as $language) { ?>
						<li <?php if ($language['current']) { echo 'class="active"'; } ?>>
							<?php if ($language['main']) { ?>
								<button class="btn btnh btn-link btn-block btn_left language-select" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></button>
							<?php } else { ?>
								<button class="btn btnh btn-link btn-block btn_left language-select" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></button>
							<?php } ?>
						</li>
					<?php } ?>
				</ul>
			</div>
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
			lm_setCookie(name, "", {
				'max-age': -1
			});
		}
	</script>
<?php } ?>