<?php if (count($languages) > 1) { ?>

	<div class="box-language">
		<div id="language">

			<div class="btn-group toggle-wrap">
				<span class="toggle">
					<?php foreach ($languages as $language) { ?>
						<?php if ($language['code'] == $code) { ?>
							<?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
								<img class="hidden" src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
							<?php } ?>
							<?php echo $language['name']; ?>
						<?php } ?>
					<?php } ?>
					<span class="hidden-xs hidden-sm hidden-md hidden"><?php echo $text_language; ?></span> </span>
				<ul class="toggle_cont pull-right">
					<?php foreach ($languages as $language) { ?>

						<?php if ($language['main']) { ?>
							<li><a href="<?php echo $language['url']; ?>" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?> <img class="hidden" src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a></li>
						<?php } else { ?>
							<li><a href="<?php echo $language['url']; ?>" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?> <img class="hidden" src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a></li>
						<?php } ?>



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