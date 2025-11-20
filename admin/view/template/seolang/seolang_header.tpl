<?php if (isset($header)) echo $header; ?><?php if (isset($column_left)) echo $column_left; ?>
<div id="content">

	<div class="seolang-top-heading">
      <div id="ukr-flag" style="margin-left: 11px; float:left; margin-top: 14px;">
         <div style="float: left;">
            <ins>
            
            	<a href="<?php echo $url_seolang; ?>" id="heading-logo">&nbsp;<?php echo $ico_seolang; ?>&nbsp;&nbsp;<?php echo strip_tags($heading_title_seolang); ?>&nbsp;<?php echo $seolang_version; ?></a>
            </ins>
         </div>
         <div id="flag-ua" class="ukraine" style="margin-left: 4px; float: left; height: 8px; width: 14px;"> </div> 
         <?php if ($config_admin_language == 'uk-ua' || $config_admin_language == 'ua-uk' || $config_admin_language == 'uk' || $config_admin_language == 'ua' || $config_admin_language == 'ru-ru' || $config_admin_language == 'ru') { ?> 
           <style>
              #flag-ua, #ukr-flag #flag-ua {
                 display: block !important;
              } 
           </style>   
         <?php } ?>  
      </div>
      <div class="seolang-top-copyright">
         <div style="color: #fff; font-size: 12px; margin-top: 6px; line-height: 18px; margin-left: 9px; margin-right: 9px; overflow: hidden;"><?php echo $heading_dev; ?></div>
      </div>
	</div>