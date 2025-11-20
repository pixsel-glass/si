<?php if (count($languages) > 1) { ?>

  <div class="pull-left">
    <div id="language">

      <div class="btn-group">
        <button class="btn dropdown-toggle" data-toggle="dropdown">
          <?php foreach ($languages as $language) { ?>
            <?php if ($language['code'] == $code) { ?>
              <i class="fa fa-globe icon"></i><span class="hidden-xs hidden-sm">&nbsp;&nbsp;<?php echo $language['name']; ?>&nbsp;</span>&nbsp;<span class="fa fa fa-angle-down caretalt"></span></button>
      <?php } ?>
    <?php } ?>

    <ul class="dropdown-menu">
      <?php foreach ($languages as $language) { ?>
        <?php if ($language['code'] != $code) { ?>

          <?php if ($language['main']) { ?>
            <li><a href="<?php echo $language['url']; ?>" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">&nbsp;&nbsp;<?php echo $language['name']; ?></a></li>
          <?php } else { ?>
            <li><a href="<?php echo $language['url']; ?>" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">&nbsp;&nbsp;<?php echo $language['name']; ?></a></li>
          <?php } ?>
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