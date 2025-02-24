<?php
/**

 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMb;

?>
<table style="width:100%; color: #737373; border: 1px solid #e4e4e4; background:none;" border="0" cellpadding="8" cellspacing="0">
	<tbody>
		<?php $cc = 0;
		if( apply_filters('wcmb_vendor_can_overwrite_customer_support', true) ) {
			foreach ( $vendor_array as $vendor_id => $products) { 
                            if(is_user_wcmb_vendor($vendor_id)){
				$vendor_meta = get_user_meta( $vendor_id );
				
//				if( (
//						isset($vendor_meta['_vendor_customer_phone'][0]) &&  
//						isset($vendor_meta['_vendor_customer_email'][0]) && 
//						isset($vendor_meta['_vendor_csd_return_address1'][0]) && 
//						isset($vendor_meta['_vendor_csd_return_city'][0]) && 
//						isset($vendor_meta['_vendor_csd_return_state'][0]) && 
//						isset($vendor_meta['_vendor_csd_return_zip'][0]) 
//						) 
//				) {
				?>
				<?php if($cc == 0) { ?>
					<tr>
					<th style="padding:10px 10px; background:none; border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; width:100%;" align="left" valign="top"><?php echo __('Vendor Details', 'MB-multivendor'); ?></th>
					
					</tr>
				<?php } ?>
				<tr>
					<td style="padding:10px 10px; background:none; border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; width:100%;" align="left" valign="top">
						<p><strong><?php echo __('Vendor Name', 'MB-multivendor');?> </strong><br>
						<?php echo $vendor_meta['nickname'][0] ?> </p>
						<p><strong><?php echo __('Product Name', 'MB-multivendor');?> </strong> <br>
							 <?php echo $products; ?>
						</p>						
						<p style="border-bottom:1px solid #eeeeee; padding-bottom:10px"> <strong><?php echo __('Customer Support Details', 'MB-multivendor');?></strong></p>
						<?php if(isset($vendor_meta['_vendor_customer_email'][0])) { ?>
						<p><strong><?php echo __('Email : ','MB-multivendor');?></strong><br>
						<a style="color:#505050;font-weight:normal;text-decoration:underline" href="mailto:<?php echo $vendor_meta['_vendor_customer_email'][0]; ?>" target="_blank"><?php echo $vendor_meta['_vendor_customer_email'][0]; ?></a>
						</p>
						<?php }?>
						<?php if(isset($vendor_meta['_vendor_customer_phone'][0])) { ?>
						<p><strong><?php echo __('Phone : ','MB-multivendor'); ?></strong> <br>
						<?php echo $vendor_meta['_vendor_customer_phone'][0]; ?></p>
						<?php }?>
						<p><strong><?php echo __('Return Address ', 'MB-multivendor');?></strong></p>
						<p>
							<?php if(isset($vendor_meta['_vendor_csd_return_address1'][0])) {  echo $vendor_meta['_vendor_csd_return_address1'][0]; ?><br> <?php }?>
							<?php if(isset($vendor_meta['_vendor_csd_return_address2'][0])) { echo $vendor_meta['_vendor_csd_return_address2'][0]; ?><br> <?php }?>
							<?php if(isset($vendor_meta['_vendor_csd_return_city'][0])) { echo $vendor_meta['_vendor_csd_return_city'][0]; ?><br> <?php }?>
							<?php if(isset($vendor_meta['_vendor_csd_return_state'][0])) { echo $vendor_meta['_vendor_csd_return_state'][0]; ?><br> <?php }?>
							<?php if(isset($vendor_meta['_vendor_csd_return_country'][0])) { echo $vendor_meta['_vendor_csd_return_country'][0]; ?><br> <?php }?>
							<?php if(isset($vendor_meta['_vendor_csd_return_zip'][0])) { echo $vendor_meta['_vendor_csd_return_zip'][0]; } ?>
						</p>
						
													
						</td>
					
				</tr>
				<?php $cc++;			
				}
			}
		}
		else {
			?>
			<tr>
					<th style="padding:10px 10px; background:none; border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; width:100%;" align="left" valign="top"><?php echo __('Customer Support Details & Product Return Address', 'MB-multivendor'); ?></th>
					
					</tr>
					<tr>
					<td style="padding:10px 10px; background:none; border-right: 1px solid #e4e4e4; border-bottom: 1px solid #e4e4e4; width:100%;" align="left" valign="top">
					<p style="border-bottom:1px solid #eeeeee; padding-bottom:10px"> <strong><?php echo __('Customer Support Details', 'MB-multivendor');?></strong></p>
					<?php if(isset($customer_support_details_settings['csd_email'])) { ?>
						<p><strong><?php echo __('Email : ','MB-multivendor');?></strong><br>
						<a style="color:#505050;font-weight:normal;text-decoration:underline" href="mailto:<?php echo $customer_support_details_settings['csd_email']; ?>" target="_blank"><?php echo $customer_support_details_settings['csd_email']; ?></a>					
						</p>
						<?php }?>
						<?php if(isset($customer_support_details_settings['csd_phone'])) { ?>
							<p><strong><?php echo __('Phone : ','MB-multivendor'); ?></strong> <br>
							<?php echo $customer_support_details_settings['csd_phone'];?></p>
						<?php }?>						
						<p><strong><?php echo __('Return Address ', 'MB-multivendor');?></strong></p>
						<p>
							<?php if(isset($customer_support_details_settings['csd_return_address_1'])) { ?>
								<?php echo $customer_support_details_settings['csd_return_address_1']; ?><br>
							<?php }?>
							<?php if(isset($customer_support_details_settings['csd_return_address_2'])) { ?>
								<?php echo $customer_support_details_settings['csd_return_address_2']; ?><br>
							<?php }?>
							<?php if(isset($customer_support_details_settings['csd_return_city'])) { ?>
								<?php echo $customer_support_details_settings['csd_return_city'];?> <br>
							<?php }?>
							<?php if(isset($customer_support_details_settings['csd_return_state'])) { ?>
								<?php echo $customer_support_details_settings['csd_return_state']; ?><br>
							<?php }?>
							<?php if(isset($customer_support_details_settings['csd_return_country'])) { ?>
								<?php echo $customer_support_details_settings['csd_return_country'];?><br>
							<?php }?>
							<?php if(isset($customer_support_details_settings['csd_return_zipcode'])) { ?>
								<?php echo $customer_support_details_settings['csd_return_zipcode'];?>
							<?php }?>					
						</p>
					
					
					</tr>
			<?php
			
		}?>                           
	</tbody>
</table>




