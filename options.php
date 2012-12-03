<?php

/* For all copyright, version, etc. information, please see add_email_signature.php */

# http://codex.wordpress.org/Creating_Options_Pages

if (!defined ('ABSPATH')) die ('No direct access allowed');

# Hook to display an options page for our plugin in the admin menu
add_action('admin_menu', 'add_email_signature_options_menu');
add_action('admin_init', 'add_email_signature_options_init' );
register_activation_hook('add_email_signature', 'add_email_signature_options_setdefaults');

function add_email_signature_options_menu() {
	# http://codex.wordpress.org/Function_Reference/add_options_page
	add_options_page('Add Email Signature', 'Add Email Signature', 'manage_options', 'add_email_signature', 'add_email_signature_options_printpage');
}

# Registered under admin_init
function add_email_signature_options_init(){
	# Register a new set of options, named add_email_signature_options, stored in the database entry add_email_signature_options

	# Register a new set of options, named add_email_signature_options_dboptions, stored in the database entry add_email_signature_dboptions
	register_setting( 'add_email_signature_options', 'add_email_signature_options' , 'add_email_signature_options_validate' );
	add_settings_section ( 'add_email_signature_options', 'Add Email Signature Options', 'add_email_signature_options_header' , 'add_email_signature');
	add_settings_field ( 'add_email_signature_options_signature', 'Signature', 'add_email_signature_options_signature', 'add_email_signature' , 'add_email_signature_options' );

}

function add_email_signature_options_setdefaults() {
	$tmp = get_option('add_email_signature_options');
	if (!is_array($tmp)) {
		$arr = array( "signature" => "" );
		update_option('add_email_signature_options', $arr);
	}
}

# Various functions for outputing each of the options fields
function add_email_signature_options_signature() {

$options = get_option('add_email_signature_options');
?>
<textarea rows="14" cols="70" name="add_email_signature_options[signature]" /><?php echo $options['signature']; ?></textarea>
<?php
}

function add_email_signature_options_header() {
	settings_errors();
}

# This function is registered via register_setting. It validates the input (which means, we attempt a database connection). It is intended to return sanitised output, and can optionally call add_settings_error to whinge about anything faulty
function add_email_signature_options_validate($input) {

	# See: http://codex.wordpress.org/Function_Reference/add_settings_error

// 	add_settings_error( "add_email_signature_options", "add_email_signature_options_nodb", "Whinge, whinge", "error" );

	return $input;
}


# This is the function outputing the HTML for our options page
function add_email_signature_options_printpage() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	$ws_advert = add_email_signature_wordshell_random_advert(1);
	$pver = ADD_EMAIL_SIGNATURE_VERSION;

	echo <<<ENDHERE
	<div class="wrap">
	<h2>Add Email Signature Options</h2>

<div class="updated fade" style="font-size:140%; padding:14px;">${ws_advert}</div>

	<p>Version: <strong>$pver</strong><br />
	Maintained by <strong>David Anderson</strong> (<a href="http://david.dw-perspective.org.uk">Homepage</a> | <a href="http://wordshell.net">WordShell - WordPress command line</a> | <a href="http://david.dw-perspective.org.uk/donate">Donate</a> | <a href="http://wordpress.org/extend/plugins/add-email-signature/faq/">FAQs</a>)
	</p>

	<p style="max-width: 600px;">Enter the signature which you wish to use here. Do not add the &quot;--&quot;, as it is assumed. If you wish to temporarily turn off the adding of signatures, then just disable the plugin (your settings will not be deleted).</p>
	<form method="post" action="options.php">
ENDHERE;
	settings_fields('add_email_signature_options');
	do_settings_sections('add_email_signature');

	echo <<<ENDHERE
	    <p class="submit">
	    <input type="submit" class="button-primary" value="Save Changes" />
	    </p>
	</form>

<hr>
	<h2>WordPress recommendations</h2>

<p style="max-width: 600px;"><strong><a href="http://wordpress.org/extend/plugins/updraftplus">UpdraftPlus (backup plugin)</strong></a><br>Automated, scheduled WordPress backups via email, FTP, Amazon S3 or Google Drive
</p>

<p style="max-width: 600px;"><strong><a href="http://www.simbahosting.co.uk">WordPress maintenance and hosting</strong></a><br>We recommend Simba Hosting - 1-click WordPress installer and other expert services available - since 2007</p>

<p style="max-width: 600px;"><strong><a href="http://wordpress.org/extend/plugins/no-weak-passwords">No Weak Passwords (plugin)</strong></a><br>This essential plugin forbids users to use any password from a list of known weak passwords which hackers presently use (gathered by statistical analysis of site break-ins).</p>

<p style="max-width: 600px;"><strong><a href="http://wordpress.org/extend/plugins/use-administrator-password">Use Administrator Password (plugin)</strong></a><br>When installed, this plugin allows any administrator to use their own password to log in to any valid user's account. Very useful for logging in as another user without having to change passwords back and forth.</p>

</div>



ENDHERE;
}

function add_email_signature_wordshell_random_advert($urls) {
	$url_start = ($urls) ? '<a href="http://wordshell.net">' : "";
	$url_end = ($urls) ? '</a>' : " (www.wordshell.net)";
	if (rand(0,1) == 0) {
		return "Like automating WordPress operations? Use the CLI? ${url_start}You will love WordShell${url_end} - saves time and money fast.";
	} else {
		return "${url_start}Check out WordShell${url_end} - manage WordPress from the command line - huge time-saver";
	}
}

function add_email_signature_action_links($links, $file) {

	if ( $file == ADDEMAILSIG_SLUG."/".ADDEMAILSIG_SLUG.".php" ){
		array_unshift( $links, 
			'<a href="options-general.php?page=add_email_signature">Settings</a>',
			'<a href="http://david.dw-perspective.org.uk/donate">Donate</a>'
		);
	}

	return $links;

}
add_filter( 'plugin_action_links', 'add_email_signature_action_links', 10, 2 );

?>
