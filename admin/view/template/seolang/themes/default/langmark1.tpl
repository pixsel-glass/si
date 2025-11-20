<?php if (count($languages) > 1) { ?>
	<style>
		.langmark {
			margin-top: 0px;
			color: #888;
		}

		.langmark a {
			color: #888;
		}

		.langmark a:hover {
			color: #333;
		}

		.langmark li {
			display: inline;
			margin: 0 0 0 0px;
			padding: 0;
		}

		.langmark a.langmarkactive {
			color: #555;
			font-weight: bold;
		}
	</style>
	<div id="language">
		<div class="nav pull-left langmark">
			<ul class="list-inline">
				<?php foreach ($languages as $language) { ?>
					<?php if ($language['main']) { ?>
						<li><a href="<?php echo $language['url']; ?>" <?php if ($language['current']) { ?> class="langmarkactive" <?php } ?> onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></a></li>
					<?php } else { ?>
						<li><a href="<?php echo $language['url']; ?>" <?php if ($language['current']) { ?> class="langmarkactive" <?php } ?> onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></a></li>
					<?php } ?>
					<?php
					if ($language !== end($languages)) {
						echo "<li>|</li>";
					}
					?><?php } ?>
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