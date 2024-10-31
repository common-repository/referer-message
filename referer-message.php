<?php
/*
Plugin Name: Referer Message
Plugin URI: http://www.bigbluedev.com/plugins/referer-message/
Description: Enables you to display a welcome/welcome-back message to your visitors depending on the site they have visited from, or a defult message if they've come direct.  Uses cookies to determine whether its a new visitor or not.
Author: Michael Vigor
Author URI: http://www.bigbluedev.com
Credits: This plugin is a new take on the Welcome Visitor! Reloaded plugin by Alaeddin (http://www.alhome.net/index.php/greet-new-visitors-a-wordpress-plugin/).  This is turn was largely based upon the original Welcome Visitor! plugin by Kaf (http://guff.szub.net/2006/04/12/welcome-visitor/) which was released under the GNU General Public License.
Version: 1.0

Referer Message is released under the GNU General Public License
(GPL) http://www.gnu.org/licenses/gpl.txt

*/
function referer_message_admin() {
	add_options_page('Referer Message', 'Referer Message', 8,  'referer-message/referer-message-editor.php');
}
add_action('admin_menu', 'referer_message_admin');

function referer_message() {
	
	$options = get_option('referer_message_contents');
	$referer = match_referer($options['referers']);
	
	if ($referer) {
		global $user_ID, $user_identity, $user_email, $user_url;
		//Initialize the variables

		if (is_new_visitor())
			$output = $referer['welcome'];
		else
			$output = $referer['welcomeback'];
	
		get_currentuserinfo();
		//Get the values
	
		if ($user_ID){
			//User is logged in!
			$cur_name=$user_identity;
			$cur_mail=$user_email;
			$cur_url= ($user_url=="" || $user_url=="http://") ? get_bloginfo("home") : $user_url;
		}
		else if ($_COOKIE["comment_author_" . COOKIEHASH] != "") {
			//User is not logged in, but we've got its cookies
			$cur_name= $_COOKIE["comment_author_" . COOKIEHASH];
			$cur_mail= $_COOKIE["comment_author_email_" . COOKIEHASH];
			$cur_url= $_COOKIE["comment_author_url_" . COOKIEHASH];
		}
	
		if($cur_name != ""){
			//We've got the visitor's data!
			
			$mybloglog_IMG = "http://pub.mybloglog.com/coiserv.php?href=" . $cur_url . "&n=". $cur_name;
			$gravatar_IMG = "http://www.gravatar.com/avatar.php?gravatar_id=". md5($cur_mail) . "&size=48&";
			//This and the following lines are to show the mybloglog avatar
		
			$output = str_replace('$cur_name', htmlspecialchars($cur_name),$output);
			$output = str_replace('$cur_avatar', '<img src="'.$mybloglog_IMG.'" style="float:right; margin-left:3px;" onload="if (this.width != 48) {this.parentNode.href = "'.$gravatar_URL.'"; this.parentNode.title = "'.$gravatar_TITLE.'"; this.src="'.$gravatar_IMG.'"; this.onload=void(null);}" alt="Avatar"/>',$output);
 
		}
		else
		{
			$output = str_replace('$cur_name', 'guest',$output);
			$output = str_replace('$cur_avatar', '',$output);
		}

	} else {
		//no referer found
		//check whether default message should be displayed
		
		if ($options['display_default'] == 'Y') {
			if (is_new_visitor()) {
				$output = stripslashes($options['default']['welcome']);
			} else {
				$output = stripslashes($options['default']['welcomeback']);
			}
		} else {
			$output = '';
		}
	}
	echo $output;
}

function match_referer($referers) {
	if (!empty($referers)) {
		foreach ($referers as $key => $referer) {  
			if (strpos($_SERVER['HTTP_REFERER'], $referer['match_on']) != false) { 
				return $referer;
			}
		}	
	} else {
		return false;
	}
}

function is_new_visitor()
{	
	global $visits;
	
	if (!is_admin()) {
		if (isset($_COOKIE['visits']))
			$visits = $_COOKIE['visits'] + 1;
		else
			$visits = 1;
			
		$url = parse_url(get_option('home'));
		setcookie('visits', $visits, time()+60*60*24*365, $url['path'] . '/');
	} else {
		return false;
	}	
	return $visits == 1;
}

// We're putting the plugin's functions in one big function we then
// call at 'plugins_loaded' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_referer_message_init() {

    // Check to see required Widget API functions are defined...
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return; // ...and if not, exit gracefully from the script.

    // This function prints the sidebar widget--the cool stuff!
    function widget_referer_message_load($args) {

        // $args is an array of strings which help your widget
        // conform to the active theme: before_widget, before_title,
        // after_widget, and after_title are the array keys.
        extract($args);

        // Collect our widget's options, or define their defaults.
        $options = get_option('referer_message_contents');
        $welcome_content = empty($options['default_message']['welcome']) ? 'Welcome' : $options['default_message']['welcome'];
        $welcomeback_content = empty($options['default_message']['welcomeback']) ? 'Welcome back!' : $options['default_message']['welcomeback'];

         // It's important to use the $before_widget, $before_title,
         // $after_title and $after_widget variables in your output.
	ob_start();
        echo $before_widget;
        echo $before_title . $title . $after_title;
        echo referer_message();
        echo $after_widget;
	ob_end_flush();
    }


    // This registers the widget. About time.
    register_sidebar_widget('Referer Message', 'widget_referer_message_load');

}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('plugins_loaded', 'widget_referer_message_init');

