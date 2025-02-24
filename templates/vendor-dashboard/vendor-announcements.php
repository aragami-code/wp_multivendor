<?php
/**

 */
global $WCMb;
?>
<div class="col-md-12">
<input type="hidden" name="wcmb_msg_tab_to_be_refrash" id="wcmb_msg_tab_to_be_refrash" value="" />
<input type="hidden" name="wcmb_msg_tab_to_be_refrash2" id="wcmb_msg_tab_to_be_refrash2" value="" />
<input type="hidden" name="wcmb_msg_tab_to_be_refrash3" id="wcmb_msg_tab_to_be_refrash3" value="" />
<div id = "tabs-1">
    <ul class="wcmb_msg_tab_nav">
        <li data-element="_all"><a href = "#wcmb_msg_tab_1"><?php _e('All', 'MB-multivendor'); ?></a></li>
        <li data-element="_read"><a href = "#wcmb_msg_tab_2"><?php _e('Read', 'MB-multivendor'); ?></a></li>
        <li data-element="_unread" ><a href = "#wcmb_msg_tab_3"><?php _e('Unread', 'MB-multivendor'); ?></a></li>
        <li data-element="_archive"><a href = "#wcmb_msg_tab_4"><?php _e('Trash', 'MB-multivendor'); ?></a></li>
    </ul>
    <!--...................... start tab1 .......................... -->
    <div id = "wcmb_msg_tab_1" data-element="_all">
        <div class="msg_container" >			
            <?php
            if(isset($vendor_announcements['all'])){
                $all = $vendor_announcements['all'];
            }else{
                $all = array();
            }
            //show all messages
            $WCMb->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-all.php',array('posts_array'=>$all));
            ?>			
        </div>
    </div>
    <!--...................... end of tab1 .......................... -->
    <!--...................... start tab2 .......................... -->
    <div id = "wcmb_msg_tab_2" data-element="_read">
        <div class="msg_container" >							
            <?php
            if(isset($vendor_announcements['read'])){
                $read = $vendor_announcements['read'];
            }else{
                $read = array();
            }
            //show read messages
            $WCMb->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-read.php',array('posts_array'=>$read));
            ?>			
        </div>
    </div>
    <!--...................... end of tab2 .......................... -->
    <!--...................... start tab3 .......................... -->
    <div id = "wcmb_msg_tab_3" data-element="_unread">
        <div class="msg_container" >				
            <?php
            if(isset($vendor_announcements['unread'])){
                $unread = $vendor_announcements['unread'];
            }else{
                $unread = array();
            }
            //show unread messages
            $WCMb->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-unread.php',array('posts_array'=>$unread));
            ?>				
        </div>
    </div>
    <!--...................... end of tab3 .......................... -->
    <!--...................... start tab4 .......................... -->
    <div id = "wcmb_msg_tab_4" data-element="_archive">
        <div class="msg_container">				
            <?php
            if(isset($vendor_announcements['deleted'])){
                $deleted = $vendor_announcements['deleted'];
            }else{
                $deleted = array();
            }
            //show unread messages
            $WCMb->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements-archive.php',array('posts_array'=>$deleted));
            ?>				
        </div>
    </div>
    <!--...................... end of tab4 .......................... -->
</div>
</div>