<?php if (count($languages) > 1) { ?>
	<style>

		.lm_modal .modal-content {
   			min-height: 200px;
		}

		.langmark {
			margin-top: 6px;
			margin-right: 0;
			color: #888;
		}

		.langmark a {
			color: #888;
		}

		.langmark a:hover {
			color: #555;
			text-decoration: none;
		}

		.langmark li {
			text-align: center; 
			vertical-align: middle;			
			display: inline;
			margin: 0 0 0 4px;
			padding: 0;
		}

		.langmark a.langmarkactive {
			color: #333;
			font-weight: bold;
		}

		.langmark .uf {
			/* transform: translateY(-10%); */
			text-align: center; 
			vertical-align: middle;			
			display: inline-block;
			width: 16px;
			height: 16px;
			border-radius: 50%;
			margin-right: 0px;
			margin-top: -3px;
			background-image: linear-gradient(to bottom, #0082D1, #0082D1 50%, #FFD100 50%, #FFD100);
		}
	</style>
	<div id="language" class="pull-left language nav langmark">
		<ul class="list-inline">
			
			<?php foreach ($languages as $language) { ?>

				<?php if ($language['code_short'] == 'uk' || $language['code_short'] == 'ua') { ?>
					<li><div class="uf"></div></li>
				<?php } ?>

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