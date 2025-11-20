<?php if (count($languages) > 1) { ?>

  <div class="pull-right">
    <div id="langmark">

      <div class="btn-group">
        <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
          <?php foreach ($languages as $language) { ?>
            <?php if ($language['current']) { ?>
              <i class="fa fa-globe" aria-hidden="true" title="<?php echo $language['name']; ?>"></i>
            <?php } ?>
          <?php } ?>
          <span class="hidden-xs"><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
        <ul class="dropdown-menu dropdown-menu-right">
          <?php foreach ($languages as $language) { ?>

            <?php if ($language['main']) { ?>
              <li><a href="<?php echo $language['url']; ?>" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'" data-code="<?php echo $language['code']; ?>">
                  <?php if (VERSION >= 2.2) { ?>
                    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                      <img src="<?php echo HTTPS_SERVER; ?>catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
                    <?php } ?>
                    <?php echo $language['name']; ?>
                  <?php } else { ?>
                    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                      <img src="<?php echo HTTPS_SERVER; ?>image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
                    <?php } ?>
                    <?php echo $language['name']; ?>
                  <?php } ?>
                </a></li>
            <?php } else { ?>
              <li><a href="<?php echo $language['url']; ?>" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'" data-code="<?php echo $language['code']; ?>">
                  <?php if (VERSION >= 2.2) { ?>
                    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                      <img src="<?php echo HTTPS_SERVER; ?>catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
                    <?php } ?>
                    <?php echo $language['name']; ?>
                  <?php } else { ?>
                    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                      <img src="<?php echo HTTPS_SERVER; ?>image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
                    <?php } ?>
                    <?php echo $language['name']; ?>                    
                  <?php } ?>
                </a></li>


            <?php } ?>
          <?php } ?>
        </ul>
      </div>



    </div>
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