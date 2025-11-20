<?php if (!empty($mainstatus) || !empty($otpstatus)) { ?>
<div class="form-group required otpbox<?php echo $otppage; ?>" id="otpbox">
	<label class="<?php echo $route ? 'col-sm-2 ' : ''; ?>control-label" for="<?php echo $id ?>"><?php echo $entry_telephone; ?></label>
	<?php if ($route) { ?>
	<div class="col-sm-10">
	<?php } ?>
	<div class="input-group telephone-error-<?php echo $otppage; ?>">
		<input type="hidden" name="page" value="<?php echo $otppage; ?>">
		<input type="hidden" name="otp_country_code" id="otp_country_code-<?php echo $otppage; ?>">
		<input type="hidden" name="fax" value="">
		<input type="text" inputmode="tel" id="<?php echo $id ?>" name="telephone" class="form-control<?php echo $show_country_code ? ' iti--sap-tel-input' : ''; ?> numeric telephonenew<?php echo $otppage; ?><?php echo $is_mask ? ' mask' : ''; ?>" <?php echo $show_country_code ? 'data-phone-input-id="' . $id . '" data-phone-dial-code-input="#otp_country_code-' . $otppage . '"' : ''; ?> data-config-type="sms"<?php echo $is_mask ? $mask : ''; ?> value="<?php echo $telephone ?>" placeholder="<?php echo $entry_telephone; ?>" <?php echo $maximum ? 'maxlength="' . $maximum . '"' : ''; ?> autocomplete="off">
		<span class="input-group-btn">
			<button type="button" class="btn btn-danger btnverify<?php echo $otppage; ?>" title="<?php echo $button_verify; ?>"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $button_verify; ?></button>
		</span>
	</div>
	<?php if ($route) { ?>
	</div>
	<?php } ?>
</div>
<style>.iti {position: unset !important;}</style>
<?php if ($show_country_code) { ?>
<script async>
	var sapTelSMSInputConfig = <?php echo $config_tel_input; ?>;
</script>
<?php } else { ?>
<?php if ($mask) { ?>
<script>
	var maskPhone = '<?php echo $mask; ?>';
	$('#otp_country_code-<?php echo $otppage; ?>').val(maskPhone.replace(/[^0-8]/g, ''));
</script>
<?php } ?>
<?php } ?>
<?php } ?>