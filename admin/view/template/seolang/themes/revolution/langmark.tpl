<?php if (count($languages) > 1) { ?>
  <span id="language">
    <li class="dropdown pull-left"><span class="span-a dropdown-toggle" data-toggle="dropdown">
        <?php foreach ($languages as $language) { ?>
          <?php if ($language['code'] == $code) { ?>
            <?php echo $language['name']; ?>
          <?php } ?>
        <?php } ?>
        <span class="hidden-xs hidden-sm hidden-md"></span><i class="fa fa-chevron-down strdown"></i></span>
      <ul class="dropdown-menu dropdown-menu-right">
        <?php foreach ($languages as $language) { ?>
          <?php if ($language['main']) { ?>
            <li><a href="<?php echo $language['url']; ?>" onclick='lm_deleteCookie("languageauto"); window.location = "<?php echo $language['url']; ?>"'><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="<?php echo (VERSION >= 2.2) ? 'catalog/language/' . $language['code'] . '/' . $language['code'] . '.png' : 'image/flags/' . $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a></li>
          <?php } else { ?>
            <li><a href="<?php echo $language['url']; ?>" onclick='lm_setCookie("languageauto", "1", {expires: 180}); window.location = "<?php echo $language['url']; ?>"'><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="<?php echo (VERSION >= 2.2) ? 'catalog/language/' . $language['code'] . '/' . $language['code'] . '.png' : 'image/flags/' . $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a></li>
          <?php } ?>
        <?php } ?>
      </ul>
    </li>
  </span>
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