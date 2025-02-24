<?php
/**

 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
global $WCMb;
if(isset($reviews_lists) && count($reviews_lists) > 0) {
	foreach($reviews_lists as $reviews_list) {
		
		$WCMb->template->get_template( 'review/review.php', array('comment' => $reviews_list, 'vendor_term_id'=> $vendor_term_id));
	}	
}?>
