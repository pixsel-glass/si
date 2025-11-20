<?php if (count($languages) > 1) { ?>
	<style>
		.langmark {
			margin-top: 15px;
			margin-left: 4px;
			margin-right: 4px;
			color: #FFF;
		}


		@media (max-width: 768px) {
			.langmark a {
			color: #777 !important;
		}			

		.langmark {
			margin-top: 10px;
			margin-left: 14px;
			margin-right: 24px;
			color: #777;
		}
		.langmark a.langmarkactive {
			color: #777;
			font-weight: bold;
		}


		}



		.langmark a {
			color: #EEE;
		}

		.langmark a:hover {
			color: #FEFEFE;
			text-decoration: underline;
		}

		.langmark li {
			text-align: center; 
			vertical-align: middle;			
			display: inline;
			margin: 0 0 0 4px;
			padding: 0;
		}

		.langmark a.langmarkactive {
			color: #FFF;
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
	<li>
	<div id="language" class="language nav langmark">
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