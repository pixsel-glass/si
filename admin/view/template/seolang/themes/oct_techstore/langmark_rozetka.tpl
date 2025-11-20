<?php if (count($languages) > 1) { ?>
	<style>
		.langmark {
			margin-top: 0px;
			margin-right: 26px;
			color: #DDD;
		}

		.langmark a {
			color: #DDD;
		}

		.langmark a:hover {
			color: #EEE;
			text-decoration: none;
		}

		.langmark li {
			display: inline;
			margin: 0px 0px 0px 4px;
			padding: 0;
		}

		.langmark a.langmarkactive {
			color: #FFF;
			font-weight: bold;
		}

		.langmark .uf {
			float: left;
			width: 16px;
			height: 11px;
			margin-right: 4px;
			padding: 0;
			margin-top: 4px;
			background-image: linear-gradient(to bottom, #0082D1, #0082D1 51%, #FFD100 49%, #FFD100);
		}
	</style>
	<div id="language" class="language nav langmark">
		<ul class="list-inline">
			<li><div class="uf"></div></li>
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