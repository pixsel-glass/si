<?php if (isset($langmark)) { ?>
  <div id="cmswidget-<?php echo $cmswidget; ?>" class="cmswidget">
    <?php echo ($langmark); ?>
  </div>

  <?php if (isset($settings_widget['anchor']) && $settings_widget['anchor'] != '') { ?>
    <script>
      $('#cmswidget-<?php echo $cmswidget; ?>').hide();
      <?php if (isset($settings_widget['doc_ready']) && $settings_widget['doc_ready']) { ?>
        $(document).ready(function() {
        <?php  } ?>
        var prefix = '<?php echo $prefix; ?>';
        var cmswidget = '<?php echo $cmswidget; ?>';
        var heading_title = '<?php echo $heading_title; ?>';
        var data = $('#cmswidget-<?php echo $cmswidget; ?>').html();
        <?php echo $settings_widget['anchor']; ?>;
        $('#cmswidget-<?php echo $cmswidget; ?>').show();
        delete data;
        delete prefix;
        delete cmswidget;
        <?php if (isset($settings_widget['doc_ready']) && $settings_widget['doc_ready']) { ?>
        });
      <?php  } ?>
    </script>

  <?php  } ?>
<?php  } else { ?>
  <?php if (count($languages) > 1) { ?>
    <?php if (isset($settings_widget['autopopup']) && $settings_widget['autopopup']) { ?>

      <div data-toggle="modal" class="hidden" data-target="#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal" id="langmarkmodal_<?php echo $settings_widget['cmswidget']; ?>"></div>
      <style>
        .lm_<?php echo $settings_widget['cmswidget']; ?>_flex {
          display: flex;
          flex-wrap: wrap;
          justify-content: center;

        }

        .lm_<?php echo $settings_widget['cmswidget']; ?>_flex>div {

          padding-left: 10px;
          padding-right: 10px;
        }

        .lm-modal-html {
          width: 100%;
          text-align: center;
        }
      </style>

      <div class="modal fade" id="lm_<?php echo $settings_widget['cmswidget']; ?>_Modal" tabindex="-1" role="dialog" aria-labelledby="lm_<?php echo $settings_widget['cmswidget']; ?>_ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">

            <?php if (isset($settings_widget['title_status']) && $settings_widget['title_status']) { ?>
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php

                                        foreach ($languages as $language) {
                                          if ($language['current']) {
                                            echo $settings_widget['title'][$language['language_id']];
                                          }
                                        }
                                        ?></h4>
              </div>
            <?php } ?>
            <div class="modal-body">
              <div class="lm-modal-html">
                <?php
                foreach ($languages as $language) {
                  if ($language['current']) {
                    echo $settings_widget['html'][$language['language_id']];
                  }
                }
                ?>
              </div>
              <div class="lm_<?php echo $settings_widget['cmswidget']; ?>_flex">
                <?php foreach ($languages as $language) { ?>
                  <div>
                    <?php if ($language['main']) { ?>
                      <a onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?><?php if ($language['current']) {
                                                                                                                                                                echo '';
                                                                                                                                                              } ?>"><?php if (SC_VERSION < 22 && isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><?php } else {
                                                                                                                                                                                                                                                                                                                                                                                                                              if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          } ?> <?php echo $language['name']; ?></a>
                    <?php } else { ?>
                      <a onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?><?php if ($language['current']) {
                                                                                                                                                                                  echo '';
                                                                                                                                                                                } ?>"><?php if (SC_VERSION < 22 && isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><?php } else {
                                                                                                                                                                                                                                                                                                                                                                                                                                                if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } ?> <?php echo $language['name']; ?></a>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>

            </div>
            <?php if (isset($settings_widget['footer_status']) && $settings_widget['footer_status']) { ?>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php

                                                                                      foreach ($languages as $language) {
                                                                                        if ($language['current']) {
                                                                                          echo $settings_widget['lm_text_close'][$language['language_id']];
                                                                                        }
                                                                                      }
                                                                                      ?></button>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

<?php
foreach ($languages as $language) {
  if ($language['current']) {
    if (isset($settings_widget['code_custom'][$language['language_id']]) && $settings_widget['code_custom'][$language['language_id']] != '') {
      echo $settings_widget['code_custom'][$language['language_id']];
    }
  }
}
?>


      <script>
        lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state = false;

        function lm_<?php echo $settings_widget['cmswidget']; ?>_afterload() {
          if (!lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state) {
            document.body.removeEventListener('touchstart', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
            document.body.removeEventListener('touchmove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
            document.body.removeEventListener('mouseover', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
            document.removeEventListener('mousemove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);

            if ($('body').hasClass('modal-open')) {} else {
              $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').modal('show');
              //$('#langmarkmodal_<?php echo $settings_widget['cmswidget']; ?>').click(); // toggle
              lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state = true;
            }
          }
        }
        var lm_<?php echo $settings_widget['cmswidget']; ?>_userAgent = navigator.userAgent || navigator.vendor || window.opera;

        if (/Android|iPhone|iPad|iPod|Windows Phone|webOS|BlackBerry/i.test(lm_<?php echo $settings_widget['cmswidget']; ?>_userAgent)) {
          document.body.addEventListener('touchstart', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
          document.body.addEventListener('touchmove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
          document.addEventListener('DOMContentLoaded', function() {
            setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload, <?php echo $settings_widget['autoredirect_delay_mobile']; ?>)
          });
        } else {
          document.body.addEventListener('mouseover', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
          document.addEventListener('mousemove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
          document.addEventListener('DOMContentLoaded', function() {
            setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload, <?php echo $settings_widget['autoredirect_delay_desktop']; ?>);
          });
        }

        $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').on('hidden.bs.modal', function() {
          <?php foreach ($languages as $language) {
            if (isset($language['main']) && $language['main'] && !$language['current'] && isset($settings_widget['redirect']) && $settings_widget['redirect']) {
          ?>
              window.location = '<?php echo $language['url']; ?>';
          <?php
            }
          } ?>
        })

        $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').on('hidden', function() {
          <?php foreach ($languages as $language) {
            if (isset($language['main']) && $language['main'] && !$language['current'] && isset($settings_widget['redirect']) && $settings_widget['redirect']) {
          ?>
              window.location = '<?php echo $language['url']; ?>';
          <?php
            }
          } ?>
        })
      </script>
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
  <?php } ?>
<?php } ?>