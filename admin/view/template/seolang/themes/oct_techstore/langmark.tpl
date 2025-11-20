<?php if (count($languages) > 1) { ?>

  <div id="language" class="language">
    <div id="form-language">

      <div class="btn-group">
        <button class="btn btn-link dropdown-toggle btn-language" data-toggle="dropdown">
          <i class="fa fa-globe" aria-hidden="true"></i> <span class="visible-xs visible-sm hidden-md hidden-lg"><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
        <ul class="dropdown-menu">
          <?php foreach ($languages as $language) { ?>
            <?php if ($language['main']) { ?>
              <li <?php if ($language['current']) { ?> class="active-item" <?php } ?>><button class="btn btn-link btn-block language-select" type="button" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></button></li>
            <?php } else { ?>
              <li <?php if ($language['current']) { ?> class="active-item" <?php } ?>><button class="btn btn-link btn-block language-select" type="button" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'"><?php echo $language['name']; ?></button></li>
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