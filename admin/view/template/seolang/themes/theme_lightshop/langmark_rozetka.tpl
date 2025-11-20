<?php if (count($languages) > 1) { ?>
	<style>
		.header--version2 .header__nav-top {
			margin-right: 10px !important;
		}

		.langmark {
			margin-top: 0px;
			margin-right: 0px;
			margin-left: 8px;
			color: #343434;
			width: 120px;
		}

		.langmark a {
			color: #343434;
		}

		.langmark a:hover {
			color: #343434;
			/* text-decoration: underline; */
		}

		.langmark li {
			display: inline;
			margin: 0px 0px 0px 2px;
			padding: 0;
		}

		.langmark a.langmarkactive {
			color: #343434;
			font-weight: bold;
		}
	</style>
	<div class="header__languages langmark">
		<ul class="list-inline">
			<li><img src="/catalog/language/uk-ua/uk-ua.png"></li>
			<?php foreach ($languages as $language) { ?>
				<?php if ($language['main']) { ?>
					<li><span><a href="<?php echo $language['url']; ?>" <?php if ($language['current']) { ?> class="langmarkactive" <?php } ?> onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></a></span></li>
				<?php } else { ?>
					<li><span><a href="<?php echo $language['url']; ?>" <?php if ($language['current']) { ?> class="langmarkactive" <?php } ?> onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></a></span></li>
				<?php } ?>
				<?php
				if ($language !== end($languages)) {
					echo "<li>|</li>";
				}
				?><?php } ?>
		</ul>
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