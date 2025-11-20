<?php if (count($languages) > 1) { ?>
<?php 
	$tmp = array();
	foreach( $languages as $language ){
		if( $language['current']){
			$tmp = $language;
			break;
		}
	}	
?>
<div class="language-wrapper pull-right">	
	<div class="btn-group">
		<button type="button" class="form-control" data-toggle="dropdown">
			<?php if( !empty($tmp) ) { ?>
			<span>
				<?php echo $language['name']; ?>				
			</span>		
			<?php } ?>
			<span class="fa fa-sort-asc"></span>
		</button>
		<div class="dropdown-menu dropdown">
<div>
				<div class="box-language">
    <?php foreach ($languages as $language) { ?>
		<?php if ($language['main']) { ?>
						<a class="list-item" onclick="lm_deleteCookie('languageauto'); window.location = '<?php echo $language['url']; ?>'">
						<?php } else { ?>
						<a class="list-item" onclick="lm_setCookie('languageauto', '1', {expires: 180}); window.location = '<?php echo $language['url']; ?>'">
						<?php } ?> 


							<span class="item-name"><?php echo $language['name']; ?></span>							
						</a>				
    <?php } ?>

  </div>
	</div>
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
  lm_setCookie(name, "", {'max-age': -1});
}
</script>  
<?php } ?>
