<?php
/*
Script Name: Referer Message admin page
Script URI: http://www.bigbluedev.com/plugins/referer-message/

/*
Administration text/label variables. These may be
edited for "localization" purposes.
*/
$no_access_msg = 'You do not have permission to access this page.';
$headline = 'Custom welcome messages for each referer';
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

$options = get_option('referer_message_contents');
if (!isset($options['display_default'])) {
	$options['display_default'] = 'N';
}

if (isset($_POST['delete_referer'])) { 
	unset($options['referers'][$_POST['delete_id']]);
	update_option('referer_message_contents', $options);
}

if (isset($_POST['save_messages'])) {
	$msg_arr['welcome'] = $_POST['welcome_msg'];
	$msg_arr['welcomeback'] = $_POST['welcomeback_msg'];
	if ($_POST['m_id'] != 'default') {
		$msg_arr['name'] = $_POST['referer_name'];
		$msg_arr['match_on'] = $_POST['match_on'];
	} else {
		$options['display_default'] = $_POST['display_default'];
	}
	
	if ($_POST['m_id'] != 'default') {
		$options['referers'][$_POST['m_id']] = $msg_arr;
	} else {
		$options[$_POST['m_id']] = $msg_arr;
	}
	update_option('referer_message_contents', $options);
}

?>

<?php if (isset($_POST['add_referer']) || isset($_POST['edit_referer'])) { ?>
<div class="wrap">
<h2><?php echo $headline; ?></h2>
	<h3><?php echo (isset($_POST['add_referer']) ? 'Add New Referer' : 'Edit Referer');?></h3>
	<?php if (isset($_POST['edit_referer'])) {
			$edit_arr = $options['referers'][$_POST['edit_id']];
	}?>
	<form method="post" name="save_referers">
		<label>Referer Name</label> <input type="text" name="referer_name" value="<?php echo (isset($edit_arr['name']) ? $edit_arr['name'] : '');?>" /> e.g. "Example.com"<br /><br />
		<label>Match Referer On</label> <input type="text" name="match_on" value="<?php echo (isset($edit_arr['match_on']) ? $edit_arr['match_on'] : '');?>" /><br /><br />
		<p>e.g. <em>example.com</em> - match an entire site.<br />
		<em>example.com/subdir/<em> - match an section or subdirectory of a site.<br />
		<em>example.com/subdir/test.php</em> - match a single page.</p>
		<fieldset id="welcomediv" class="options">
			<legend><?php echo $welcome_label; ?></legend>
				<div><textarea rows="11" cols="110" name="welcome_msg"><?php echo (isset($edit_arr['welcome']) ? stripslashes($edit_arr['welcome']) : '');?></textarea></div>
		</fieldset>
		<br />
		<fieldset id="welcomebackdiv" class="options">
			<legend><?php echo $welcomeback_label; ?></legend>
				<div><textarea rows="11" cols="110" name="welcomeback_msg"><?php echo (isset($edit_arr['welcomeback']) ? stripslashes($edit_arr['welcomeback']) : '');?></textarea><br /><br /></div>
		</fieldset>
		<?php if(is_array($options['referers'])){ 
				$next_id = is_int(end(array_keys($options['referers']))) ? end(array_keys($options['referers'])) + 1 : 1;
			} else { $next_id = 1; } ?>
		<input type="hidden" name="m_id" value="<?php echo (isset($_POST['edit_id']) ? $_POST['edit_id'] : $next_id);?>" />
<input type="submit" name="save_messages" value="Save Changes" />
	</form><br /><br />
	<h3 style="clear: left;">Tips</h3>
	<ul>
		<li><abbr title="HyperText Markup Language">HTML</abbr> is supported messages</li>
		<li>You can use the following placeholders (without the quotes):
			<ul><li>"<strong>$cur_name</strong>" - The name of the currently logged-in user or the last name they used to post a comment.</li>
			<li>"<strong>$cur_avatar</strong>" - The MyBlogLog avatar of the currently logged-in user or the one associated with his last comment. <br /> <small>Note: By default the avatar is floated to the right. Currently, you can change the alignment and margin of the avatar by editing <strong>referer-message.php</strong></small></li></ul>
		</li>
	</ul>
</div>

<?php } else { ?>
<div class="wrap">
<h2><?php echo $headline; ?></h2>
	<h3>Tips</h3>
	<ul>
		<li><abbr title="HyperText Markup Language">HTML</abbr> is supported messages</li>
		<li>You can use the following placeholders (without the quotes):
			<ul><li>"<strong>$cur_name</strong>" - The name of the currently logged-in user or the last name they used to post a comment.</li>
			<li>"<strong>$cur_avatar</strong>" - The MyBlogLog avatar of the currently logged-in user or the one associated with his last comment. <br /> <small>Note: By default the avatar is floated to the right. Currently, you can change the alignment and margin of the avatar by editing <strong>referer-message.php</strong></small></li></ul>
		</li>
	</ul>
<h3>Current Referer Messages</h3>

<?php if (!empty($options['referers'])) {
	echo '<table cellpadding="0" cellspacing="0" border="0" width="730" style="width:730px;">';
	foreach ($options['referers'] as $key => $referer) {
		echo '<tr><td>' . $referer['name'] . '</td><td>' . $referer['match_on'] . '</td><td><form method="post" style="display:inline;"><input type="hidden" name="edit_id" value="' . $key . '" /><input type="submit" value="Edit" name="edit_referer" style="float:left;" /></form> <form method="post" style="display:inline;"><input type="hidden" name="delete_id" value="' . $key . '" /><input type="submit" value="Delete" style="float:left;" name="delete_referer" onclick="return confirm(\'Are you sure you want to delete this referer?\');" /></form></td></tr>';
	}
	echo '</table>';
} else {
	echo '<p>There are currently no referer messages.</p>';
}
?>
<form method="post" name="add_referers">
	<p><input type="submit" value="Add New" name="add_referer" /></p>
</form>
<h3 style="clear: left;padding-top:10px;">Default welcome message</h3>
<form method="post" name="default_messages">
<p>Display default welcome if no match found on the referer? <label for="default_on">Yes</label> <input type="radio" value="Y" name="display_default" id="default_on" 
<?php echo ($options['display_default'] == 'Y' ? ' checked="true"' : '');?> /> <label for="default_off">No</label> <input type="radio" value="N" name="display_default" id="default_off" <?php echo ($options['display_default'] == 'N' ? ' checked="true"' : '');?> /></p>

<fieldset id="welcomediv" class="options">
	<legend><?php echo $welcome_label; ?></legend>
		<div><textarea rows="11" cols="110" name="welcome_msg" id="default_welcome"><?php echo stripslashes($options['default']['welcome']); ?></textarea></div>
</fieldset>
<br />
<fieldset id="welcomebackdiv" class="options">
	<legend><?php echo $welcomeback_label; ?></legend>
		<div><textarea rows="11" cols="110" name="welcomeback_msg" id="default_welcomeback"><?php echo stripslashes($options['default']['welcomeback']); ?></textarea></div>
</fieldset>

<input type="hidden" name="m_id" value="default" />
<input type="submit" name="save_messages" value="Save Changes" />
</form>
</div>
<?php } ?>
