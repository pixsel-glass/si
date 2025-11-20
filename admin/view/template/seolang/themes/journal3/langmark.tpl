<?php if (count($languages) > 1) { ?>
  <?php
  $current_language = null;
  foreach ($languages as $language) {
    if ($language['code'] == $code) {
      $current_language = $language;
    }
  }
  ?>
  <style>
    .language-currency .dropdown::after {
      content: "" !important;
    }
  </style>

  <div id="language" class="language">
    <form id="form-language-mark">

      <div class="dropdown drop-menu">
        <button type="button" class="dropdown-toggle" data-toggle="dropdown">
          <span class="language-flag-title">
            <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>

              <span class="symbol"><img src="<?php echo $j3->imageToBase64('catalog/language/' . $current_language['code'] . '/' . $current_language['code'] . '.png'); ?>" width="16" height="11" alt="<?php echo $current_language['name']; ?>" title="<?php echo $current_language['name']; ?>" /></span>
            <?php } else { ?>
              <!-- <span class="symbol"><?php echo $text_language; ?></span> -->

            <?php } ?>
            <span class="language-title"><?php echo $current_language['name']; ?></span>
          </span>
        </button>

        <div class="dropdown-menu j-dropdown">
          <ul class="j-menu">
            <?php foreach ($languages as $language) : ?>

              <?php if ($language['main']) { ?>
                <li>
                  <a href="<?php echo $language['url']; ?>" onclick='lm_deleteCookie("languageauto"); window.location = "<?php echo $language['url']; ?>"' class="language-select" data-name="<?php echo $language['code']; ?>">
                    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                      <span class="language-flag"><img src="<?php echo $j3->imageToBase64('catalog/language/' . $language['code'] . '/' . $language['code'] . '.png'); ?>" width="16" height="11" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /></span>
                    <?php } ?>
                    <span class="language-title-dropdown"><?php echo $language['name']; ?></span>
                  </a>
                </li>

              <?php } else { ?>


                <li>
                  <a href="<?php echo $language['url']; ?>" onclick='lm_setCookie("languageauto", "1", {expires: 180});  window.location = "<?php echo $language['url']; ?>"' class="language-select" data-name="<?php echo $language['code']; ?>">
                    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                      <span class="language-flag"><img src="<?php echo $j3->imageToBase64('catalog/language/' . $language['code'] . '/' . $language['code'] . '.png'); ?>" width="16" height="11" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /></span>
                    <?php } ?>
                    <span class="language-title-dropdown"><?php echo $language['name']; ?></span>
                  </a>
                </li>
              <?php } ?>


            <?php endforeach; ?>
          </ul>
        </div>

      </div>


    </form>
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