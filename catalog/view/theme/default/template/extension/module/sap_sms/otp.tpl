<?php if (!empty($mainstatus) || !empty($otpstatus)) { ?>
<div class="popup-window" id="popup-otp">
	<button type="button" class="close-popup-otp btn-close-popup"><span></span></button>
	<input type="hidden" class="<?php echo $otppage; ?>otpstatus" value="<?php echo $loginotpstatus; ?>">
	<input type="hidden" name="page" value="<?php echo $otppage; ?>">
	<div class="inner">
		<div class="popup-title"><?php echo $text_otp_verification; ?></div>
		<div class="popup-text otp-info"></div>
		<div class="popup-form">
			<form id="otp<?php echo $otppage; ?>confirm" class="form" action="#" novalidate method="post" enctype="multipart/form-data">
				<div class="form-group group-telephoneotp">
					<input class="form-field numeric" disabled="" autocomplete="off" type="tel" name="telephone" id="input-otp-telephone" value="<?php echo $tel_otp ?>" required>
					<?php if ($sociallogin) { ?>
					<span class="edit-field">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12"><path fill="var(--c-pen)" d="M11.406 3.561l-.414.414L7.995.97l.414-.415a1.994 1.994 0 012.826 0l.171.172a2.007 2.007 0 010 2.834zM2.999 11.99H0V8.985l6.995-7.013 2.998 3.005-6.994 7.013zM1 9.4v1.589h1.584L8.58 4.977 6.995 3.389 1 9.4zm10.992 2.591H5.996v-1.002h5.996v1.002"/><path fill="var(--c-pen-hover)" d="M1 9.4v1.589h1.584L8.58 4.977 6.995 3.389 1 9.4"/></svg>
					</span>
					<?php } ?>
				</div>
				<div class="otp-input">
					<fieldset class="otp-input_field">
						<input inputmode="numeric" autocomplete="one-time-code" name="oneTimePassword" maxlength="<?php echo $otplength ?>" id="otp<?php echo $otppage; ?>" class="otpinput<?php echo $otppage; ?> onetime" required>
					</fieldset>
					<span class="otp-input_message"></span>
				</div>
				<?php if (isset($resendtime) && ($resendtime > 0)) { ?>
				<button type="button" style="display:none;" class="btn_resend resendotp<?php echo $otppage; ?>"><strong><?php echo $button_resend; ?></strong></button>
				<div class="group timerset_for_<?php echo $otppage; ?> text-center">
					<div><?php echo $text_pleaswait; ?><b id="resendotptime_for_<?php echo $otppage; ?>"></b><?php echo $text_seconds; ?></div>
					<progress value="0" max="<?php echo $resendtime; ?>" id="progressBarTimer<?php echo $otppage; ?>"></progress>
				</div>
				<?php } ?>
			</form>
		</div>
	</div>
	<script id="otp" src="catalog/view/javascript/sap_sms/sap_verify.js"></script>
	<style>.onetime{width:<?php echo $otplength_width;?>ch;}</style>
	<script id="otpAutoRead">
		async function autoReadSMS() {
			if ('OTPCredential' in window && 'credentials' in navigator) {
				try {
					const input = document.querySelector('input[autocomplete="one-time-code"]');
					if (!input) {
						return;
					}

					const SIGNAL_TIMEOUT = 1 * 60 * 1000;
					const signal = new AbortController();
					setTimeout(() => signal.abort(), SIGNAL_TIMEOUT);
					const content = await navigator.credentials.get({ abort: signal, otp: { transport: ['sms'] } }).catch(e => console.error(e));
					if (content && content.code) {
						input.value = content.code;
						const inputEvent = new Event('input');
						const changeEvent = new Event('change');
						const keyupEvent = new KeyboardEvent('keyup');

						input.dispatchEvent(inputEvent);
						input.dispatchEvent(changeEvent);
						input.dispatchEvent(keyupEvent);
					}

					navigator.credentials.preventSilentAccess();
				}
				catch (err) {
					console.log(err);
				}
			}
		}

		autoReadSMS();
	</script>
</div>
<?php } ?>