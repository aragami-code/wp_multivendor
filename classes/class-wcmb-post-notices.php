<?php
/**
 
 */
 
class WCMb_Notices {
	private $post_type;
  public $dir;
  public $file;
  
  public function __construct() {
    $this->post_type = 'wcmb_vendor_notice';
    $this->register_post_type();
		add_action( 'add_meta_boxes', array($this,'vendor_notices_add_meta_box_addtional_field') );
		add_action( 'save_post', array( $this, 'vendor_notices_save_addtional_field' ), 10, 3 );		
  }
  
  
  public function vendor_notices_add_meta_box_addtional_field() {
  	global $WCMb;
		$screens = array( 'wcmb_vendor_notice' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wcmb_vendor_notice_addtional_field',
				__( 'Addtional Fields', 'MB-multivendor' ),
				array($this,'wcmb_vendor_notice_addtional_field_callback'),
				$screen,
				'normal',
				'high'
			);
		}  	
  }
  
  public function wcmb_vendor_notice_addtional_field_callback() {
  	global $WCMb, $post;
  	$url = get_post_meta($post->ID,'_wcmb_vendor_notices_url', true);
  	?>
  	<div id="_wcmb_vendor_notices_url_div" class="_wcmb_vendor_notices_url_div" >
  		<label>Enter Url</label>
  		<input type="text" name="_wcmb_vendor_notices_url" value="<?php echo $url; ?>" class="widefat" style="margin:10px; border:1px solid #888; width:90%;" >			
		</div>			
		<?php
  }
  
  public function vendor_notices_save_addtional_field($post_id, $post, $update) {
  	global $WCMb;
  	if (  $this->post_type != $post->post_type ) {
        return;
    }
    if(isset($_POST['_wcmb_vendor_notices_url'])) {
    	update_post_meta($post_id, '_wcmb_vendor_notices_url', $_POST['_wcmb_vendor_notices_url']);    	
    } 	
  }
  
  /**
	 * Register vendor_notices post type
	 *
	 * @access public
	 * @return void
	*/
  function register_post_type() {
		global $WCMb;
		if ( post_type_exists($this->post_type) ) return;
		$labels = array(
			'name' => _x( 'Announcements', 'post type general name' , 'MB-multivendor' ),
			'singular_name' => _x( 'Announcements', 'post type singular name' , 'MB-multivendor' ),
			'add_new' => _x( 'Add New', $this->post_type , 'MB-multivendor' ),
			'add_new_item' => sprintf( __( 'Add New %s' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor' ) ),
			'edit_item' => sprintf( __( 'Edit %s' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor') ),
			'new_item' => sprintf( __( 'New %s' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor') ),
			'all_items' => sprintf( __( 'All %s' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor' ) ),
			'view_item' => sprintf( __( 'View %s' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor' ) ),
			'search_items' => sprintf( __( 'Search %a' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor' ) ),
			'not_found' =>  sprintf( __( 'No %s found' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor' ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash' , 'MB-multivendor' ), __( 'Announcements' , 'MB-multivendor' ) ),
			'parent_item_colon' => '',
			'all_items' => __( 'Announcements' , 'MB-multivendor' ),
			'menu_name' => __( 'Announcements' , 'MB-multivendor' )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => current_user_can( 'manage_woocommerce' ) ? 'wcmb' : false,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor' ),
			'menu_position' => 10,
			//'menu_icon' => $WCMb->plugin_url.'/assets/images/dualcube.png'
		);		
		register_post_type( $this->post_type, $args );
	}  
	
	
}
