<?php include(DIR_TEMPLATE . 'seolang/seolang_header.tpl'); ?>
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
                  <style>
                        .flexline {
                              display: flex;
                              flex-wrap: wrap;
                              align-content: start;
                              justify-content: start;
                              width: 100%;
                              text-rendering: optimizeLegibility;

                        }

                        .flexline>div {
                              border: solid 1px #FFF;
                              padding-right: 16px;
                              display: flex;
                              vertical-align: center;
                              height: 34px;
                        }

                        .z-depth-1 {
                              -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12) !important;
                              box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12) !important;
                        }


                        .redbar {
                              text-align: center;
                              border-image: none;
                              border-radius: 6px !important;
                              display: block !important;
                              width: 100% !important;
                              margin-bottom: -4px;
                              min-height: 36px;
                              font-size: 11px;
                              line-height: 14px;

                              float: none;
                        }


                        .flexstyle {
                              display: flex;
                              flex-wrap: wrap;
                              align-content: space-between;
                              justify-content: space-between;
                              width: 100%;
                              text-rendering: optimizeLegibility;
                              flex-grow: 1;
                        }
                  </style>


                  <div class="flexstyle">
                        <div>
                              <div class="flexline">


                                    <div>
                                          <div>
                                                <a href="<?php echo $url_seolang_seolang_options; ?>" class="markbuttono sc_button"><i class="fa fa-cog fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_langmark_widget'); ?></a>
                                          </div>
                                    </div>

                                    <div>
                                          <div>
                                                <a href="<?php echo $url_seolang_langmark_options; ?>" class="markbuttono sc_button"><i class="fa fa-cog fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_seolang_langmark_options'); ?></a>
                                          </div>
                                    </div>

                                    <div>
                                          <div>
                                                <a href="<?php echo $url_seolang_seolang_adapter; ?>" class="markbuttono sc_button"><i class="fa fa-bars" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_seolang_adapter_themes'); ?></a>
                                          </div>
                                    </div>

                              </div>

                        </div>
                        <div style="float:right;">
                        <span style="color: green"><?php echo $current_name; ?> - <?php echo $current_directory; ?> </span>
                              <a onclick="$('#form').submit();" class="markbuttono asc_blinking nohref" style="background-color: orange;"><i class="fa fa-refresh" aria-hidden="true"></i> &nbsp;<?php echo $language->get('button_adapter'); ?></a>
                              

                        </div>

                  </div>






                  <form action="<?php echo $url_action_form; ?>" method="post" enctype="multipart/form-data" id="form">
                        <div class="input-group" style="width: 100%; margin-top:8px;">
                              <span class="input-group-addon redbar"><span id="span_file_in"><?php echo $form_file; ?></span> &nbsp; -> &nbsp; <br><?php echo $form_folder; ?><span id="span_file_theme"><?php echo $file_theme; ?></span></span>
                              <input type="hidden" name="file_theme" id="input_file_theme" value="<?php echo $file_theme; ?>">
                              <input type="hidden" name="folder_theme" id="input_folder_theme" value="<?php echo $folder_theme; ?>">
                              <textarea class="form-control z-depth-1" id="form_content" name="form_content" rows="25" style="width: 100%;"><?php echo $form_content; ?></textarea>

                        </div>

                  </form>


                  <div class="flexstyle" style="margin-top: 10px;">
                        <div>
                              <div class="flex200">
                                    <div>
                                          <div>
                                                <?php echo $language->get('entry_adapter_load_ajax_themes'); ?>&nbsp;<br><span class="help"><?php if (!isset($folder_themes_path_full)) $folder_themes = $folder_themes_default_path_full;
                                                                                                                                                else $folder_themes = $folder_themes_path_full;
                                                                                                                                                echo str_ireplace($dir_main, '', '/' . $folder_themes); ?></span><span id="span_themes_file" class="help"></span>




                                                <div class="input-group">
                                                      <input type="text" class="form-control" id="input_themes_file" name="themes_file" value="">
                                                </div>



                                                <script type="text/javascript">
                                                      $('input[name=\'themes_file\']').autocomplete({
                                                            minLength: 0,
                                                            autoFocus: true,
                                                            delay: 0,
                                                            search: '',
                                                            'source': function(request, response) {

                                                                  let postdata = new FormData($('#form')[0]);
                                                                  postdata.append('themes_file', $('#input_themes_file').val());

                                                                  $.ajax({
                                                                        url: '<?php echo $url_ajax_themes_autotemplate; ?>&filter_name=' + $('input[name=\'themes_file\']').val(),
                                                                        type: 'POST',
                                                                        data: postdata,
                                                                        cache: false,
                                                                        contentType: false,
                                                                        processData: false,
                                                                        error: function() {
                                                                              console.log('Error request');
                                                                        },
                                                                        success: function(json) {

                                                                              response($.map(json, function(item) {
                                                                                    return {
                                                                                          label: item['label'],
                                                                                          value: item['name']
                                                                                    }
                                                                              }));
                                                                        }
                                                                  });
                                                            },
                                                            'select': function(item) {
                                                                  $('#input_themes_file').val(item['value']);
                                                                  $('#span_themes_file').text(item['value']);
                                                                  $('#input_file_theme').text(item['value']);
                                                                  $('#span_file_theme').text(item['value']);
                                                                  input_select_change();
                                                            }
                                                      });
                                                      <?php if (SC_VERSION < 20) { ?>
                                                            $('body').on('click', 'input[name=\'themes_file\']', function() {
                                                                  $(this).trigger("keydown", {
                                                                        which: 80
                                                                  });
                                                            });
                                                      <?php } ?>
                                                </script>



                                          </div>
                                          <div class="input-group">
                                                <a id="load-ajax-themes" class="markbuttono sc_button"><i class="fa fa-download fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_adapter_download'); ?></a>
                                                <div id="load-ajax-themes-message"></div>

                                          </div>
                                    </div>

                                    <div>
                                          <div>
                                                <?php echo $language->get('entry_adapter_load_ajax_theme'); ?>&nbsp;<br><span class="help"><?php if (!isset($folder_theme_path_full)) $folder_theme = $folder_theme_default_path_full;
                                                                                                                                                else $folder_theme = $folder_theme_path_full;
                                                                                                                                                echo str_ireplace($dir_main, '', '/' . $folder_theme); ?></span><span id="span_theme_file" class="help"></span>


                                                <div class="input-group">
                                                      <input type="text" class="form-control" id="input_theme_file" name="theme_file" value="">
                                                </div>
                                                <script type="text/javascript">
                                                      $('input[name=\'theme_file\']').autocomplete({
                                                            minLength: 0,
                                                            autoFocus: true,
                                                            delay: 0,
                                                            search: '',
                                                            'source': function(request, response) {

                                                                  let postdata = new FormData($('#form')[0]);
                                                                  postdata.append('theme_file', $('#input_theme_file').val());

                                                                  $.ajax({
                                                                        url: '<?php echo $url_ajax_theme_autotemplate; ?>&filter_name=' + $('input[name=\'theme_file\']').val(),
                                                                        type: 'POST',
                                                                        data: postdata,
                                                                        cache: false,
                                                                        contentType: false,
                                                                        processData: false,
                                                                        error: function() {
                                                                              console.log('Error request');
                                                                        },
                                                                        success: function(json) {

                                                                              response($.map(json, function(item) {
                                                                                    return {
                                                                                          label: item['label'],
                                                                                          value: item['name']
                                                                                    }
                                                                              }));

                                                                        }
                                                                  });
                                                            },
                                                            'select': function(item) {
                                                                  $('#input_theme_file').val(item['value']);
                                                                  $('#span_theme_file').text(item['value']);
                                                                  $('#input_file_theme').text(item['value']);
                                                                  $('#span_file_theme').text(item['value']);
                                                                  input_select_change();
                                                            }
                                                      });
                                                      <?php if (SC_VERSION < 20) { ?>
                                                            $('body').on('click', 'input[name=\'theme_file\']', function() {
                                                                  $(this).trigger("keydown", {
                                                                        which: 80
                                                                  });
                                                            });
                                                      <?php } ?>
                                                </script>
                                          </div>
                                          <div class="input-group">
                                                <a id="load-ajax-theme" class="markbuttono sc_button"><i class="fa fa-download fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_adapter_download'); ?></a>
                                                <div id="load-ajax-theme-message"></div>

                                          </div>
                                    </div>

                                    <div>
                                          <div>
                                                <?php echo $language->get('entry_adapter_load_ajax_language'); ?>&nbsp;<br><span class="help"><?php echo str_ireplace($dir_main, '', '/' . $file_language_path_full); ?></span>
                                          </div>
                                          <div class="input-group">
                                                <a id="load-ajax-language" class="markbuttono sc_button"><i class="fa fa-download fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_adapter_download'); ?></a>
                                                <div id="load-ajax-language-message"></div>

                                          </div>
                                    </div>


                                    <script>
                                          function hide_messages(sel) {
                                                $(sel).html('&nbsp;');
                                                $(sel).hide();

                                          }
                                          $('#load-ajax-language').on('click', function() {



                                                $.ajax({
                                                      url: '<?php echo $url_ajax_language; ?>',
                                                      type: 'POST',
                                                      data: new FormData($('#form')[0]),
                                                      cache: false,
                                                      contentType: false,
                                                      processData: false,
                                                      beforeSend: function() {

                                                            $('#span_file_in').html('');
                                                            $('#load-ajax-language-message').html('<?php echo $language->get('text_seolang_loading_main_without'); ?>').show();
                                                      },
                                                      success: function(content) {
                                                            if (content) {
                                                                  content_array = JSON.parse(content);
                                                                  if (content_array['success']) {
                                                                        $('#form_content').text(content_array['text']);
                                                                        $('#span_file_in').html(content_array['file']);
                                                                        $('#span_file_theme').html(content_array['filename']);
                                                                        $('#input_file_theme').val(content_array['filename']);
                                                                        $('#load-ajax-language-message').html('<span style=\'color:green\'><?php echo $language->get('text_load_ajax_language_message_succes'); ?><\/span>').show();
                                                                  } else {
                                                                        $('#load-ajax-language-message').html('<span style=\'color:red\'>' + content_array['error'] + '<\/span>').show();
                                                                  }
                                                                  setTimeout(hide_messages, 3000, '#load-ajax-language-message');
                                                            }
                                                      },
                                                      error: function(xhr, ajaxOptions, thrownError) {
                                                            $('#load-ajax-language-message').html('<span style="color:red"><?php echo $language->get('text_adapter_error_download'); ?></span><br>' + thrownError + '<br>' + xhr.statusText + '<br>' + xhr.responseText);
                                                            setTimeout(hide_messages, 3000, '#load-ajax-language-message');
                                                      }
                                                });
                                          });

                                          $('#load-ajax-theme').on('click', function() {
                                                let postdata = new FormData($('#form')[0]);
                                                postdata.append('theme_file', $('#input_theme_file').val());
                                                $.ajax({
                                                      url: '<?php echo $url_ajax_theme; ?>',
                                                      type: 'POST',
                                                      data: postdata,
                                                      cache: false,
                                                      contentType: false,
                                                      processData: false,
                                                      beforeSend: function() {

                                                            $('#span_file_in').html('');
                                                            $('#load-ajax-theme-message').html('<?php echo $language->get('text_seolang_loading_main_without'); ?>').show();
                                                      },
                                                      success: function(content) {
                                                            if (content) {
                                                                  content_array = JSON.parse(content);
                                                                  if (content_array['success']) {
                                                                        $('#form_content').text(content_array['text']);
                                                                        $('#span_file_in').html(content_array['file']);
                                                                        $('#span_file_theme').html(content_array['filename']);
                                                                        $('#input_file_theme').val(content_array['filename']);
                                                                        $('#load-ajax-theme-message').html('<span style=\'color:green\'><?php echo $language->get('text_load_ajax_theme_message_succes'); ?><\/span>').show();



                                                                  } else {
                                                                        $('#load-ajax-theme-message').html('<span style=\'color:red\'>' + content_array['error'] + '<\/span>').show();
                                                                  }
                                                                  setTimeout(hide_messages, 3000, '#load-ajax-theme-message');
                                                            }
                                                      },
                                                      error: function(xhr, ajaxOptions, thrownError) {
                                                            $('#load-ajax-theme-message').html('<span style="color:red"><?php echo $language->get('text_adapter_error_download'); ?></span><br>' + thrownError + '<br>' + xhr.statusText + '<br>' + xhr.responseText);
                                                            setTimeout(hide_messages, 3000, '#load-ajax-theme-message');
                                                      }
                                                });
                                          });

                                          $('#load-ajax-themes').on('click', function() {
                                                let postdata = new FormData($('#form')[0]);
                                                postdata.append('themes_file', $('#input_themes_file').val());
                                                $.ajax({
                                                      url: '<?php echo $url_ajax_themes; ?>',
                                                      type: 'POST',
                                                      data: postdata,
                                                      cache: false,
                                                      contentType: false,
                                                      processData: false,
                                                      beforeSend: function() {

                                                            $('#span_file_in').html('');
                                                            $('#load-ajax-themes-message').html('<?php echo $language->get('text_seolang_loading_main_without'); ?>').show();
                                                      },
                                                      success: function(content) {
                                                            if (content) {
                                                                  content_array = JSON.parse(content);
                                                                  if (content_array['success']) {
                                                                        $('#form_content').text(content_array['text']);
                                                                        $('#span_file_in').html(content_array['file']);
                                                                        $('#span_file_theme').html(content_array['filename']);
                                                                        $('#input_file_theme').val(content_array['filename']);
                                                                        $('#load-ajax-themes-message').html('<span style=\'color:green\'><?php echo $language->get('text_load_ajax_themes_message_succes'); ?><\/span>').show();



                                                                  } else {
                                                                        $('#load-ajax-themes-message').html('<span style=\'color:red\'>' + content_array['error'] + '<\/span>').show();
                                                                  }
                                                                  setTimeout(hide_messages, 3000, '#load-ajax-themes-message');
                                                            }
                                                      },
                                                      error: function(xhr, ajaxOptions, thrownError) {
                                                            $('#load-ajax-themes-message').html('<span style="color:red"><?php echo $language->get('text_adapter_error_download'); ?></span><br>' + thrownError + '<br>' + xhr.statusText + '<br>' + xhr.responseText);
                                                            setTimeout(hide_messages, 3000, '#load-ajax-themes-message');
                                                      }
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
                                          $(document).ready(function() {


                                                input_select_change();

                                                $("select, input")
                                                      .change(function() {
                                                            input_select_change();

                                                      });

                                                $("input")
                                                      .blur(function() {
                                                            input_select_change();
                                                      });

                                          });
                                    </script>
                              </div>
                        </div>

                        <div>

                        </div>


                  </div>







            </div>
      </div>
</div>
<?php if (SC_VERSION > 15) { ?>
      </div>
<?php } ?>

<?php if (SC_VERSION < 20) { ?>
      <style>
            #footer {
                  margin-top: 0px;
            }
      </style>
<?php } ?>

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


<?php if (isset($footer)) echo $footer; ?>