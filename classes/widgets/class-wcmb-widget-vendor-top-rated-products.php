<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCMb_Widget_Vendor_Top_Rated_Products extends WC_Widget {

	public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'wcmb woocommerce wcmb_widget_vendor_top_rated_products widget_top_rated_products';
        $this->widget_description = __('Displays a list of vendor top-rated products on the vendor shop page.', 'MB-multivendor');
        $this->widget_id = 'wcmb_vendor_top_rated_products';
        $this->widget_name = __('MB: Vendor\'s Products by Rating', 'MB-multivendor');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('Vendor top rated products', 'MB-multivendor'),
                'label' => __('Title', 'MB-multivendor'),
            ),
            'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of products to show', 'MB-multivendor' ),
			),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $wp_query, $WCMb;
        if ( $this->get_cached_widget( $args ) ) {
			return;
		}
		
		if (!is_tax($WCMb->taxonomy->taxonomy_name)) {
            return;
        }

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];

		$this->vendor_term_id = $wp_query->queried_object->term_id;
        $vendor = get_wcmb_vendor_by_term($this->vendor_term_id);
        
		$query_args = array(
			'posts_per_page' => $number,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'author'		 => $vendor->ID,
			'meta_key'       => '_wc_average_rating',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_query'     => WC()->query->get_meta_query(),
			'tax_query'      => WC()->query->get_tax_query(),
		); // WPCS: slow query ok.

		$r = new WP_Query( $query_args );

		if ( $r->have_posts() ) {

			$this->widget_start( $args, $instance );

			echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

			$template_args = array(
				'widget_id'   => $args['widget_id'],
				'show_rating' => true,
			);

			while ( $r->have_posts() ) {
				$r->the_post();
				wc_get_template( 'content-widget-product.php', $template_args );
			}

			echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );

			$this->widget_end( $args );
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content; // WPCS: XSS ok.

		$this->cache_widget( $args, $content );
	}
}
