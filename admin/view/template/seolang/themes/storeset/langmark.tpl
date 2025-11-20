<?php if (count($languages) > 1) { ?>
	<div class="pull-left">
		<div class="language">

			<div class="btn-group">
				<button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
					<?php foreach ($languages as $language) { ?>
						<?php if ($language['current']) { ?>
							<?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
								<img class="hidden-xs hidden-sm hidden-md hidden-lg hidden-xl" src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
							<?php } ?>
						<?php } ?>
					<?php } ?>
					<span class="<?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>hidden-xs hidden-sm hidden-md <?php } ?>"><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
				<ul class="dropdown-menu">
					<?php foreach ($languages as $language) { ?>
						<li>
							<?php if ($language['main']) { ?>
								<a href="<?php echo $language['url']; ?>" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
								<?php } else { ?>
									<a href="<?php echo $language['url']; ?>" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
									<?php } ?>
									<?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
										<img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" />
									<?php } ?>
									<?php echo $language['name']; ?></a>
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