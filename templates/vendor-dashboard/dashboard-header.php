<?php
/**

 */
if (!defined('ABSPATH')) {
    exit;
}
global $WCMb;
$vendor = get_wcmb_vendor(get_current_vendor_id());
if($vendor) {
	$vendor_logo = $vendor->profile_image ? wp_get_attachment_url($vendor->profile_image) : get_avatar_url(get_current_vendor_id(), array('size' => 80));
} else {
    $vendor_logo = get_avatar_url(get_current_vendor_id(), array('size' => 80));
}
$site_logo = get_wcmb_vendor_settings('wcmb_dashboard_site_logo', 'vendor', 'dashboard') ? get_wcmb_vendor_settings('wcmb_dashboard_site_logo', 'vendor', 'dashboard') : '';
?>

<!-- Top bar -->
<div class="top-navbar white-bkg">
    <div class="navbar navbar-default">
        <div class="topbar-left pull-left pos-rel">
            <div class="site-logo text-center pos-middle">
                <a href="<?php echo apply_filters('wcmb_vendor_dashboard_header_site_url', site_url(), $vendor); ?>">
                    <?php if ($site_logo) { ?>
                        <img src="<?php echo get_url_from_upload_field_value($site_logo); ?>" alt="<?php echo bloginfo(); ?>">
                    <?php } else {
                        echo bloginfo();
                    } ?>
                </a>
            </div>
        </div>
        <ul class="nav pull-right top-user-nav">
            <li class="dropdown login-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="wcmb-font ico-tools-icon"></i>
                    <span><i class="wcmb-font ico-down-arrow-icon"></i></span>
                </a>
                <ul class="dropdown-menu dropdown-user dropdown-menu-right">
                    <li class="sidebar-logo text-center"> 
                        <div class="vendor-profile-pic-holder">
                            <img src="<?php echo $vendor_logo; ?>" alt="vendor logo">
                        </div>
                        <h4><?php
                            if ($vendor) {
                                echo $vendor->user_data->data->display_name;
                            } else {
                                $user = wp_get_current_user();
                                echo $user->data->user_email;
                            }
                            ?></h4>  
                    </li> 
                    <?php
                    $panel_nav = $WCMb->vendor_dashboard->dashboard_header_right_panel_nav();
                    if ($panel_nav) :
                        if (!$vendor) {
                            unset($panel_nav['storefront']);
                            unset($panel_nav['wp-admin']);
                            unset($panel_nav['profile']);
                        }
                        sksort($panel_nav, 'position', true);
                        foreach ($panel_nav as $key => $nav):
                            if (current_user_can($nav['capability']) || $nav['capability'] === true):
                                ?>
                                <li class="<?php if (!empty($nav['class'])) echo $nav['class']; ?>"><a href="<?php echo esc_url($nav['url']); ?>" target="<?php echo $nav['link_target']; ?>"><i class="<?php echo $nav['nav_icon']; ?>"></i> <span><?php echo $nav['label']; ?></span></a></li>
                            <?php
                            endif;
                        endforeach;
                    endif;
                    ?>

<?php do_action('wcmb_dashboard_header_right_vendor_dropdown'); ?>
                </ul>
                <!-- /.dropdown -->
            </li>
        </ul>

        <?php
        if ($vendor)
            $header_nav = $WCMb->vendor_dashboard->dashboard_header_nav();
        else
            $header_nav = false;

        if ($header_nav) :
            sksort($header_nav, 'position', true);
            ?>
            <ul class="nav navbar-top-links navbar-right pull-right btm-nav-fixed">
                        <?php
                        foreach ($header_nav as $key => $nav):
                            if (current_user_can($nav['capability']) || $nav['capability'] === true):
                                ?>
                        <li class="notification-link <?php if (!empty($nav['class'])) echo $nav['class']; ?>">
                            <a href="<?php echo esc_url($nav['url']); ?>" target="<?php echo $nav['link_target']; ?>" title="<?php echo $nav['label']; ?>">
                                <i class="<?php echo $nav['nav_icon']; ?>"></i> <span class="hidden-sm hidden-xs"><?php echo $nav['label']; ?></span>
                        <?php
                        if ($key == 'announcement') :
                            $vendor_announcements = $vendor->get_announcements();
                            if (isset($vendor_announcements['unread']) && count($vendor_announcements['unread']) > 0) {
                                echo '<span class="notification-blink">'.count($vendor_announcements['unread']).'</span>';
                            }
                        endif;
                        ?>
                            </a>
                        </li>
            <?php
        endif;
    endforeach;
    ?>
            </ul>     
<?php endif; ?>
        <!-- /.navbar-top-links -->
    </div>
</div>