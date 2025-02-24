<?php
/*

 */
if (!defined('ABSPATH')) {
    exit;
}
global $WCMb;

sksort($nav_items, 'position', true);

$add_vendor_navigation = is_user_wcmb_vendor(get_current_user_id());
if(!$add_vendor_navigation) $add_vendor_navigation = is_user_wcmb_pending_vendor(get_current_user_id());
if(!$add_vendor_navigation) $add_vendor_navigation = is_user_wcmb_rejected_vendor(get_current_user_id());
if(!$add_vendor_navigation){
    return;
}

do_action('wcmb_before_vendor_dashboard_navigation');
?>
<!-- Navigation 
<nav class="navbar navbar-default navbar-static-top">
    <div class="navbar-header">
        <button data-toggle="collapse-side"  type="button" class="navbar-toggle pull-left larr collapsed">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>-->
    <!-- /.navbar-header -->
 <!--   <div class="navbar-default sidebar side-collapse" id="side-collapse" role="navigation">
        <div class="mCustomScrollbar" data-mcs-theme="minimal-dark">
              <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                  <?php foreach ($nav_items as $key => $item): ?>
                        <?php if (current_user_can($item['capability']) || $item['capability'] === true): ?>
                            <li class="<?php if(!empty($item['submenu'])){ echo 'hasmenu';} ?>">
                                <?php if(array_key_exists($WCMb->endpoints->get_current_endpoint(), $item['submenu'])){ $force_active = true;} else {$force_active = false;}?>
                                <a href="<?php echo esc_url($item['url']); ?>" target="<?php echo $item['link_target'] ?>" data-menu_item="<?php echo $key ?>" class="<?php echo implode(' ', array_map('sanitize_html_class', wcmb_get_vendor_dashboard_nav_item_css_class($key, $force_active))); ?>">
                                    <i class="<?php echo $item['nav_icon'] ?>"></i> 
                                    <span><?php echo esc_html($item['label']); ?></span>
                                    <?php if(!empty($item['submenu'])): ?><i class="wcmb-font ico-downarrow-2-icon"></i><?php endif; ?>
                                </a>
                                <?php if (!empty($item['submenu']) && is_array($item['submenu'])): sksort($item['submenu'], 'position', true) ?>
                                    <ul class="nav submenu" <?php if(!in_array('active', wcmb_get_vendor_dashboard_nav_item_css_class($key, $force_active))){ echo 'style="display:none"'; }else{ echo 'style="display:block"'; } ?>>
                                        <?php foreach ($item['submenu'] as $submenukey => $submenu): ?>
                                            <?php if(current_user_can($submenu['capability']) || $submenu['capability'] === true): ?>
                                                <li>
                                                    <a href="<?php echo esc_url($submenu['url']); ?>" target="<?php echo $submenu['link_target'] ?>" class="<?php echo implode(' ', array_map('sanitize_html_class', wcmb_get_vendor_dashboard_nav_item_css_class($submenukey))); ?>">-- <?php echo esc_html($submenu['label']); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>-->
        <!-- /.sidebar-collapse 
    </div>-->
    <!-- /.navbar-static-side 
</nav>-->
<?php do_action('wcmb_after_vendor_dashboard_navigation'); ?>