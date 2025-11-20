<?php $type = $this->journal2->settings->get('language_display', 'flag'); ?>
<?php if (count($languages) > 1) : ?>
  <?php
  $current_language = '';
  foreach ($languages as $language) {
    if ($language['code'] == $code) {
      switch ($type) {
        case 'flag':
          $current_language = "<img width=\"16\" height=\"11\" src=\"" . Journal2Utils::getLanguageFlag($language) . "\" alt=\"{$language['name']}\" />";
          break;
        case 'text':
          $current_language = "{$language['name']}";
          break;
        case 'full':
          $current_language = "<img width=\"16\" height=\"11\" src=\"" . Journal2Utils::getLanguageFlag($language) . "\" alt=\"{$language['name']}\" /> {$language['name']}";
          break;
      }
    }
  }
  ?>
  <form>

    <div id="language">

      <div class="btn-group">
        <button class="dropdown-toggle" type="button" data-hover="dropdown">
          <?php echo $current_language; ?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <?php foreach ($languages as $language) : ?>

            <?php if ($language['main']) { ?>
              <?php if ($type === 'flag') : ?>
                <li><a href="<?php echo $language['url']; ?>" onclick='lm_deleteCookie("languageauto"); window.location = "<?php echo $language['url']; ?>"'><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img width="16" height="11" src="<?php echo Journal2Utils::getLanguageFlag($language); ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><?php } ?></a></li>
              <?php endif; ?>
              <?php if ($type === 'text') : ?>
                <li><a href="<?php echo $language['url']; ?>" onclick='lm_deleteCookie("languageauto"); window.location = "<?php echo $language['url']; ?>"'><?php echo $language['name']; ?></a></li>
              <?php endif; ?>
              <?php if ($type === 'full') : ?>
                <li><a href="<?php echo $language['url']; ?>" onclick='lm_deleteCookie("languageauto"); window.location = "<?php echo $language['url']; ?>"'><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img width="16" height="11" src="<?php echo Journal2Utils::getLanguageFlag($language); ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a></li>
              <?php endif; ?>
            <?php } else { ?>

              <?php if ($type === 'flag') : ?>
                <li><a href="<?php echo $language['url']; ?>" onclick='lm_setCookie("languageauto", "1", {expires: 180}); window.location = "<?php echo $language['url']; ?>"'><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img width="16" height="11" src="<?php echo Journal2Utils::getLanguageFlag($language); ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><?php } ?></a></li>
              <?php endif; ?>
              <?php if ($type === 'text') : ?>
                <li><a href="<?php echo $language['url']; ?>" onclick='lm_setCookie("languageauto", "1", {expires: 180}); window.location = "<?php echo $language['url']; ?>"'><?php echo $language['name']; ?></a></li>
              <?php endif; ?>
              <?php if ($type === 'full') : ?>
                <li><a href="<?php echo $language['url']; ?>" onclick='lm_setCookie("languageauto", "1", {expires: 180}); window.location = "<?php echo $language['url']; ?>"'><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img width="16" height="11" src="<?php echo Journal2Utils::getLanguageFlag($language); ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a></li>
              <?php endif; ?>


            <?php } ?>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </form>

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

<?php endif; ?>