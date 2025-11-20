<?php if (count($languages) > 1) { ?>
	<div class="box-language">
		<div id="form-language">
			<div class="btn-group toggle-wrap hidden-xs hidden-sm">
				<span class="dropdown-toggle" data-toggle="dropdown">
					<?php foreach ($languages as $language) { ?>
						<?php if ($language['current']) { ?>
							<span><?php echo $text_language; ?></span>
						<?php } ?>
					<?php } ?>
					<span class="hidden-xs hidden-sm hidden-md hidden"><?php echo $text_language; ?></span>
				</span>
				<ul class="dropdown-menu list-unstyled">
					<?php foreach ($languages as $language) { ?>
						<?php if ($language['current']) { ?>
							<li>

								<?php if ($language['main']) { ?>
									<button class="language-select selected" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
									<?php } else { ?>
									<button class="language-select selected" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
									<?php } ?>
									<?php echo $language['name']; ?>
									</button>
							</li>
						<?php } else { ?>
							<li>
							<?php if ($language['main']) { ?>
									<button class="language-select" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
									<?php } else { ?>
									<button class="language-select" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
									<?php } ?>
									<?php echo $language['name']; ?>
									</button>
							</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
			<ul class="hidden-md hidden-lg">
				<?php foreach ($languages as $language) { ?>
					<?php if ($language['current']) { ?>
						<li>
						<?php if ($language['main']) { ?>
									<button class="language-select selected" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
									<?php } else { ?>
									<button class="language-select selected" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
									<?php } ?>
									<?php echo $language['name']; ?>
									</button>
						</li>
					<?php } else { ?>
						<li>
						<?php if ($language['main']) { ?>
									<button class="language-select" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
									<?php } else { ?>
									<button class="language-select" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
									<?php } ?>
									<?php echo $language['name']; ?>
									</button>
						</li>
					<?php } ?>
				<?php } ?>
			</ul>

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