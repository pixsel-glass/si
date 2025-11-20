		<div id="tab-multi-access-<?php echo $multi_name_row; ?>">
			<table>
				<tr>
					<td><?php echo $language->get('entry_seolang_customer_groups'); ?></td>
					<td>
						<div class="scrollbox">
							<?php $class = 'even'; ?>
							<?php if (!isset($seolang_settings_store['multi'][$multi_name]['access']['customer_groups'])) { ?>
								<?php foreach ($customer_groups as $customer_group) { ?>
									<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?php echo $class; ?>">
										<input type="checkbox" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][access][customer_groups][]" value="<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
										<?php echo $customer_group['name']; ?>
									</div>
								<?php } ?>

							<?php } else { ?>

								<?php foreach ($customer_groups as $customer_group) { ?>
									<?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
									<div class="<?php echo $class; ?>">
										<?php if (isset($seolang_settings_store['multi'][$multi_name]['access']['customer_groups']) && !empty($seolang_settings_store['multi'][$multi_name]['access']['customer_groups']) && in_array($customer_group['customer_group_id'], $seolang_settings_store['multi'][$multi_name]['access']['customer_groups'])) { ?>
											<input type="checkbox" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][access][customer_groups][]" value="<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
											<?php echo $customer_group['name']; ?>
										<?php } else { ?>
											<input type="checkbox" name="seolang_settings_<?php echo $store_id ?>[multi][<?php echo $multi_name; ?>][access][customer_groups][]" value="<?php echo $customer_group['customer_group_id']; ?>" />
											<?php echo $customer_group['name']; ?>
										<?php } ?>
									</div>
								<?php } ?>

							<?php } ?>
						</div>
						<a onclick="$(this).parent().find(':checkbox').prop('checked', true);" class="nohref"><?php echo $language->get('text_seolang_select_all'); ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);" class="nohref"><?php echo $language->get('text_seolang_unselect_all'); ?></a>
					</td>
					</td>
				</tr>

			</table>
		</div>