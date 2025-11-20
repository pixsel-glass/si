
<script>

tab_options_widget_html = $('#tabs-options-widgets').html();

tab_options_widget_html = tab_options_widget_html + '<a href="#tab-options-<?php echo $widget;?>" class="lm-tab"><?php echo $language->get('entry_seolang_widget_' . $widget); ?></a>'; 

$('#tabs-options-widgets').html(tab_options_widget_html);


</script>	

<div id="tab-options-<?php echo $widget;?>">

<div class="sh-block-layouts" style="
     			display: flex;
     			flex-wrap: wrap;
				align-content: space-between;
				justify-content: space-between;
				width: 100%;
				margin-top: 4px; margin-bottom: 8px;
				 background-color: #F4F6F7;
				 padding: 8px;
				">
				<div>

				</div>
				<div>
					<span class="markbutton button_blue nohref" style="font-size: 14px;"><?php echo $language->get('entry_seolang_widget_' . $widget); ?></span>
				</div>
				<div>

				</div>

</div>

<div style="
           display: flex;
           flex-wrap: wrap;
           align-content: space-between;
           justify-content: space-between;
           width: 100%;
           margin-top: 4px; margin-bottom: 8px;
           ">

	<div style="display: flex; align-items: center;">
	   <div style="width: 160px;">
	   	 <?php echo $language->get('text_seolang_status'); ?> <?php echo $language->get('entry_seolang_widget_' . $widget); ?>&nbsp;
	   </div>
	   <div class="input-group">
	      <select class="form-control" name="seolang_settings[widget_<?php echo $widget; ?>_status]">
	         <?php if (isset($seolang_settings['widget_' . $widget . '_status']) && $seolang_settings['widget_' . $widget . '_status']) { ?>
	         <option value="1" selected="selected"><?php echo $text_seolang_enabled; ?></option>
	         <option value="0"><?php echo $text_seolang_disabled; ?></option>
	         <?php } else { ?>
	         <option value="1"><?php echo $text_seolang_enabled; ?></option>
	         <option value="0" selected="selected"><?php echo $text_seolang_disabled; ?></option>
	         <?php } ?>
	      </select>
	   </div>
	</div>

</div>
</div>