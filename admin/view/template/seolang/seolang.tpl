<?php include('seolang_header.tpl'); ?>


<script>
   function add_switcher(customToggles) {

      customToggles.forEach((customToggle) => {
         const toggleSwitch = customToggle.querySelector('.lm-select-switch');
         const options = toggleSwitch.querySelectorAll('option');

         function updateToggleState(context) {

            for (let i = 0; i < options.length; i++) {
               if (options[i].hasAttribute('selected')) {
                  toggleSwitch.value = options[i].value;
                  if (toggleSwitch.value === '1') {
                     context.classList.add('lm-select-on');
                     context.classList.remove('lm-select-off');
                     context.setAttribute('data-value', options[i].textContent);
                  } else {
                     context.classList.add('lm-select-off');
                     context.classList.remove('lm-select-on');
                     context.setAttribute('data-value', options[i].textContent);
                  }
                  break;
               }
            }
         }

         customToggle.addEventListener('click', function() {
            click_options = this.querySelectorAll('option');

            if (click_options.length > 1) {
               const lm_saves = document.querySelectorAll('.seolang_save');
               lm_saves.forEach((lm_save) => {
                  lm_save.classList.add('lm-must-save');
               })
               toggleSwitch.value = toggleSwitch.value === '1' ? '0' : '1';
               for (let i = 0; i < options.length; i++) {
                  if (options[i].hasAttribute('selected')) {
                     options[i].removeAttribute('selected');
                  } else {
                     options[i].setAttribute('selected', 'selected');
                  }
               }
               this.classList.add('lm-must-save');
               updateToggleState(this);
            }

         });

         updateToggleState(customToggle);
      });
   }
</script>
<div class="page-header">
   <div class="container-fluid">
      <div id="content1" style="border: none;">

         <div class="clearboth-1"></div>

         <?php if ($success) { ?>
            <div class="alert-success success"><i class="fa fa-check-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button>
               <?php echo $success; ?>
            </div>
         <?php } ?>
         <?php if (isset($error_warning) && $error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button>
               <?php echo $error_warning; ?>
            </div>
         <?php } ?>

         <div id="content" style="border: none;">
            <div class="clearboth-1"></div>
            <?php if (isset($session_success)) {
               unset($session_success); ?>
               <div class="success"><?php echo $language_text_seolang_success; ?></div>
            <?php } ?>
            <div class="box1">
               <div class="content">
                  <div class="clearboth-1"></div>
                  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                     <div style="display: flex; flex-wrap: wrap; align-content: space-between; justify-content: space-between; width: 100%; margin-top: 4px; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center;">
                           <div>
                              <?php echo $ico_seolang; ?> <?php echo strip_tags($heading_title_seolang); ?>&nbsp;<?php echo $text_seolang_widgets; ?>&nbsp;<?php echo $seolang_version; ?>&nbsp;&nbsp;<?php if (!isset($seolang_settings['status']) || !$seolang_settings['status']) { ?><span style="color: red;"><?php } else { ?><span style="color: green;"><?php } ?><?php echo $entry_seolang_widget_status; ?><?php if (!isset($seolang_settings['status']) || !$seolang_settings['status']) { ?></span><?php } ?>&nbsp;&nbsp;
                           </div>
                           <div class="input-group lm-select-toggle">
                              <select class="form-control lm-select-switch" name="seolang_settings[status]">
                                 <?php if (isset($seolang_settings['status']) && $seolang_settings['status']) { ?>
                                    <option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
                                    <option value="0"><?php echo $text_seolang_disabled; ?></option>
                                 <?php } else { ?>
                                    <option value="1"><?php echo $text_seolang_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </div>
                        <div style="text-align: right;">
                           <div id="sticky-anchor"></div>
                           <div id="sticky" style="margin:5px; float:right;">
                              <a href="#" id="lm_save" class="mbutton seolang_save"><i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
                           </div>
                        </div>
                     </div>
                     <?php if (isset($stores) && is_array($stores) && !empty($stores)) { ?>
                        <div style="display: flex; align-items: center; margin-bottom: 8px">
                           <div>
                              <?php echo $language->get('entry_seolang_store'); ?>&nbsp;&nbsp;
                           </div>
                           <div class="input-group">
                              <select class="form-control sc_select_other" id="seolang_settings_store_id" name="seolang_settings_<?php echo $store_id ?>[store_id]">
                                 <?php foreach ($stores as $store) { ?>
                                    <option value="<?php echo $store['store_id']; ?>" <?php if (isset($store_id) && $store_id == $store['store_id']) { ?> selected="selected" <?php } ?>><?php echo $store['url']; ?> - <?php echo $store['name']; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                        </div>
                     <?php } ?>

                     <div id="tabs" class="htabs">
                        <a href="#tab-menu" class="lm-tab-1"><?php echo $tab_text_seolang_menu; ?></a>
                        <a href="#tab-main" class="lm-tab-1"><?php echo $tab_text_seolang_main; ?></a>
                        <a href="#tab-options" class="lm-tab-1"><?php echo $tab_text_seolang_options; ?></a>
                        <a href="#tab-install" class="lm-tab-1" id="tab-install-header"> <?php echo $entry_seolang_install_update; ?></a>
                        <a href="#tab-service" class="lm-tab-1"><?php echo $tab_text_seolang_service; ?></a>
                        <a href="#tab-doc" class="lm-tab-1"><?php echo $tab_text_seolang_doc; ?></a>
                     </div>

                     <div id="tab-menu">
                        <?php echo $html_tab_menu ?>
                     </div>

                     <div id="tab-main">
                        <?php echo $html_tab_main ?>
                     </div>

                     <div id="tab-options">
                        <?php echo $html_tab_options ?>
                        <script>
                           $('#tabs-options-widgets a').tabs();
                        </script>
                     </div>

                     <div id="tab-install">
                        <?php echo $html_tab_install ?>
                     </div>

                     <div id="tab-service">
                        <?php echo $html_tab_service ?>
                     </div>

                     <div id="tab-doc">
                        <?php echo $html_tab_doc ?>
                     </div>

                  </form>
               </div>
               <style>
                  .flex-box {
                     display: flex;
                     align-items: center;
                     align-content: stretch;
                     justify-content: space-between;
                  }

                  .flex-box>div {
                     width: 33.3%;
                  }
               </style>

               <script>
                  form_submit = function() {
                     $('#form').submit();
                     return false;
                  }
                  $('.seolang_save').bind('click', form_submit);
               </script>
               <script>
                  $('#tabs a').tabs();

                  $('#tabs-multi > a').tabs();
               </script>
               <script>
                  function odd_even() {
                     var kz = 0;
                     $('table tr').each(function(i, elem) {
                        $(this).removeClass('odd');
                        $(this).removeClass('even');
                        if ($(this).is(':visible')) {
                           kz++;
                           if (kz % 2 == 0) {
                              $(this).addClass('odd');
                           }
                        }
                     });
                  }

                  $(document).ready(function() {
                     odd_even();

                     $('.htabs a').click(function() {
                        odd_even();
                     });

                     $('.vtabs a').click(function() {
                        odd_even();
                     });

                  });

                  function input_select_change() {

                     $('input').each(function() {
                        if (!$(this).hasClass('no_change')) {
                           $(this).removeClass('sc_select_enable');
                           $(this).removeClass('sc_select_disable');

                           if ($(this).val() != '') {
                              $(this).addClass('sc_select_enable');
                           } else {
                              $(this).addClass('sc_select_disable');
                           }
                        }
                     });

                     $('select').each(function() {
                        if (!$(this).hasClass('no_change')) {
                           $(this).removeClass('sc_select_enable');
                           $(this).removeClass('sc_select_disable');

                           this_val = $(this).find('option:selected').val()

                           if (this_val == '1') {
                              $(this).addClass('sc_select_enable');
                           }

                           if (this_val == '0' || this_val == '') {
                              $(this).addClass('sc_select_disable');
                           }

                           if (this_val != '0' && this_val != '1' && this_val != '') {
                              $(this).addClass('sc_select_other');
                           }
                        }
                     });
                  }

                  $('.table-help-href').on('click', function() {
                     $('.help').toggle();
                  });

                  $(document).ready(function() {
                     $('.help').hide();

                     input_select_change();

                     $("select")
                        .change(function() {
                           input_select_change();

                        });

                     $("input")
                        .blur(function() {
                           input_select_change();
                        });

                  });
               </script>
               <script>
                  function delayer() {
                     window.location = 'index.php?route=seolang/seolang&<?php echo $token_name; ?>=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&seolang_save=1';
                  }
               </script>

               <style>
                  .sticky-back {
                     background-color: #E1E1E1;
                     box-shadow: 0 0 16px rgba(0, 0, 0, 0.3) !important;
                  }

                  #sticky.stick {
                     position: fixed;
                     top: 0;
                     z-index: 10000;
                  }
               </style>
               <script>
                  function sticky_relocate() {
                     var window_top = $(window).scrollTop();
                     var div_top = $('#sticky-anchor').offset().top;

                     if (window_top > div_top) {
                        $('#sticky').addClass('stick');
                        $('#sticky').addClass('sticky-back');
                        $('#sticky').css({
                           "right": "0px"
                        });

                     } else {
                        $('#sticky').removeClass('stick');
                        $('#sticky').removeClass('sticky-back');
                        $('#sticky').css({
                           "margin-left": "0px"
                        });
                     }
                  }

                  $(function() {
                     div_left = $('#sticky').offset().left - 60;
                     $(window).scroll(sticky_relocate);
                     sticky_relocate();
                  });
               </script>
               <script>
                  $('#seolang_settings_store_id').change(function() {
                     var store_id = $(this).val();
                     window.location = 'index.php?route=seolang/seolang&<?php echo $token_name; ?>=<?php echo $token; ?>&store_id=' + store_id;
                  });
               </script>

               <?php if (isset($widgets_ocmod_swith) && $widgets_ocmod_swith) { ?>
                  <script>
                     $(document).ready(function() {
                        $('#seolang_ocmod_refresh').click();
                     });
                  </script>
               <?php } ?>

               <?php if (isset($seolang_save) && $seolang_save) { ?>
                  <script>
                     $(document).ready(function() {
                        $('.seolang_save').click();
                     });
                  </script>
               <?php } ?>



               <?php if (isset($text_seolang_update) && $text_seolang_update != '') { ?>
                  <script>
                     $(document).ready(function() {
                        $('#tab-install-header').click();
                     });
                  </script>
               <?php } ?>
               <script>
                  $('.close').on('click', function() {
                     $('.alert, .success').hide();
                  });
               </script>


               <div class="input-group" style="float: right;">
                  <a href="#pro_show" class="seolang_pro_show markbuttono sc_button"><?php echo $language->get('entry_seolang_show_pro_settings'); ?></a>
                  <a href="#pro_hide" class="seolang_pro_hide markbuttono sc_button"><?php echo $language->get('entry_seolang_hide_pro_settings'); ?></a>
               </div>


               <script>
                  $('.seolang_pro_show').click(function() {
                     sc = $(this).offset().top;
                     th = this;

                     $('.seolang-pro-settings').show();
                     $('.seolang_pro_show').hide();
                     $('.seolang_pro_hide').show();
                     /*
	setTimeout(function(el, e) {
		$('html, body').animate({
		    scrollTop: el + $(th).offset().top
		}, 500);
	}, 100, sc, th);
    */
                     return false;
                  });

                  $('.seolang_pro_hide').click(function() {
                     $('.seolang-pro-settings').hide();
                     $('.seolang_pro_hide').hide();
                     $('.seolang_pro_show').show();
                     localStorage.removeItem('lm_pro_click');
                     return false;
                  });

                  string_lm_pro_click = localStorage.getItem('lm_pro_click');

                  if (string_lm_pro_click == null) {
                     var array_lm_pro_click = [];
                     $('.seolang_pro_hide').click();
                  } else {
                     var array_lm_pro_click = JSON.parse(string_lm_pro_click);

                     array_lm_pro_click.forEach(function(item, index, array) {
                        $('a[href="' + item + '"]').click();
                        console.log(item);
                     });
                  }

                  $('.seolang_pro_show').on('click', function() {
                     lm_pro_href = $(this).attr('href');
                     array_lm_pro_click.push(lm_pro_href);
                     if (array_lm_pro_click.length > 1) {
                        array_lm_pro_click.shift();
                     }
                     localStorage.setItem('lm_pro_click', JSON.stringify(array_lm_pro_click));
                  });

                  $('.seolang_pro_hide').on('click', function() {
                     lm_pro_href = $(this).attr('href');
                     array_lm_pro_click.push(lm_pro_href);
                     if (array_lm_pro_click.length > 1) {
                        array_lm_pro_click.shift();
                     }
                     localStorage.setItem('lm_pro_click', JSON.stringify(array_lm_pro_click));
                  });
               </script>

               <?php if (isset($seolang_ocmod_none) && $seolang_ocmod_none) { ?>
                  <script>
                     $(document).ready(function() {
                        $('a[href=\'#tab-install\']').trigger('click');
                     });
                  </script>
               <?php } ?>


            </div>
         </div>
      </div>
   </div>
</div>
</div>
<script>
   $('.alert').on('click', function() {
      $(this).hide(1000)
   });
   setTimeout(function() {
      $('.alert').hide(1000);
   }, 2500, '.alert');
   $('.success').on('click', function() {
      $(this).hide(1000)
   });
   setTimeout(function() {
      $('.success').hide(1000);
   }, 3500, '.success');
</script>

<script>
   function tab_history(line) {

      string_lm_tabs_click = localStorage.getItem('sl_tabs_click_' + line);

      if (string_lm_tabs_click == null) {
         var array_lm_tabs_click = [];
      } else {
         var array_lm_tabs_click = JSON.parse(string_lm_tabs_click);

         array_lm_tabs_click.forEach(function(item, index, array) {
            $('a[href="' + item + '"]').click();
            console.log(item);
         });
      }

      $('.lm-tab-' + line).on('click', function() {
         lm_tab_href = $(this).attr('href');
         array_lm_tabs_click.push(lm_tab_href);
         if (array_lm_tabs_click.length > 3) {
            array_lm_tabs_click.shift();
         }
         localStorage.setItem('sl_tabs_click_' + line, JSON.stringify(array_lm_tabs_click));
      });
   }

   tab_history(1);
   tab_history(2);
</script>


<script>
   const customToggles = document.querySelectorAll('.lm-select-toggle');
   add_switcher(customToggles);
</script>
<?php if (isset($footer)) echo $footer; ?>