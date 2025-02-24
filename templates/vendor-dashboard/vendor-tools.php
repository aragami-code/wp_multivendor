<?php

/**

 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}

do_action( 'before_wcmb_vendor_tools_content' );
?>
<div class="col-md-12">
    <div class="panel panel-default panel-pading">
        <div class="wcmb-vendor-tools panel-body">
            <div class="tools-item">
                <label class="control-label col-md-9 col-sm-6">
                    <?php _e( 'Vendor Dashboard Transients', 'MB-multivendor' ); ?>
                    <p class="description"><?php _e( 'This tool will clear the dashboard widget transients cache.', 'MB-multivendor' ); ?></p>
                </label>
                <div class="col-md-3 col-sm-6">
                    <a class="wcmb_vendor_clear_transients btn btn-default" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'tools_action' => 'clear_all_transients' ), wcmb_get_vendor_dashboard_endpoint_url( get_wcmb_vendor_settings( 'wcmb_clear_cache_endpoint', 'vendor', 'general', 'vendor-tools' ) ) ), 'wcmb_clear_vendor_transients' ) ); ?>"><?php _e( 'Clear transients', 'MB-multivendor' ) ?></a>
                </div>
            </div>
            <?php do_action( 'wcmb_vendor_dashboard_tools_item' ); ?>
        </div>
    </div>
</div>
<?php
do_action( 'after_wcmb_vendor_tools_content' );