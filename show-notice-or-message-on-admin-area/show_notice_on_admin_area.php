<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/*
Plugin Name: Show notice or message on admin area
Plugin URI: https://venugopalphp.wordpress.com
Description: Show urgent or normal notice on admin dashboard area
Author: Venugopal
Author URI: https://venugopalphp.wordpress.com
Version: 2.0
*/


 /**
 * Including plugin  styles
 */ 
  add_action( 'admin_init', 'sna_show_notice_styles' );
	function sna_show_notice_styles() {
	$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)).'/css';
	wp_enqueue_style( 'sna_notice', $pluginfolder.'/sna_notice.css' );
	
}

/* Initializing  plugin function */
add_action('admin_menu','sna_show_notice_area');

function sna_show_notice_area()
{
	add_options_page('Notice on Dashboard', 'Notice on Dashboard','manage_options','sna-a-dashboard','sna_show_notice_dashboard');
		 
}

/* Calling plugin function */
function sna_show_notice_dashboard()
{
	echo "<h2>Welcome to Show notice on Admin Dashboard plugin</h2>";
	
	/* If save button clicked stores details in database */
	if(isset($_REQUEST['urgnt_notice']))
	{
	   $urgnt_text = $_REQUEST['notice_urgt'];
	   update_option('urgnt_notice',$urgnt_text);
	}
	 /* Removing urgent message  */
	if(isset($_REQUEST['remove_ur_notice']))
	{
	   update_option('urgnt_notice','');
	}
	
   /* If save normal notic button clicked stores details in database */

	if(isset($_REQUEST['nrml_notice']))
	{
	   $nrml_text = $_REQUEST['notice_nrml'];
	   update_option('nrml_notice',$nrml_text);
	}
	
	/* Removing normal message  */
	if(isset($_REQUEST['remove_nr_notice']))
	{
	    update_option('nrml_notice','');
	}
	

	
?>
	<div class="error_notice_area">
	  <form name="error_notice" action="" method="post">
	    <label>Urgent Notice:</label> <textarea class="tex_ar_err" name="notice_urgt" rows="3" ><?php echo get_option('urgnt_notice');?></textarea><br>
		<input type="submit" name="urgnt_notice" value="Save" class="notice_sub"> 
		<input type="submit" name="remove_ur_notice" value="Remove Notice" class="notice_remv"> 
      
	 </form>
   </div>
	
	<div class="nrml_notice_area">
	  <form name="nrml_notice" action="" method="post">
	    <label> Normal Notice: </label><textarea class="tex_ar_nrml" name="notice_nrml" rows="3"><?php echo get_option('nrml_notice');?></textarea><br>
		<input type="submit" name="nrml_notice" value="Save" class="notice_sub">
		<input type="submit" name="remove_nr_notice" value="Remove Notice" class="notice_remv"> 
	 </form>
   </div>
   
   
<?php } // Here plugin function End
 
 /* Get the normal and urgent message values from database and 
    showing in admin area
 */

	
function show_admin_notice(){
	
	
$urngent_notices = get_option('urgnt_notice');
$norml_notices = get_option('nrml_notice');
	
	if(!empty($urngent_notices)){
	echo '<div class="error notice is-dismissible" id="message"><p>'.$urngent_notices.'</p></div>';	
	}
	
	if(!empty($norml_notices)){
	echo '<div class="updated notice is-dismissible" id="message"><p>'.$norml_notices.'</p></div>';	
	}
}  
	 /* Removing Notices from this plugin */
	if(isset($_REQUEST['page']) != 'sna-a-dashboard'){
	add_action('admin_notices', 'show_admin_notice');
	}


/* By default URGENT and NORMAL message adding to database on plugin activation */
 class sna_ad_dashboard {
     static function sna_default_text() {
		 
    
	$urngent_notices = get_option('urgnt_notice');
	$norml_notices = get_option('nrml_notice');
    /* If empty will show the default message */
	if(empty($urngent_notices)){	 
	update_option('urgnt_notice', 'This example <b>URGENT message</b> from <b>show notice on admin area</b> plugin. you can remove or edit from settings--> Notice on Dashboard plugin');
	}
	
	/* If empty will show the default message */
   if(empty($norml_notices)){	
	update_option('nrml_notice', 'This example <b>NORMAL message</b> from <b>show notice on admin area</b> plugin. you can remove or edit from settings--> Notice on Dashboard plugin');
   }
		
     }
}
register_activation_hook( __FILE__, array( 'sna_ad_dashboard', 'sna_default_text' ) );	
?>