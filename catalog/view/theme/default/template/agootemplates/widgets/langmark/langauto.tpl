<?php if (isset($langmark)) { ?>
	<div id="cmswidget-<?php echo $cmswidget; ?>" class="cmswidget">
		<?php echo ($langmark); ?>
	</div>
	<?php if (isset($settings_widget['anchor']) && $settings_widget['anchor'] != '') { ?>
		<script>
			$('#cmswidget-<?php echo $cmswidget; ?>').hide();
			<?php if (isset($settings_widget['doc_ready']) && $settings_widget['doc_ready']) { ?>
				$(document).ready(function() {
				<?php  } ?>
				var prefix = '<?php echo $prefix; ?>';
				var cmswidget = '<?php echo $cmswidget; ?>';
				var heading_title = '<?php echo $heading_title; ?>';
				var data = $('#cmswidget-<?php echo $cmswidget; ?>').html();
				<?php echo $settings_widget['anchor']; ?>;
				$('#cmswidget-<?php echo $cmswidget; ?>').show();
				delete data;
				delete prefix;
				delete cmswidget;
				<?php if (isset($settings_widget['doc_ready']) && $settings_widget['doc_ready']) { ?>
				});
			<?php  } ?>
		</script>

	<?php  } ?>
<?php  } else { ?>
	<?php if (count($languages) > 1 && isset($settings_widget['autoredirect']) && $settings_widget['autoredirect']) { ?>
		
		<script>
			lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state = false;

			function lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto() {
				if (!lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state) {
					document.body.removeEventListener('touchstart', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
					document.body.removeEventListener('touchmove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
					document.body.removeEventListener('mouseover', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
					document.removeEventListener('mousemove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);

					// Redirecting users who do not have language cookies to the main language of the site, as required by law
					lm_<?php echo $settings_widget['cmswidget']; ?>_autoredirect();
					lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state = true;
				}
			}
			var lm_<?php echo $settings_widget['cmswidget']; ?>_userAgent = navigator.userAgent || navigator.vendor || window.opera;
			if (/Android|iPhone|iPad|iPod|Windows Phone|webOS|BlackBerry/i.test(lm_<?php echo $settings_widget['cmswidget']; ?>_userAgent)) {
				document.body.addEventListener('touchstart', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
				document.body.addEventListener('touchmove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
				document.addEventListener('DOMContentLoaded', function() {
					setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto, <?php echo $settings_widget['autoredirect_delay_mobile']; ?>)
				});
			} else {
				document.body.addEventListener('mouseover', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
				document.addEventListener('mousemove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto);
				document.addEventListener('DOMContentLoaded', function() {
					setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload_auto, <?php echo $settings_widget['autoredirect_delay_desktop']; ?>);
				});
			}

			function lm_<?php echo $settings_widget['cmswidget']; ?>_autoredirect() {
				<?php foreach ($languages as $language) {
					if (isset($language['main']) && $language['main'] && !$language['current'] && isset($settings_widget['autoredirect']) && $settings_widget['autoredirect']) {
				?>
						window.location = '<?php echo $language['url']; ?>';
				<?php
					}
				} ?>
			}
		</script>
	<?php } ?>
<?php } ?>