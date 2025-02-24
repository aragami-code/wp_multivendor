<?php
    global $WCMb;
    $vendor_shipping_methods = wcmb_get_shipping_methods();
?>
<div id="wcmb_shipping_method_add_container" class="collapse wcmb-modal-dialog">
    <div class="wcmb-modal">
        <div class="wcmb-modal-content" tabindex="0">
            <section class="wcmb-modal-main" role="main">
                <header class="wcmb-modal-header">
                    <h1><?php _e( 'Add shipping method', 'MB-multivendor' ); ?></h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text"><?php _e( 'Close modal panel', 'MB-multivendor' ); ?></span>
                    </button>
                </header>
                <article>
                    <form action="" method="post">
                        <div class="wc-shipping-zone-method-selector">
                            <p><?php _e( 'Choose the shipping method you wish to add. Only shipping methods which support zones are listed.', 'MB-multivendor' ); ?></p>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-9">
                                    <select id="shipping_method" class="form-control mt-15" name="wcmb_shipping_method">
                                        <?php foreach( $vendor_shipping_methods as $key => $method ) { 
                                            echo '<option data-description="' . esc_attr( wp_kses_post( wpautop( $method->get_method_description() ) ) ) . '" value="' . esc_attr( $method->id ) . '">' . esc_attr( $method->get_method_title() ) . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="wc-shipping-zone-method-description"><p><?php _e( 'Lets you charge a fixed rate for shipping.', 'MB-multivendor' ); ?></p></div>
                        </div>
                    </form>
                </article>
                <footer>
                    <div class="inner">
                        <button id="btn-ok" class="btn btn-default add-shipping-method"><?php _e( 'Add shipping method', 'MB-multivendor' ); ?></button>
                    </div>
                </footer>
            </section>
        </div>
    </div>
    <div class="wcmb-modal-backdrop modal-close"></div>
</div>