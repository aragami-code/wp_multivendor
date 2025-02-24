<?php
/**

 */
 
class WCMb_University {
	private $post_type;
  public $dir;
  public $file;
  
  public function __construct() {
    $this->post_type = 'wcmb_university';
    $this->register_post_type();		
  }
  
  /**
	 * Register university post type
	 *
	 * @access public
	 * @return void
	*/
  function register_post_type() {
		global $WCMb;
		if ( post_type_exists($this->post_type) ) return;
		$labels = array(
			'name' => _x( 'Knowledgebase', 'post type general name' , 'MB-multivendor' ),
			'singular_name' => _x( 'Knowledgebase', 'post type singular name' , 'MB-multivendor' ),
			'add_new' => _x( 'Add New', $this->post_type , 'MB-multivendor' ),
			'add_new_item' => sprintf( __( 'Add New %s' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor' ) ),
			'edit_item' => sprintf( __( 'Edit %s' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor') ),
			'new_item' => sprintf( __( 'New %s' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor') ),
			'all_items' => sprintf( __( 'All %s' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor' ) ),
			'view_item' => sprintf( __( 'View %s' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor' ) ),
			'search_items' => sprintf( __( 'Search %a' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor' ) ),
			'not_found' =>  sprintf( __( 'No %s found' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor' ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash' , 'MB-multivendor' ), __( 'Knowledgebase' , 'MB-multivendor' ) ),
			'parent_item_colon' => '',
			'all_items' => __( 'Knowledgebase' , 'MB-multivendor' ),
			'menu_name' => __( 'Knowledgebase' , 'MB-multivendor' )
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
			'menu_position' => 15,
			//'menu_icon' => $WCMb->plugin_url.'/assets/images/dualcube.png'
		);		
		register_post_type( $this->post_type, $args );
	}  
	
	
}
