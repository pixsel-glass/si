<?php if (count($languages) > 1) { ?>
	<div id="form-language"><img src="catalog/language/<?php echo $code; ?>/<?php echo $code; ?>.png" alt="" title="">
		<select name="code" class="noselect" onchange="window.location = $(this).val();">
			<?php foreach ($languages as $language) { ?>
				<?php if ($language['current']) { ?>
					<option value="<?php echo $language['url']; ?>" selected="selected"><?php echo $language['name']; ?></option>
				<?php } else { ?>
					<option value="<?php echo $language['url']; ?>"><?php echo $language['name']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>

	</div>
<?php } ?>