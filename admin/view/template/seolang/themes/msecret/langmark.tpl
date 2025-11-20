<?php if (count($languages) > 1) { ?>
	<div class="pull-left moblang">
		<div id="language">
			<div class="btn-group">


				<ul class="dropdown-menu2 list-inline">

					<?php foreach ($languages as $language) { ?>

						<?php if ($language['code_short'] == 'uk' || $language['code_short'] == 'ua') { ?>
							<li>
								<div class="uf"></div>
							</li>
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

		</div>
	</div>

	<style>
		.langmark {

			margin-right: 0;
			color: #DDD;
			height: 21px;
			margin-top: -10px;
		}

		.langmark a {
			color: #DDD;
		}

		.langmark a:hover {
			color: #FFF;
			text-decoration: none;
		}

		.langmark li {
			text-align: center;
			vertical-align: middle;
			display: inline;
			margin: 0 0 0 4px;
			padding: 0;
			color: #FFF;
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
	<style>
		#language .dropdown-menu2 {
			list-style: none;
			margin: 0 !important;
			;
			padding: 0 !important;
		}

		#language ul li a {
			color: #fff;
			display: inline-block;

		}

		#language ul li:after {

			margin: 0 1px 0 6px;
			color: #fff;
		}

		#language ul li {
			display: inline-block;
			margin: 0;
			padding: 0;
			color: #FFF;
		}

		#language {
			margin-top: -9px;
			margin-bottom: -6px;
		}

		@media screen and (max-width: 768px) {
			#language .dropdown-menu2 {
				text-align: center;
			}

			#language ul li {

				color: #000;
			}

			#language ul li a {
				color: #000;
				display: inline-block;

			}

			#language ul li:after {

				margin: 0 1px 0 6px;
				color: #000;
			}

			.moblang {
				margin: 0 auto;
				display: block;
				text-align: center;
				font-size: 1.3em;
			}

			.header-social {
				border-bottom: 1px solid #565555;
				padding-bottom: 5px;
				display: flex;
			}

			.contactus-wrapper {
				padding: 0;
				line-height: 0;
				display: block;
				text-align: right;
				margin-top: 0px
			}

			#language {
				margin-top: -3px;
				margin-bottom: -6px;
			}

		}
	</style>
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