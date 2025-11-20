<?php if (count($languages) > 1) { ?>
   <div class="header__languages <?php echo count($languages) < 2 ? 'header__languages--hide' : '' ?>">
      <div class="header__select">
        <!-- <span class="account__title"><?php echo $text_language; ?></span> -->
         <span id="form-language">
            <span class="select select--header select--squer select--transparent">
               <select data-placeholder="" class="select select--header select--transparent" onchange="window.location = $(this).val();">
                  <option> </option>
                  <?php foreach ($languages as $language) { ?> <?php if ($language['current']) { ?>
                        <option value="<?php echo $language['url']; ?>" selected="selected"><?php echo $language['name']; ?></option>
                     <?php } else { ?>
                        <option value="<?php echo $language['url']; ?>"><?php echo $language['name']; ?></option>
                     <?php } ?> <?php } ?>
               </select>
            </span>

         </span>
      </div>
   </div>
<?php } ?>