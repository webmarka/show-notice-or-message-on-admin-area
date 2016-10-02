<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/*
Plugin Name: Show notice or message on admin area (dismissable)
Plugin URI: http://marc-antoine-minville.com
Description: Show urgent or normal dismissable notice on admin dashboard area
Author: webmarka
Author URI: http://marc-antoine-minville.com
Version: 12.0
*/


/**
* Include plugin styles.
*/ 
function sna_show_notice_styles() {
	$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)).'/css';
	wp_enqueue_style( 'sna_notice', $pluginfolder.'/sna_notice.css' );
}
add_action( 'admin_init', 'sna_show_notice_styles' );


/**
 * Register the JavaScript for the admin area.
 *
 * @since    12.0
 */
function sna_enqueue_scripts() {
	
		wp_enqueue_script( 'show-notice-or-message-on-admin-area', plugin_dir_url( __FILE__ ) . 'js/common.js', array( 'jquery' ), '12.0', false );
}
add_action( 'admin_init', 'sna_enqueue_scripts' );


// Handle ajax admin notices.
add_action('wp_ajax_sna_dismiss_notice_ajax', 'sna_dismiss_notice_ajax');

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
	
	/* If save urgent notice button clicked stores details in database */
	if(isset($_REQUEST['sna_urgent_notice']))
	{
	   $urgnt_text = $_REQUEST['sna_urgent_notice_text'];
	   update_option('sna_urgent_notice',$urgnt_text);
	   /* Remove user's preferences. */
	   sna_dismiss_notice_reset('urgent');
	}
	/* Removing urgent message  */
	if(isset($_REQUEST['remove_ur_notice']))
	{
	   update_option('sna_urgent_notice','');
	   /* Remove user's preferences. */
	   sna_dismiss_notice_reset('urgent');
	}
	
  /* If save normal notice button clicked stores details in database */
	if(isset($_REQUEST['sna_normal_notice']))
	{
	   $nrml_text = $_REQUEST['sna_normal_notice_text'];
	   update_option('sna_normal_notice',$nrml_text);
	   /* Remove user's preferences. */
	   sna_dismiss_notice_reset('normal');
	}
	/* Removing normal message  */
	if(isset($_REQUEST['remove_nr_notice']))
	{
	   update_option('sna_normal_notice','');
	   /* Remove user's preferences. */
	   sna_dismiss_notice_reset('normal');
	}
	
?>
	<div class="error_notice_area">
	  <form name="error_notice" action="" method="post">
	    <label>Urgent Notice:</label> <textarea class="tex_ar_err" name="sna_urgent_notice_text" rows="3" ><?php echo stripslashes(get_option('sna_urgent_notice'));?></textarea><br>
		<input type="submit" name="sna_urgent_notice" value="Save" class="notice_sub"> 
		<input type="submit" name="remove_ur_notice" value="Remove Notice" class="notice_remv"> 
      
	 </form>
   </div>
	
	<div class="sna_normal_notice_area">
	  <form name="sna_normal_notice" action="" method="post">
	    <label> Normal Notice: </label><textarea class="tex_ar_nrml" name="sna_normal_notice_text" rows="3"><?php echo stripslashes(get_option('sna_normal_notice'));?></textarea><br>
		<input type="submit" name="sna_normal_notice" value="Save" class="notice_sub">
		<input type="submit" name="remove_nr_notice" value="Remove Notice" class="notice_remv"> 
	 </form>
   </div>
   
   
<?php } // Here plugin function End
 
 /* Get the normal and urgent message values from database and 
    showing in admin area
 */

	
function show_admin_notice(){
	
	global $current_user;
  
  $user_id = $current_user->ID;
  
	/* Dismiss urgent notice for current user. */
	if(isset($_REQUEST['sna_urgent_notice_ignore']) == '1'){
		sna_dismiss_notice('urgent');
	}
  /* Urgent Notice : Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta($user_id, 'sna_urgent_notice_ignore') ) {
		
		$urgent_notices = stripslashes(get_option('sna_urgent_notice'));
		
		if(!empty($urgent_notices)){
			echo '<div class="error notice is-dismissible sna-dismiss-notice" id="message" data-notice-name="urgent"><p>';
			//printf(__('%1$s | <a href="%2$s">Hide Notice</a>'), $urgent_notices, '?sna_urgent_notice_ignore=1');
			printf(__('%1$s'), $urgent_notices);
			echo '</p></div>';	
		}
	}

	/* Dismiss normal notice for current user. */
	if(isset($_REQUEST['sna_normal_notice_ignore']) == '1'){
		sna_dismiss_notice('normal');
	}
	/* Normal Notice : Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta($user_id, 'sna_normal_notice_ignore') ) {
		
		$normal_notices = stripslashes(get_option('sna_normal_notice'));
		
		if(!empty($normal_notices)){
			echo '<div class="updated notice is-dismissible sna-dismiss-notice" id="message" data-notice-name="normal"><p>';
			//printf(__('%1$s | <a href="%2$s">Hide Notice</a>'), $normal_notices, '?sna_normal_notice_ignore=1');
			printf(__('%1$s'), $normal_notices);
			echo '</p></div>';	
		}
	}
	
}

/* Accepted notices values. */
function sna_notices_accept_values() {
	
	return array("normal", "urgent");
}

/* Accepted notices values. */
function sna_get_notice_dismiss_string($notice) {
	
	return 'sna_'.$notice.'_notice_ignore';
}



/**
 * Dismiss an admin notice through ajax.
 */
function sna_dismiss_notice_ajax(){
		
		if(!isset($_REQUEST['notice']))
				die('Notice name expected as "notice" parameter.');
		
		sna_dismiss_notice($_REQUEST['notice']);
}

/**
 * Dismiss an admin notice.
 */
function sna_dismiss_notice($notice) {
	
	global $current_user;
	
  $user_id = $current_user->ID;
	if (!in_array($notice, sna_notices_accept_values())) return false;
	$meta_key = sna_get_notice_dismiss_string($notice);
	update_user_meta($user_id, $meta_key, 'on');
}


/**
 * Dismissed admin notices reset.
 */
function sna_dismiss_notice_reset($notice) {
	
	global $wpdb;
	
	/* Minimal security check... */
	if (!in_array($notice, sna_notices_accept_values())) return false;
	$meta_key = sna_get_notice_dismiss_string($notice);
	
	$wpdb->query( 
			$wpdb->prepare( 
					"
					DELETE FROM $wpdb->usermeta
					WHERE meta_key = %s
					",
					$meta_key
					)
	);
	
	return true;
}

/* Removing Notices from this plugin */
if(isset($_REQUEST['page']) != 'sna-a-dashboard'){
	add_action('admin_notices', 'show_admin_notice');
}

/* By default URGENT and NORMAL message adding to database on plugin activation */
class sna_ad_dashboard {
    static function sna_default_text() {
    
		$urgent_notices = get_option('sna_urgent_notice');
		$normal_notices = get_option('sna_normal_notice');
			/* If empty will show the default message */
		if(empty($urgent_notices)){	 
			update_option('sna_urgent_notice', 'This example <b>URGENT message</b> from <b>show notice on admin area</b> plugin. you can remove or edit from settings--> Notice on Dashboard plugin');
		}
	
		/* If empty will show the default message */
		if(empty($normal_notices)){	
			update_option('sna_normal_notice', 'This example <b>NORMAL message</b> from <b>show notice on admin area</b> plugin. you can remove or edit from settings--> Notice on Dashboard plugin');
		}
		
	}
}
register_activation_hook( __FILE__, array( 'sna_ad_dashboard', 'sna_default_text' ) );	
?>
