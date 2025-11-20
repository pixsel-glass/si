<?php if (count($languages) > 1) { ?>
  <li class="dropdown" id="language-dropdown">
    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-globe"></i> <span class="hidden-lg hidden-md hidden-sm"><?php echo $text_language; ?> <i class="fa fa-angle-down"></i></span></a>
    <ul class="dropdown-menu keep-open">
      <?php foreach ($languages as $language) { ?>
        <li <?php if ($language['current']) { ?> class="active" <?php } ?>>

          <?php if ($language['main']) { ?>
            <a href="<?php echo $language['url']; ?>" class="language-select" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
            <?php } else { ?>
              <a href="<?php echo $language['url']; ?>" class="language-select" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
              <?php } ?>

              <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
                <span><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"></span>&nbsp;
              <?php } ?>

              <?php echo $language['name']; ?></a>
        </li>
      <?php } ?>
    </ul>
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