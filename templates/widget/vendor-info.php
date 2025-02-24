<?php
/**

 */

global $WCMb;
?>

<h4><?php echo $vendor->page_title; ?> </h4>
<?php 
	$description = strip_tags($vendor->description);
	if (strlen($description) > 250) {
		// truncate string
		$stringCut = substr($description, 0, 250);

		// make sure it ends in a word so assassinate doesn't become ass...
		$description = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
	}
?>
<p><?php echo $description; ?> </p>
<p>
	<a href="<?php echo esc_attr( $vendor->permalink ); ?>" title="<?php echo sprintf( __( 'More Products from %1$s', 'MB-multivendor' ), $vendor->page_title ); ?>">
		<?php echo sprintf( __( 'More Products from %1$s', 'MB-multivendor' ), $vendor->page_title );?>
	</a>
</p>