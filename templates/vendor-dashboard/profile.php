<?php
/**
 
 */
global $WCMb;

$vendor_profile_image = get_user_meta($user->ID, '_vendor_profile_image', true);
?>
<div class="col-md-12">
    <form method="post" name="profile_settings_form" class="wcmb_profile_form form-horizontal">
        <?php do_action('wcmb_before_vendor_dashboard_profile'); ?>
		<div class="panel panel-default pannel-outer-heading">
			<div class="panel-heading">
				<h3><?php _e('Personal Information', 'MB-multivendor'); ?></h3>
			</div>
			<div class="panel-body panel-content-padding">
				<div class="wcmb_form1">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('First Name', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_profile_data[first_name]" value="<?php echo isset($user->first_name)? $user->first_name : ''; ?>"  placeholder="<?php _e('Enter your First Name here', 'MB-multivendor'); ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Last Name', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_profile_data[last_name]" value="<?php echo isset($user->last_name)? $user->last_name : ''; ?>"  placeholder="<?php _e('Enter your Last Name here', 'MB-multivendor'); ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Email', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_profile_data[user_email]" required value="<?php echo isset($user->user_email) ? $user->user_email : ''; ?>"  placeholder="<?php _e('Enter your Email here', 'MB-multivendor'); ?>">
                            <div class="wcmb-do-change-pass">
                                <button type="button" class="btn btn-secondary" id="wcmb-change-pass"><?php _e('Change Password', 'MB-multivendor'); ?></button>
                            </div>
                        </div>  
                    </div>
                    
                    <div class="form-group vendor-edit-pass-field" style="display:none;">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Current password', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="password" name="vendor_profile_data[password_current]" autocomplete="off" >
                            <div class="hints"><?php _e('Keep it blank for not to update.', 'MB-multivendor'); ?></div>
                        </div>  
                    </div>
                    <div class="form-group vendor-edit-pass-field" style="display:none;">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('New password', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="password" name="vendor_profile_data[password_1]" autocomplete="off" >
                            <div class="hints"><?php _e('Keep it blank for not to update.', 'MB-multivendor'); ?></div>
                        </div>  
                    </div>
                    <div class="form-group vendor-edit-pass-field" style="display:none;">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Confirm new password', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="password" name="vendor_profile_data[password_2]" autocomplete="off" >
                        </div>  
                    </div>             
                    
                    <?php if ($WCMb->vendor_caps->vendor_can('is_upload_files')) { ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Profile Image', 'MB-multivendor'); ?></label>
                        <div class="col-md-6 col-sm-9">
                        	<img id="vendor-profile-img" src="<?php echo (isset($vendor_profile_image) && $vendor_profile_image > 0) ? wp_get_attachment_url($vendor_profile_image) :  get_avatar_url($user->ID, array('size' => 120)); ?>" width="120" alt="dp">
							<div class="wcmb-media profile-pic-btn">
								<button type="button" class="btn btn-secondary wcmb_upload_btn" data-target="vendor-profile"><i class="wcmb-font ico-edit-pencil-icon"></i> <?php _e('Upload image', 'MB-multivendor'); ?></button>
							</div>
							<input type="hidden" name="vendor_profile_data[vendor_profile_image]" id="vendor-profile-img-id" class="user-profile-fields" value="<?php echo isset($vendor_profile_image) ? $vendor_profile_image : ''; ?>"  />
						</div>  
                    </div>
                    <?php } ?>
                </div>
			</div>
		</div>
        <?php do_action('wcmb_after_vendor_dashboard_profile'); ?>
        <?php do_action('other_exta_field_dcmv'); ?>
        <div class="wcmb-action-container">
            <button class="btn btn-default" name="store_save_profile"><?php _e('Save Options', 'MB-multivendor'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>