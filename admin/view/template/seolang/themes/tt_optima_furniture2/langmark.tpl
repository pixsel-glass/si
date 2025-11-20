<?php if (count($languages) > 1) { ?>
	<div class="pull-left">
		<div id="language">
			<div class="btn-group">

				<ul class="dropdown-menu1">
					<?php foreach ($languages as $language) { ?>

						<li <?php if ($language['current']) { ?> class="active" <?php } ?>>
							<?php if ($language['main']) { ?>
								<a href="<?php echo $language['url']; ?>" onclick='lm_deleteCookie("languageauto"); window.location = "<?php echo $language['url']; ?>"'>
								<?php } else { ?>
									<a href="<?php echo $language['url']; ?>" onclick='lm_setCookie("languageauto", "1", {expires: 180}); window.location = "<?php echo $language['url']; ?>"'>
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