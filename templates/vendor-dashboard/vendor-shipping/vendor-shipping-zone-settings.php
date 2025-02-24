<?php global $WCMb; ?>
<div class="zone-component panel-content-padding">
	<div class="shipping-zone-list">
		<a href="javascript:void(0);" ><i class="wcmb-font ico-back-arrow"></i> <?php  _e('Shipping Zones', 'MB-multivendor'); ?></a>
		<hr>
	</div>
	<form action="" method="post">
		<div class="form-group"> 
			<label class="control-label col-sm-3 col-md-3"><?php  _e('Zone Name', 'MB-multivendor'); ?></label>
			<div class="col-md-6 col-sm-9"><?php _e($zones['data']['zone_name'], 'MB-multivendor'); ?></div>
		</div>
		<div class="form-group"> 
			<label class="control-label col-sm-3 col-md-3"><?php  _e('Zone region', 'MB-multivendor'); ?></label>
			<div class="col-md-6 col-sm-9"><?php _e($zones['formatted_zone_location'], 'MB-multivendor'); ?></div>
		</div>		
		<div class="form-group">
		   	<div class="col-md-6 col-sm-9">
		    	<input id="zone_id" class="form-control" type="hidden" name="<?php echo 'wcmb_shipping_zone['. $zone_id .'][_zone_id]'; ?>" value="<?php echo $zone_id; ?>">
		   	</div>
		</div>
		<?php if( $show_limit_location_link && $zone_id !== 0 ) { ?>
			<div class="form-group">
			   	<label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Limit Zone Location', 'MB-multivendor' ); ?></label>
			   	<div class="col-md-6 col-sm-9">
			    	<input id="limit_zone_location" class="form-control" type="checkbox" name="<?php echo 'wcmb_shipping_zone['. $zone_id .'][_limit_zone_location]'; ?>" value="1" <?php checked( $want_to_limit_location, 1 ); ?>>
			   	</div>
			</div>
		<?php } ?>
		<?php if( $show_state_list ) { ?>
			<div class="form-group hide_if_zone_not_limited">
			   	<label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Select specific states', 'MB-multivendor' ); ?></label>
			   	<div class="col-md-6 col-sm-9">
			    	<select id="select_zone_states" class="form-control" name="<?php echo 'wcmb_shipping_zone['. $zone_id .'][_select_zone_states][]'; ?>" multiple>
			    		<?php foreach( $state_key_by_country as $key => $value ) { ?>
			    			<option value="<?php echo $key; ?>" <?php selected( in_array( $key, $states ), true ); ?>><?php echo $value; ?></option>
			    		<?php } ?>
			    	</select>
			   	</div>
			</div>
		<?php } ?>
		<?php if( $show_post_code_list ) { ?>
			<div class="form-group hide_if_zone_not_limited">
			   	<label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Set your postcode', 'MB-multivendor' ); ?></label>
			   	<div class="col-md-6 col-sm-9">
			    	<input id="select_zone_postcodes" class="form-control" type="text" name="<?php echo 'wcmb_shipping_zone['. $zone_id .'][_select_zone_postcodes]'; ?>" value="<?php echo $postcodes; ?>" placholder="<?php _e( 'Postcodes need to be comma separated', 'MB-multivendor' ); ?>">
			   	</div>
			</div>
		<?php } ?>
	</form>
	<div class="wcmb-zone-method-wrapper form-group mt-10">
		<label class="control-label col-sm-3 col-md-3 wcmb-zone-method-heading" for="_sku">
			<?php _e( 'Shipping methods', 'MB-multivendor' ); ?>
            <div class="form-text mt-10 small"><?php _e('Add your shipping method for appropiate zone', 'MB-multivendor'); ?></div>
        </label> 
		<div class="wcmb-zone-method-content col-md-9 col-sm-9">
			<table class="table wcmb-table zone-method-table table-bordered">
				<thead>
					<tr>
						<th class="title"><?php _e('Title', 'MB-multivendor'); ?></th>
						<th class="enabled"><?php _e('Enabled', 'MB-multivendor'); ?></th> 
						<th class="description"><?php _e('Description', 'MB-multivendor'); ?></th>
						<th class="action"><?php _e('Action', 'MB-multivendor'); ?></th>
					</tr>
				</thead> 
				<tbody>
					<?php 
					if(empty($vendor_shipping_methods)) { ?> 
						<tr>
							<td colspan="4"><?php _e( 'You can add multiple shipping methods within this zone. Only customers within the zone will see them.', 'MB-multivendor' ); ?></td>
						</tr>
						<?php 
					} else { 
						foreach ( $vendor_shipping_methods as $vendor_shipping_method ) { ?>
							<tr>
								<td><?php _e($vendor_shipping_method['title'], 'wcmb' ); ?>
									<div data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>' class="row-actions edit_del_actions">
									</div>
								</td>
								<td> 
									<input id="method_status" class="form-control method-status" type="checkbox" name="<?php echo 'method_status'; ?>" value="<?php echo $vendor_shipping_method['instance_id']; ?>" <?php checked( ( $vendor_shipping_method['enabled'] == "yes" ), true ); ?>>
								</td>
								<td><?php _e($vendor_shipping_method['settings']['description'], 'MB-multivendor' ); ?></td>
								<td>
									<div class="col-actions edit_del_actions" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>'>
										<span class="edit"><a href="javascript:void(0);" class="edit-shipping-method" title="<?php _e( 'Edit', 'MB-multivendor' ) ?>"><i class="wcmb-font ico-edit-pencil-icon"></i></a>
										</span>|
										<span class="delete"><a class="delete-shipping-method" href="javascript:void(0);" title="<?php _e( 'Delete', 'MB-multivendor' ) ?>"><i class="wcmb-font ico-delete-icon"></i></a></span>
									</div>
								</td>
							</tr>
						<?php 
					}
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="wcmb-zone-method-footer col-md-9 col-sm-9 col-md-offset-3 mt-15">
			<a href="javascript:void(0);" class="btn btn-default wcmb-zone-method-add-btn show-shipping-methods"><i class="fa fa-plus"></i><?php _e( 'Add Shipping Method', 'MB-multivendor' ) ?></a>
		</div>
		<?php 
                    $WCMb->template->get_template( 'vendor-dashboard/vendor-shipping/vendor-edit-shipping-method.php' );
                    $WCMb->template->get_template( 'vendor-dashboard/vendor-shipping/vendor-add-shipping-method.php' );
		?>
	</div>
</div>