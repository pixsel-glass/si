<?php if (isset($langmark)) { ?>
    <div id="cmswidget-<?php echo $cmswidget; ?>" class="cmswidget">
        <?php echo $langmark; ?>
    </div>

    <?php if (isset($settings_widget['anchor']) && $settings_widget['anchor'] != '') { ?>
        <script>
            $('#cmswidget-<?php echo $cmswidget; ?>').hide();
            <?php if (isset($settings_widget['doc_ready']) && $settings_widget['doc_ready']) { ?>
                $(document).ready(function() {
                <?php } ?>
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
            <?php } ?>
        </script>

    <?php } ?>
<?php } else { ?>
    <?php if (count($languages) > 1) { ?>


        <?php if (isset($settings_widget['autopopup']) && $settings_widget['autopopup']) { ?>

            <div data-toggle="modal" class="hidden" data-target="#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal" id="langmarkmodal_<?php echo $settings_widget['cmswidget']; ?>"></div>

            <style>
                .lm_ua_flag {
                    text-align: center;
                    vertical-align: middle;
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    border-radius: 50%;
                    margin-right: 0px;
                    margin-top: -3px;
                    background-image: linear-gradient(to bottom, #0082D1, #0082D1 50%, #FFD100 50%, #FFD100);
                }

                #lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .lm_flex {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                }

                #lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .lm_flex>div {
                    padding-left: 10px;
                    padding-right: 10px;
                }

                #lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .lm-modal-html {
                    width: 100%;
                    text-align: center;
                }

                <?php if (isset($settings_widget['pointer']) && $settings_widget['pointer']) { ?>#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-dialog::before {
                    content: "";
                    position: absolute;

                    top: -9px;

                    border-bottom: 12px solid #FFF;
                    border-left: 12px solid transparent;
                    border-right: 12px solid transparent;

                    width: 0;
                    height: 0;

                    filter: drop-shadow(0 -4px 3px rgba(0, 0, 0, 0.25));
                    -webkit-filter: drop-shadow(0 -4px 3px rgba(0, 0, 0, 0.25));

                    z-index: 100600;

                }

                <?php } ?>#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-dialog {
                    width: <?php if (isset($settings_widget['window_width']) && $settings_widget['window_width']  != '') { ?><?php echo $settings_widget['window_width']; ?><?php } ?>px;
                    opacity: <?php if (isset($settings_widget['window_opacity']) && $settings_widget['window_opacity']  != '') { ?><?php echo $settings_widget['window_opacity']; ?><?php } ?>;
                }

            </style>

            <div class="modal fade lm_modal" id="lm_<?php echo $settings_widget['cmswidget']; ?>_Modal" style="padding-right: 1px !important;" data-focus="false" data-backdrop="<?php if ($settings_widget['dark_back'] && $settings_widget['dark_back']) { ?>true<?php } else { ?>false<?php } ?>" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" aria-labelledby="lm_<?php echo $settings_widget['cmswidget']; ?>_ModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                        <?php if ($settings_widget['title_status'] && $settings_widget['title_status']) { ?>
                            <div class="modal-header">
                                <button type="button" class="close lm-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">
                                    <?php foreach ($languages as $language) { ?>
                                        <?php if ($language['current']) { ?>
                                            <?php echo $settings_widget['title'][$language['language_id']]; ?>
                                        <?php } ?>
                                    <?php } ?>
                                </h4>
                            </div>
                        <?php } ?>

                        <div class="modal-body">
                            <div class="lm-modal-html">
                                <?php foreach ($languages as $language) { ?>
                                    <?php if ($language['current']) { ?>
                                        <?php echo $settings_widget['html'][$language['language_id']]; ?>
                                    <?php } ?>
                                <?php } ?>
                            </div>

                            <div class="lm_flex">
                                <?php foreach ($languages as $language) { ?>
                                    <?php if (!$settings_widget['current_store_id'] || ($settings_widget['current_store_id'] && $language['store_id'] == $store_id)) { ?>
                                        <?php if ($language['main']) { ?>
                                            <div>
                                                <a onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?><?php if ($language['current']) { ?><?php echo ''; ?><?php } ?>"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a>
                                            </div>
                                        <?php } else { ?>
                                            <div>
                                                <a onclick="lm_setCookie('languageauto', '1', {expires: <?php echo $settings_widget['cookie_auto_days']; ?>}); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?><?php if ($language['current']) { ?><?php echo ''; ?><?php } ?>"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /> <?php } ?><?php echo $language['name']; ?></a>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </div>

                        </div>

                        <?php if ($settings_widget['footer_status'] && $settings_widget['footer_status']) { ?>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary lm-close" data-dismiss="modal" data-bs-dismiss="modal">
                                    <?php foreach ($languages as $language) { ?>
                                        <?php if ($language['current']) { ?>
                                            <?php echo $settings_widget['lm_text_close'][$language['language_id']]; ?>
                                        <?php } ?>
                                    <?php } ?>
                                </button>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>

            <?php foreach ($languages as $language) { ?>
                <?php if ($language['current']) { ?>
                    <?php if (isset($settings_widget['code_custom'][$language['language_id']]) && $settings_widget['code_custom'][$language['language_id']]  != '') { ?>
                        <?php echo $settings_widget['code_custom'][$language['language_id']]; ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <script>
                lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state = false;

                function lm_<?php echo $settings_widget['cmswidget']; ?>_afterload() {
                    if (!lm_<?php echo $settings_widget['cmswidget']; ?>_afterLoad_state) {
                        document.body.removeEventListener('touchstart', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
                        document.body.removeEventListener('touchmove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
                        document.body.removeEventListener('mouseover', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
                        document.removeEventListener('mousemove', lm_<?php echo $settings_widget['cmswidget']; ?>_afterload);
                        if ($('body').hasClass('modal-open')) {} else {

                            <?php if (isset($settings_widget['position']) && $settings_widget['position']  != '') { ?>

                                var lm_<?php echo $settings_widget['cmswidget']; ?>_modal = $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal');
                                var lm_<?php echo $settings_widget['cmswidget']; ?>_modal_dialog = $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-dialog');


                                $('body').append(lm_<?php echo $settings_widget['cmswidget']; ?>_modal);

                                var lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset = $('<?php echo $settings_widget['position']; ?>').offset();

                                if (typeof lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset != "undefined") {

                                    var lm_<?php echo $settings_widget['cmswidget']; ?>_languageHeight = $('<?php echo $settings_widget['position']; ?>').outerHeight();
                                    var lm_<?php echo $settings_widget['cmswidget']; ?>_windowWidth = $(window).width();

                                    const lm_<?php echo $settings_widget['cmswidget']; ?>_style = document.createElement('style');

                                    if ((lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset.left + <?php if (isset($settings_widget['window_width']) && $settings_widget['window_width']  != '') { ?><?php echo $settings_widget['window_width']; ?><?php } ?>) > lm_<?php echo $settings_widget['cmswidget']; ?>_windowWidth) {
                                        lm_<?php echo $settings_widget['cmswidget']; ?>_offset = (lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset.left + <?php if (isset($settings_widget['window_width']) && $settings_widget['window_width']  != '') { ?><?php echo $settings_widget['window_width']; ?><?php } ?>) - lm_<?php echo $settings_widget['cmswidget']; ?>_windowWidth;
                                        lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset.left = lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset.left - lm_<?php echo $settings_widget['cmswidget']; ?>_offset - 18;

                                        lm_<?php echo $settings_widget['cmswidget']; ?>_style.innerHTML = "#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-dialog::before { right: 9px; }";

                                    } else {
                                        lm_<?php echo $settings_widget['cmswidget']; ?>_style.innerHTML = "#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-dialog::before { left: 9px; }";
                                    }
                                    
                                    <?php if (isset($settings_widget['pointer']) && $settings_widget['pointer']) { ?>
                                        topOffsetPointer_<?php echo $settings_widget['cmswidget']; ?> = 9;
                                    <?php } else { ?>
                                        topOffsetPointer_<?php echo $settings_widget['cmswidget']; ?> = 0;
                                    <?php } ?>

                                    document.head.appendChild(lm_<?php echo $settings_widget['cmswidget']; ?>_style);

                                    lm_<?php echo $settings_widget['cmswidget']; ?>_modal_dialog.css({
                                        'margin': '0'
                                    });
                                    
                                }


                                if (typeof lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset != "undefined") {
                                    
                                    let overflowValue_<?php echo $settings_widget['cmswidget']; ?> = getComputedStyle(document.body).overflow;
                                    
                                    document.body.style.overflow = 'visible';
                                    document.body.style.paddingRight = '0px';

                                    setTimeout(function() {
                                        $('body').removeClass('modal-open');
                                    }, 500);
                                    $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').removeClass('fade');
					                $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').removeClass('modal');                                    

                                    $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').css({
                                        //'all': 'unset',
                                        'padding-right': '0px',
                                        'height': 'auto',
                                        'width': '0px',
                                        'position': 'absolute',
                                        'overflow': 'visible',
                                        'z-index': '100500',
                                        'top': lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset.top + lm_<?php echo $settings_widget['cmswidget']; ?>_languageHeight + topOffsetPointer_<?php echo $settings_widget['cmswidget']; ?>,
                                        'left': lm_<?php echo $settings_widget['cmswidget']; ?>_languageOffset.left,
                                        'pointer-events': 'auto'
                                    });
                                    $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-footer button.lm-close, #lm_<?php echo $settings_widget['cmswidget']; ?>_Modal .modal-header button.lm-close').click(function(){
						                lm_setCookie('languageauto', '1', {expires: <?php echo $settings_widget['cookie_auto_days']; ?>});
						                $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').remove();
					                }); 
                                    document.body.style.overflow = overflowValue_<?php echo $settings_widget['cmswidget']; ?>;                                   
                                }
                            <?php } else { ?>
                                $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').modal('show');
                            <?php } ?>

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

                    document.body.addEventListener('mouseover', function() {
                        setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload, 1000)
                    });
                    document.addEventListener('mousemove', function() {
                        setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload, 1000)
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(lm_<?php echo $settings_widget['cmswidget']; ?>_afterload, <?php echo $settings_widget['autoredirect_delay_desktop']; ?>);
                    });
                }

                $('#lm_<?php echo $settings_widget['cmswidget']; ?>_Modal').on('hidden.bs.modal hidden', function() {
                    <?php foreach ($languages as $language) { ?>
                        <?php if ($language['main'] && !$language['current'] && $settings_widget['redirect'] && $settings_widget['redirect']) { ?>

                            window.location = '<?php echo $language['url']; ?>';
                        <?php } ?>
                    <?php } ?>
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