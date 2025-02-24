<div class="transaction-details">
	<div class="total-balance-wrap">
		<h4><?php _e('Withdrawable Balance', 'MB-multivendor');?></h4>
		<div class="wcmb_dashboard_widget_total_transaction"><?php echo wc_price($total_amount);  ?></div>
	</div>
    <?php if($transaction_display_array) : ?>
	<ul class="transaction-list">
	<?php 
		foreach ($transaction_display_array as $key => $value) {
                    //print_r($value);
                    
			echo "<li><p>".$value['transaction_date']."<span class='order-id'>#".$key."</span></p><span class='pull-right'>".wc_price($value['total_amount'])."</span></li>";	
		}?>
	</ul>
    <?php endif; ?>
</div>
