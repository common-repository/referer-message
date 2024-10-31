<?php
/*
Script Name: Referer Message admin page
Script URI: http://www.bigbluedev.com/plugins/referer-message/

/*
Administration text/label variables. These may be
edited for "localization" purposes.
*/
$no_access_msg = 'You do not have permission to access this page.';
$headline = 'Welcome new and returning visitors';
$update_msg = 'Changes to messages saved';
$update_btn_text = 'Update Messages';
$welcome_label = 'Welcome message';
$welcomeback_label = 'Welcome-back message';
$welcome_content = 'This is your welcome message. You will probably want to edit it.';
$welcomeback_content = 'This is your welcome-back message. You will probably want to edit it.';
/*
--------------------------------------------------------------------
*/

global $user_level;
if ($user_level < 8)
	die($no_access_msg);

$options = get_option('welcome_visitor_reloaded');

if(empty($options)) {
	$welcome_content = $options['welcome_content'] = $welcome_content;
	$welcomeback_content = $options['welcomeback_content'] = $welcomeback_content;
	update_option('welcome_visitor_reloaded', $options);
} else {
	if(!isset($_POST['update'])) {
		$welcome_content = $options['welcome_content'];
		$welcomeback_content = $options['welcomeback_content'];
	} else {
		$welcome_content = $options['welcome_content'] = $_POST['welcome_content'];
		$welcomeback_content = $options['welcomeback_content'] = $_POST['welcomeback_content'];
		update_option('welcome_visitor_reloaded', $options);
?>
	<div id="updated" class="updated fade">
	<p><strong><?php echo $update_msg; ?></strong></p>
	</div>
<?php
	}
}
$welcome_content = stripslashes(htmlentities($welcome_content));
$welcomeback_content = stripslashes(htmlentities($welcomeback_content));
?>
	<div class="wrap">
	<h2><?php echo $headline; ?></h2>
	<h3>Tips</h3>
	<ul>
		<li><abbr title="HyperText Markup Language">HTML</abbr> is supported in either message</li>
		<li>You can use the following placeholders (without the quotes) in either message:
			<ul><li>"<strong>$cur_name</strong>" - The name of the currently logged-in user or the last name they used to post a comment.</li>
			<li>"<strong>$cur_avatar</strong>" - The MyBlogLog avatar of the currently logged-in user or the one associated with his last comment. <br /> <small>Note: By default the avatar is floated to the right. Currently, you can change the alignment and margin of the avatar by editing <strong>welcome-visitor-reloaded.php</strong></small></li></ul>
		</li>
	</ul>
	<form name="szub_welcome" method="post" id="post">
		<div id="poststuff">
			<fieldset id="welcomediv" class="options">
			<legend><?php echo $welcome_label; ?></legend>
				<div><textarea rows="11" cols="110" name="welcome_content" id="welcome_content"><?php echo $welcome_content; ?></textarea></div>
			</fieldset>
			<br />
			<fieldset id="welcomebackdiv" class="options">
			<legend><?php echo $welcomeback_label; ?></legend>
				<div><textarea rows="11" cols="110" name="welcomeback_content" id="welcomeback_content"><?php echo $welcomeback_content; ?></textarea></div>
			</fieldset>

			<p class="submit">
			<input type="submit" name="update" value="<?php echo $update_btn_text ;?>" title="update"/>
			</p>
			</div>

		</div>

	</div>
