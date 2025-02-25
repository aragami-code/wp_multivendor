<?php
/**

 */

global $WCMb;

$vendor_count = count($vendors); ?>
<div id="wcmb_widget_vendor_search" class="vendor_search_wrap">
	<?php wp_nonce_field( 'wcmb_widget_vendor_search_form', 'wcmb_vendor_search_nonce' ); ?>
	<input type="search" class="search_keyword search-field" placeholder="<?php echo __('Search Vendor…', 'MB-multivendor'); ?>" value="" name="s" style="width: 100%;margin-bottom: 10px;">
</div>
<?php
if($vendor_count > 5 )	{ ?>
	<div id="wcmb_widget_vendor_list" style="height: 308px; overflow-y: scroll; width: 226px;" >
<?php } else {?>
<div id="wcmb_widget_vendor_list" style=" height: auto; width: 226px;" >
<?php }
if($vendors) {
	foreach($vendors as $vendors_key => $vendor) {            
		$vendor->image = $vendor->get_image() ? $vendor->get_image() : $WCMb->plugin_url . 'assets/images/WP-stdavatar.png';
		?>
		<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
			<div style=" width: 25%;  display: inline;">		
			<img width="50" height="50" class="vendor_img" style="display: inline;" src=<?php echo $vendor->image; ?> id="vendor_image_display">
			</div>
			<div style=" width: 75%;  display: inline;  padding: 10px;">
					<a href="<?php echo esc_attr( $vendor->permalink ); ?>">
						<?php echo $vendor->page_title; ?>
					</a>
			</div>
		</div>
	<?php } 
}?>
</div>