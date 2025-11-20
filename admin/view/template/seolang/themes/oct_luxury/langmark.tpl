<?php if (count($languages) > 1) { ?>
	<li class="top-language">
		<div class="pull-left" id="language-div">
			<span id="form-language">
				<div class="btn-group">
					<button class="btn btn-link dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
						<?php foreach ($languages as $language) { ?>
							<?php if ($language['current']) { ?>
								<?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
									<img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
								<?php } ?>
							<?php } ?>
						<?php } ?>
						<span><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
					<ul class="dropdown-menu dropdown-menu-right">
						<?php foreach ($languages as $language) { ?>
							<?php if ($language['main']) { ?>
								<li><button class="btn btn-link btn-block language-select" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></button></li>
							<?php } else { ?>
								<li><button class="btn btn-link btn-block language-select" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></button></li>

							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</span>
		</div>
	</li>

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