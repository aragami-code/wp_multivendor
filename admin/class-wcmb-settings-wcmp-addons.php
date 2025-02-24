<?php

class WCMb_Settings_WCMb_Addons {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmb_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMb, $wp_version;
        $args = apply_filters( 'wcmb_extensions_addons_remote_args', array(
            'timeout' => 25,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null
        ));
        $url = 'https://wc-marketplace.com/wp-json/wc/v2/products/?per_page=100&orderby=title&order=asc&status=publish';
        $response = wp_remote_get($url, $args);
        ?>
        <div class="wcmb-addon-container">
            <div class="addon-banner">
                <img src="<?php echo $WCMb->plugin_url.'assets/images/addon-banner.png' ?>" />
                <div class="addon-banner-content">
                   
                </div>
            </div>
            <div class="addonbox-container">
                <?php
                if (!is_wp_error($response) && isset($response['body'])) {
                    foreach (json_decode($response['body']) as $product) {
                        if (isset($product->id) && $product->id != 12603) {
                            ?>
                            <div class="addonbox">
                                <h2><?php echo $product->name; ?></h2> 
                                <div class="addon-img-holder">
                                    <?php
                                        $all_meta_data = wp_list_pluck($product->meta_data, 'value' ,'key'); 
                                        if( ! empty( $all_meta_data['extension_img_path'] ) ) {
                                    ?>
                                    <img src="<?php echo $all_meta_data['extension_img_path']; ?>" alt="wcmb" />    
                                    <?php
                                        } else {
                                    ?>

                                    <img src="<?php echo $product->images[0]->src; ?>" alt="wcmb" />

                                    <?php
                                        }
                                    ?>  
                                </div>   
                                <div class="addon-content-holder">
                                    <p><?php echo wp_trim_words(strip_tags($product->short_description), 25, '...'); ?></p> 
                                    <a href="<?php echo $product->permalink; ?>" target="_blank" class="button"><?php _e('View More!', 'MB-multivendor'); ?></a>  
                                </div> 
                            </div>
                            <?php
                        }
                    }
                } else{
                    ?>
                    <div class="offline-addon-wrap">
                        <div class="addon-content">
                          
                <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

}
