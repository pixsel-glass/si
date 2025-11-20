<?php if (count($languages) > 1) { ?>
<?php if (SC_VERSION < 20) { ?>
<div id="language">
  <?php echo $text_language; ?>
    <?php foreach ($languages as $language) { ?>
    <?php if ($language['current']) { ?>
      <?php if ($language['main']) { ?>
      <a onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?>"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php } ?> <?php echo $language['name']; ?></a>
      <?php } else { ?>
        <a onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?>"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php } ?> <?php echo $language['name']; ?></a>
      <?php } ?> 
    <?php } else { ?>
    
      <?php if ($language['main']) { ?>
      <a onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?>"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php } ?> <?php echo $language['name']; ?></a>
      <?php } else { ?>
      <a onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?>"><?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php } ?> <?php echo $language['name']; ?></a>
      <?php } ?>   

    <?php } ?>
    <?php } ?>
</div>
<?php } else { ?>
<div class="pull-left">
  <div class="btn-group">
    <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
    <?php foreach ($languages as $language) { ?>
    <?php if ($language['current']) { ?>
    <?php if (SC_VERSION < 22 && isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
    <img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
    <?php } else { ?>
    <?php if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?>
    <img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>">
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_language; ?></span> <i class="fa fa-caret-down"></i></button>
    <ul class="dropdown-menu">
      <?php foreach ($languages as $language) { ?>
      <?php if ($language['main']) { ?>
      <li><a onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?><?php if ($language['current']) { echo '#'; }?>"><?php if (SC_VERSION < 22 && isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><?php } else { if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php } } ?> <?php echo $language['name']; ?></a></li>
      <?php } else { ?>
      <li><a onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'" href="<?php echo $language['url']; ?><?php if ($language['current']) { echo '#'; }?>"><?php if (SC_VERSION < 22 && isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>" /><?php } else { if (isset($settings_widget['image_status']) && $settings_widget['image_status']) { ?><img src="catalog/language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" alt="<?php echo $language['name']; ?>" title="<?php echo $language['name']; ?>"><?php } } ?> <?php echo $language['name']; ?></a></li>
      <?php } ?> 
      <?php } ?>
    </ul>
  </div>
</div>
<?php } ?>

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
  lm_setCookie(name, "", {'max-age': -1});
}
</script>  
<?php } ?>