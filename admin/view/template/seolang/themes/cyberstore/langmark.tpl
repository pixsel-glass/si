<?php if (count($languages) > 1) {
  $variant_view_lang = 2; ?>
  <div class="variant_lang_2">
    <div id="language">
      <span class="mob-title-lang"><?php echo $text_language; ?></span>
      <div class="list_lang">
        <?php foreach ($languages as $language) { ?>
          <?php if ($language['current']) { ?>
            <span class="item_lang active"><?php echo $language['name']; ?></span>
          <?php } else { ?>
            <?php if ($language['main']) { ?>
              <span class="item_lang"><a href="<?php echo $language['url']; ?>" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></a></span>
            <?php } else { ?>
              <span class="item_lang"><a href="<?php echo $language['url']; ?>" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></a></span>
            <?php } ?>
          <?php } ?>
        <?php } ?>
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