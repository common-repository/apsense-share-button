<?php
/*
Plugin Name: Share Button designed for APSense
Plugin URI: http://www.apsense.com/tools.html
Description: Add simple social sharing buttons to your articles. Your visitors will be able to easily share your content on the global business social network - APSense.com. 
Version: 3.6
Author: APSense.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: share_button_apsense
Domain Path: /languages
*/

$apsense_plugin_url = plugins_url() . '/apsense-share-button';
add_filter( 'the_content', 'apsense_the_content', 10 );
add_filter( 'get_the_excerpt', 'apsense_get_the_excerpt', 1);
add_filter( 'the_excerpt', 'apsense_the_content', 100 );

load_plugin_textdomain( 'apsense-share-button', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if ( is_admin() ) {
	add_filter('plugin_action_links', 'apsense_plugin_action_links', 10, 2);
	add_action('admin_menu', 'apsense_admin_menu');
}

add_action( 'admin_head', 'apsense_admin_head' );

if ( function_exists('register_activation_hook') )
	register_activation_hook( __FILE__, 'apsense_plugin_activation' );

if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook( __FILE__, 'apsense_plugin_uninstall' );

function apsense_plugin_uninstall() {
	delete_option( 'apsense_custom' );
	delete_option( 'apsense_button_type' );
	delete_option( 'apsense_button_position' );
	delete_option( 'apsense_uid' );
}

function apsense_plugin_activation() {

}

function apsense_admin_menu() {
	add_options_page('', '', 'manage_options', __FILE__, 'apsense_admin_settings_show', '', 6);
}

function apsense_admin_head() {

}   

function apsense_get_the_excerpt($content) {
	$content = str_ireplace('[apsense_hide]', '', $content);
	$content = str_ireplace('[apsense]', '', $content);
	return $content;
}

function apsense_admin_settings_show() {
	global $apsense_plugin_url;
	
	$apsense_share_image = __('Share', 'share_button_apsense');
	$apsense_excerpts = __('Excerpts', 'share_button_apsense');
	$apsense_feeds = __('Feeds', 'share_button_apsense');	
	
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' , 'share_button_apsense') );
	}
	
	/* save settings */
	if ( @$_POST[ 'button_type' ] != '' ) {	
		apsense_admin_settings_save();
	}
		
	echo '<div class="wrap" style="padding-bottom:50px;"><div class="icon32" id="icon-users"></div>';
	echo '<h2>'. __('Share Buttons for APSense.com', 'share_button_apsense').'</h2>';
	echo '<form name="apsense_settings_form" method="post" action="">'; 	
	
	echo '<div style="display:block;padding:20px; margin-right:10px; margin-left:20px; margin-top:0px;">';
	echo '<div id="tips" style="background: #EEE; padding: 10px 10px 10px 10px; margin-top:30px; border-radius:10px ">';
	echo '<p><b>' . __('Shortcodes', 'share_button_apsense') . '</b></p>';
	echo '<p>Use <b>[apsense_hide]</b> anywhere in post\'s text to hide buttons for specific post.</p>';
	echo '<p>Use <b>[apsense]</b> anywhere in post\'s text to show buttons for specific post at custom position.</p>';
	echo '</div>';	
	
	echo '</div>';
	
	
	$start = '<!-- apsense Share Buttons - http://www.apsense.com/share/ -->';
	$end = '<!-- apsense Share Buttons -->';
	$class_name = 'apsense_pop';
	$alt = 'Share';
	$class_url = ' href="http://www.apsense.com/share/" ';	
	$style = 'padding-left:5px; padding-top:5px; padding-bottom:5px; margin:0';	
	$checked = 'checked="checked"';

	?>
	
	<input type="hidden" name="code" value="" />	
	<br/>
	<table border="0">
	<tr>
		<td style="width:150px;" valign="top"><?php _e('Button type', 'share_button_apsense'); ?>
		</td>
		<?php
			$apsense_button_type = get_option( 'apsense_button_type', 'share_button');
			$apsense_uid = get_option( 'apsense_uid', '');

			$checked = ' checked="checked" ';
			$apsense_share_button_checked = '';
			$apsense_share_icon_checked = '';
			switch ( $apsense_button_type ) {
				case 'share_button': 	$apsense_share_button_checked = $checked; break;
				case 'share_icon': 	$apsense_share_icon_checked = $checked; break;
				default: $apsense_share_button_checked = $checked;
			}			
		?>		
		<td height="100"><input type="radio" name="button_type" value="share_button" <?php echo $apsense_share_button_checked; ?>  /> Share Button<br/>
		<img src="http://www.apsense.com/public/btn_share.png" /><br/><br/>
		<input type="radio" name="button_type" value="share_icon" <?php echo $apsense_share_icon_checked; ?> /> Share Icon <br/>
		<img src="http://www.apsense.com/public/apsense_16.png" /><br/><br/>	
		</td>	
	</tr>
	<tr>
		<td style="width:150px;" valign="top"><?php _e('Button position', 'share_button_apsense'); ?></td>
		<?php
			$button_position = get_option( 'apsense_button_position', 'below' );
			$checked = ' checked="checked" ';
			$apsense_below_checked = '';
			$apsense_above_checked = '';
			switch ( $button_position ) {
				case 'below': 	$apsense_below_checked = $checked; break;
				case 'above' :  $apsense_above_checked = $checked; break;
				default: $apsense_below_checked = $checked;
			}			
		?>
		<td>
		<input type="radio" name="apsense_button_position" value="above" <?php echo $apsense_above_checked; ?> /> <?php _e('Above the post', 'share_button_apsense'); ?><br/>
		<input type="radio" name="apsense_button_position" value="below" <?php echo $apsense_below_checked; ?> /> <?php _e('Below the post', 'share_button_apsense'); ?><br/><br/>
		</td>
	</tr>	
	<tr>
		<td style="width:150px;" valign="top"><?php _e('Your APSense Username (optional)', 'share_apsense_uid'); ?></td>
		<td>
		<input type="text" size="20" name="apsense_uid" value="<?php echo $apsense_uid; ?>" /><br />
		Add your referral link into share button. (<a href="http://www.apsense.com/" target="_blank">Create a free account on APSense</a>)
		</td>
	</tr>
	<tr>
		<td style="width:100px;">
		&nbsp;
		</td>
		<td>
		<br/><br/><input class="button-primary" name="submit" type="submit"  value="<?php _e('Save Settings', 'share_button_apsense'); ?>" />
		</td>
	</tr>
	</table>
	
	</form>
</div>
	
<?php	
	
}

function apsense_admin_settings_save() {

	global $apsense_plugin_url;	
	update_option( 'apsense_custom', '1' );

	if ( @$_POST[ 'button_type' ] != '' )
		$post = true;
	else
		$post = false;	
			
	/* save button style */
	if ( $post ) {
		$button_type = @$_POST[ 'button_type' ];
		update_option( 'apsense_button_type', $button_type );		
	} else {
		$button_type = get_option ( 'button_type', 'share_button');
	}				
	
	/* save button position */
	if ( $post ) {
		$apsense_button_position = @$_POST[ 'apsense_button_position' ];	
		update_option( 'apsense_button_position', $apsense_button_position );
	}
	else {
		$apsense_button_position = get_option( 'apsense_button_position', 'below' );		
	}	

	/* save apsense uid */
	if ( $post ) {
		$apsense_uid = @trim($_POST[ 'apsense_uid' ]);
		update_option( 'apsense_uid', $apsense_uid );		
	} else {
		$button_type = get_option ( 'apsense_uid', '');
	}				
	

}

function apsense_the_content( $content ) {

	global $apsense_plugin_url, $wp_version;
	
	/* Do now show share buttons when [apsense_hide] is used */
	if ( stripos($content, '[apsense_hide]') !== false ) {
		$content = str_ireplace('[apsense_hide]', '', $content);
		$content = str_ireplace('[apsense]', '', $content);
		return $content;
	}

	/* Do not show share buttons in feeds */
	if (is_single() != true) {
		$content = str_ireplace('[apsense_hide]', '', $content);
		$content = str_ireplace('[apsense]', '', $content);		
		return $content;
	}
	

	$post_url = get_permalink($GLOBALS['post']->ID);
	$post_title = $GLOBALS['post']->post_title;

    $apsense_uid = get_option( 'apsense_uid', '' );
	if ($apsense_uid != "")
		$ref = "ref=$apsense_uid&";

	/* default code */
	$code_icon = "<a href='http://www.apsense.com/share' onclick=\"window.open('http://www.apsense.com/share?".$ref."url=' + encodeURIComponent(window.location),'apshare'); return false\" title='Share on APSense'> <img src='http://www.apsense.com/public/apsense_16.png' alt='Share on APSense' border='0' /> </a>";
	$code_button = "<a href='http://www.apsense.com/share' onclick=\"window.open('http://www.apsense.com/share?".$ref."url=' + encodeURIComponent(window.location),'apshare'); return false\" title='Share on APSense'> <img src='http://www.apsense.com/public/btn_share.png' alt='Share on APSense' border='0' /> </a>";
		
	$button_type = get_option( 'apsense_button_type', 'share_button' );
	
	switch ( $button_type ) {
		case 'share_button': 
			$code = $code_button;
			break;
		case 'share_icon':
			$code = $code_icon;
			break;
	}
	   
    $position = get_option( 'apsense_button_position', 'below' );
	
	if ( stripos($content, '[apsense]') !== false) {
		$new_content = str_ireplace('[apsense]', '<div style="margin:10px 0px">' . $code . '</div>', $content);
	}
	else {
		if ( $position == 'below' ) {
			$new_content = $content . '<div style="margin:10px 0px">' . $code . '</div>';   
    	}
		else {
			$new_content = '<div style="margin:10px 0px">' . $code . '</div>' . $content;
		}
	}	
		
	return $new_content;
}  

function apsense_plugin_action_links( $links, $file ) {
    static $this_plugin;
    if ( !$this_plugin ) {
        $this_plugin = plugin_basename( __FILE__ );
    }
 
    // check to make sure we are on the correct plugin
    if ( $file == $this_plugin ) {
         $settings_link = '<a href="options-general.php?page=apsense-share-button/apsense-share-button.php">' . __('Settings', 'share_button_apsense') . '</a>';
        array_unshift( $links, $settings_link );
    }
 
    return $links;
}

?>