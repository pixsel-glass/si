<?php include 'seolang_header.tpl'; ?>
<div class="page-header">
   <div class="container-fluid">
      <div id="content1" style="border: none;">
         <style>
            .flexstyle {
               display: flex;
               flex-wrap: wrap;
               align-content: space-between;
               justify-content: space-between;
               width: 100%;
               text-rendering: optimizeLegibility;
               flex-grow: 1;
            }

            .flexheader {
               background-color: #242D37;
               color: #b3cbdd;
            }

            .flexheader>div {
               text-align: center;
               vertical-align: center;
               align-items: center;
               align-content: center;
               justify-content: center;
            }


            .flexcontent {
               background-color: #F0F0F0;
            }

            .buttonactive {
               background-color: green !important;
            }

            .flexactive {
               background-color: #d5edd5;
               color: green !important;
               font-weight: 600;
            }

            .flexstyle>div {
               border: solid 1px #FFF;
               flex-grow: 1;
               display: flex;
               vertical-align: center;
               height: 50px;
               vertical-align: center;
               align-items: center;
               align-content: center;
            }
            .flexstyle > div:nth-child(1) > a, .flexstyle > div:nth-child(2) > a  {
               color: #545454;
            }
            .flexactive > div:nth-child(1) > a, .flexactive > div:nth-child(2) > a {
               color: green;
            }

            .flexstyle>div:nth-child(1) {
               width: 10%;
               min-width: 200px;
               padding-left: 16px;
            }

            .flexstyle>div:nth-child(2) {
               padding-left: 16px;
               width: 20%;
            }

            .flexstyle>div:nth-child(3) {
               padding-left: 16px;
               width: 30%;
            }

            .flexstyle>div:nth-child(4) {
               width: 90px;
               text-align: center;
               vertical-align: center;
               align-items: center;
               align-content: center;
               justify-content: center;
            }

            .flexstyle>div:nth-child(5) {
               width: 60px;
               text-align: center;
               vertical-align: center;
               align-items: center;
               align-content: center;
               justify-content: center;
            }

            .flexstyle>div:nth-child(6) {
               width: 100px;
               text-align: center;
               vertical-align: center;
               align-items: center;
               align-content: center;
               justify-content: center;
               font-weight: 100;
            }

            .adapter-template-file {
               color: #888;
               font-size: 11px;
               font-weight: 100;
            }
         </style>






         <div class="flexstyle flexheader">
            <div>
               <?php echo $entry_adapter_column_directory; ?>
            </div>
            <div>
               <?php echo $entry_adapter_column_name; ?>
            </div>
            <div>
               <?php echo $entry_adapter_column_file; ?>
            </div>
            <div>
               <?php echo $entry_adapter_column_files_themes_count; ?>
            </div>
            <div>
               <?php echo $entry_adapter_column_image; ?>
            </div>
            <div>
               <?php echo $entry_adapter_column_action; ?>
            </div>

         </div>

         <?php foreach ($directories as $directory => $values) { ?>

            <div class="flexstyle flexcontent <?php if (isset($values['status']) && $values['status']) { ?>flexactive<?php } ?>">

               <div>
               <a href="<?php echo $values['action']; ?>"><?php echo $values['directory']; ?></a>
               </div>

               <div>
               <a href="<?php echo $values['action']; ?>"><?php echo $values['name']; ?></a>
               </div>

               <div>
                  <div style="display:block !important;">

                     <?php if (isset($values['file'])) { ?>
                        <div class="adapter-template-file"><?php echo $values['file']; ?></div>
                     <?php } ?>

                  </div>
               </div>

               <div>

                  <?php if (isset($values['files_themes_count'])) { ?>
                     <div class="adapter-template-file"><?php echo $values['files_themes_count']; ?></div>
                  <?php } ?>

               </div>


               <div class="thumbnails">
                  <?php if (isset($values['image']) && $values['image'] != '') { ?>
                     <a class="thumbnail" href="<?php echo $values['image']; ?>" title="(<?php echo strip_tags($values['name']); ?>)">
                        <img src="<?php echo $values['image']; ?>" style="height: 40px;">
                     </a>
                  <?php } ?>
               </div>

               <div>
                  <a href="<?php echo $values['action']; ?>" class="markbuttono sc_button <?php if (isset($values['status']) && $values['status']) { ?>buttonactive<?php } ?>"><i class="fa fa-refresh" aria-hidden="true"></i>&nbsp;<?php echo $language->get('entry_adapter_action'); ?></a>
               </div>

            </div>




         <?php } ?>





      </div>
   </div>


   <div class="flex200">

      <div>
         <div>
            <?php echo $language->get('entry_seolang_seolang_options'); ?>&nbsp;
         </div>

         <div class="input-group">
            <a href="<?php echo $url_seolang_seolang_options; ?>" class="markbuttono sc_button"><i class="fa fa-cog fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_langmark_widget'); ?></a>
         </div>
      </div>


      <div>
         <div>
            <?php echo $language->get('entry_seolang_langmark_options'); ?>&nbsp;
         </div>

         <div class="input-group">
            <a href="<?php echo $url_seolang_langmark_options; ?>" class="markbuttono sc_button"><i class="fa fa-cog fw" aria-hidden="true"></i>&nbsp;<?php echo $language->get('text_seolang_langmark_options'); ?></a>
         </div>
      </div>




   </div>

   <?php if (SC_VERSION < 20) { ?>
      <style>
         #footer {
            margin-top: 0px;
         }
      </style>
   <?php } ?>

   <script>
      $(document).ready(function() {
         $('.thumbnails').magnificPopup({
            type: 'image',
            delegate: 'a',
            gallery: {
               enabled: true
            }
         });
      });
   </script>

   <style>
      .thumbnail {
         padding: 0px;
         margin-bottom: 0px;
      }
   </style>


</div>

<?php if (SC_VERSION > 15) { ?>
   </div>
<?php } ?>
<script>
	setTimeout(function() {console.log('Alert hide'); $('.alert, .success').hide();}, 7000, '.alert, .success');
</script>
<?php if (isset($footer)) {
   echo $footer;
}
?>