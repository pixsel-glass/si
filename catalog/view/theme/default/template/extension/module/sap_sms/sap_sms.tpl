<?php if (!empty($mainstatus) || !empty($otpstatus)) { ?>
<?php if ($popup) { ?>
<script>
if (typeof jQuery !== 'undefined' && jQuery.isReady) {
	<?php foreach ($scripts as $script) { ?>
	if (!$("script[src='<?php echo $script ?>']").length) {
        jQuery.getScript('<?php echo $script ?>');
    }
	<?php } ?>
	<?php foreach ($styles as $style) { ?>
	if (!$("link[href='<?php echo $style ?>']").length) {
        jQuery("head").append('<link href="' + '<?php echo $style ?>' + '" rel="stylesheet"/>');
    }
	<?php } ?>
}
</script>
<?php } ?>
<p class="login-info-text text-center margintop20"><?php echo $text_title_otp; ?></p>
<div class="form-group">
	<?php if ($sociallogin) { ?>
    <div class="form">
    <?php } ?>
    <div class="form-group<?php echo $sociallogin ? ' group-telephone' : ''; ?> otpbox<?php echo $otppage; ?>" id="otpbox">
        <div class="input-group telephone-error-<?php echo $otppage; ?>">
            <input type="hidden" name="page" value="<?php echo $otppage; ?>">
            <input type="hidden" name="otp_country_code" id="otp_country_code-<?php echo $otppage; ?>">
			<input type="text" inputmode="tel" id="<?php echo $id ?>" name="telephone" class="<?php echo $sociallogin ? 'form-field' : 'form-control'; ?><?php echo $show_country_code ? ' iti--sap-tel-input' : ''; ?> numeric telephonenew<?php echo $otppage; ?><?php echo $is_mask ? ' mask' : ''; ?>" <?php echo $show_country_code ? 'data-phone-input-id="' . $id . '" data-phone-dial-code-input="#otp_country_code-' . $otppage . '"' : ''; ?> data-config-type="sms"<?php echo $is_mask ? $mask : ''; ?> value="<?php echo $telephone ?>" placeholder="<?php echo $entry_telephone; ?>" <?php echo $maximum ? 'maxlength="' . $maximum . '"' : ''; ?> autocomplete="off">
			<span class="input-group-btn">
                <button class="<?php echo $sociallogin ? 'send_otp_btn otp_btn-s' : 'btn btn-success'; ?> btnverify<?php echo $otppage; ?>" type="button" title="<?php echo $button_login; ?>"><i class="fa fa-check" aria-hidden="true"></i> <?php echo $button_login; ?></button>
            </span>
        </div>
    </div>
    <?php if ($sociallogin) { ?>
    </div>
    <?php } else { ?>
	<style>.iti {position: unset !important;}</style>
	<?php } ?>
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
</div>
<?php } ?>