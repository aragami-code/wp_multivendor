<?php

/**

 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb, $wp_version;

wp_clear_scheduled_hook('masspay_cron_start');
wp_clear_scheduled_hook('vendor_monthly_order_stats');
wp_clear_scheduled_hook('vendor_weekly_order_stats');
wp_clear_scheduled_hook('migrate_spmv_multivendor_table');
wp_clear_scheduled_hook('wcmb_spmv_excluded_products_map');
wp_clear_scheduled_hook('wcmb_spmv_product_meta_update');

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if (defined('WCMB_REMOVE_ALL_DATA') && true === WCMB_REMOVE_ALL_DATA) {
    // Roles + caps.
    include_once( dirname(__FILE__) . '/includes/wcmb-core-functions.php' );
    remove_role('dc_vendor');
    remove_role('dc_pending_vendor');
    remove_role('dc_rejected_vendor');
    // Pages.
    wp_trash_post(wcmb_vendor_dashboard_page_id());
    wp_trash_post(wcmb_vendor_registration_page_id());

    // Tables.
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_vendor_orders");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_products_map");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_visitors_stats");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_cust_questions");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_cust_answers");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_shipping_zone_methods");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}wcmb_shipping_zone_locations");

    // Delete options.
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wcmb\_%';");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'dc\_%';");

    // Delete posts + data.
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'dc_commission', 'wcmb_vendor_notice', 'wcmb_transaction', 'wcmb_university', 'wcmb_vendorrequest' );");
    $wpdb->query("DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;");
    

    // Delete terms if > WP 4.2 (term splitting was added in 4.2)
    if (version_compare($wp_version, '4.2', '>=')) {
        // Delete term taxonomie

        $wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => 'dc_vendor_shop') );

        // Delete orphan relationships
        $wpdb->query("DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;");

        // Delete orphan terms
        $wpdb->query("DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;");

        // Delete orphan term meta
        if (!empty($wpdb->termmeta)) {
            $wpdb->query("DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;");
        }
    }

    // Clear any cached data that has been removed
    wp_cache_flush();
}
