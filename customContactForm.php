<?
/*
Plugin Name: Base & MailChimp Custom Contact Form
Plugin URI: http://www.davidwhitehouse.co.uk/blog/base-crm-contact-form/
Description: A Custom contact form with integration into Base & MailChimp, built for D.W. by <a href="http://www.stormgate.co.uk">StormGate</a>
Version: 1.2.2
Author: StormGate
Author URI: http://www.stormgate.co.uk
*/

#} Hooks

    #} Install/uninstall
    register_activation_hook(__FILE__,'bmc_customcontact__install');
    register_deactivation_hook(__FILE__,'bmc_customcontact__uninstall');

    #} general
	add_action('init', 'bmc_customcontact__init');
    add_action('admin_menu', 'bmc_customcontact__admin_menu'); #} Initialise Admin menu


#} Initial Vars
global $bmc_customcontact_db_version;
$bmc_customcontact_db_version 				= "1.0";
$bmc_customcontact_version 					= "1.0";
$bmc_customcontact_perma					= "bmccc";

#} Page slugs
global $bmc_customcontact_slugs;
$bmc_customcontact_slugs['config'] 		= $bmc_customcontact_perma."-plugin-config";


#} Install function
function bmc_customcontact__install(){

	global $bmc_customcontact_version, $bmc_customcontact_db_version;	#} Req


	#} Save initial options
	add_option("bmc_customcontact_db_version", 					$bmc_customcontact_db_version);
    add_option("bmc_customcontact_version",						$bmc_customcontact_version);
	add_option("bmc_customcontact_reg", 						"1");

	#} Default Options
	add_option('bmc_customcontact_mailchimp_apikey',			'');
	add_option('bmc_customcontact_mailchimp_apiListID',			'');
    add_option('bmc_customcontact_email',						get_bloginfo('admin_email'));
    add_option('bmc_customcontact_base_username',				'');
    add_option('bmc_customcontact_base_userpass',				'');
    add_option('bmc_customcontact_base_dealName',				'contactForm');
    add_option('bmc_customcontact_redirUrl',					'');
    add_option('bmc_customcontact_redirUrl',					'');

	# Additions for config v1.2.2
    add_option('bmc_customcontact_creatorlink',					1);
    add_option('bmc_customcontact_fullname_s',					1);
    add_option('bmc_customcontact_fullname_r',					1);
    add_option('bmc_customcontact_company_s',					1);
    add_option('bmc_customcontact_company_r',					1);
    add_option('bmc_customcontact_phone_s',						1);
    add_option('bmc_customcontact_phone_r',						1);
    add_option('bmc_customcontact_mobile_s',					1);
    add_option('bmc_customcontact_mobile_r',					1);
    add_option('bmc_customcontact_email_s',						1);
    add_option('bmc_customcontact_email_r',						1);
    add_option('bmc_customcontact_comment_s',					1);
    add_option('bmc_customcontact_comment_r',					1);

}


#} Uninstall
function bmc_customcontact__uninstall(){

	#} Removes initial settings, leaves config intact for upgrades.
	#} Removes these for security.
    delete_option('bmc_customcontact_email');
    delete_option('bmc_customcontact_base_username');
    delete_option('bmc_customcontact_base_userpass');

}

#} Actual form output (Shortcode)
function bmc_customcontact_showform( $atts ){

	#} Prefilled & Detect Failed save:
	global $bcmPreFill,$bcmErrorFields;

	# Identify what to show
    $bmcConfig['bmc_customcontact_creatorlink'] = 				get_option('bmc_customcontact_creatorlink');				#Default: 1
    $bmcConfig['bmc_customcontact_fullname_s'] = 				get_option('bmc_customcontact_fullname_s');					#Default: 1
    $bmcConfig['bmc_customcontact_company_s'] = 				get_option('bmc_customcontact_company_s');					#Default: 1
    $bmcConfig['bmc_customcontact_phone_s'] = 					get_option('bmc_customcontact_phone_s');					#Default: 1
    $bmcConfig['bmc_customcontact_mobile_s'] = 					get_option('bmc_customcontact_mobile_s');					#Default: 1
    $bmcConfig['bmc_customcontact_email_s'] = 					get_option('bmc_customcontact_email_s');					#Default: 1
    $bmcConfig['bmc_customcontact_comment_s'] = 				get_option('bmc_customcontact_comment_s');					#Default: 1

	#} Styles output inline, not ideal!
	$formHTML = '<div id="bcmContactForm">
    <form method="post">
		<input type="hidden" name="bcmToken" value="'.md5(time()+23232).'" />';

		if ($bmcConfig['bmc_customcontact_fullname_s'] == "1") {
			$formHTML .= '<div class="bcmContactField">Your Full Name<div><input type="text" name="bcmName" id="bcmName" value="'.$bcmPreFill['bcmName'].'" />';
			if (isset($bcmErrorFields['bcmName'])) $formHTML .= ' <span>This field is required</span>';
			$formHTML .= '</div></div>';
		}
		if ($bmcConfig['bmc_customcontact_company_s'] == "1") {
			$formHTML .= '<div class="bcmContactField">Your Company<div><input type="text" name="bcmCompany" id="bcmCompany" value="'.$bcmPreFill['bcmCompany'].'" /></div></div>';
			if (isset($bcmErrorFields['bcmCompany'])) $formHTML .= '<div class="bcmContactField" style="margin-bottom:40px;"><div><span>A valid company is required</span></div></div>';
		}

		if ($bmcConfig['bmc_customcontact_phone_s'] == "1") {
			$formHTML .= '<div class="bcmContactField">Your Phone Number<div><input type="text" name="bcmPhone" id="bcmPhone" value="'.$bcmPreFill['bcmPhone'].'" /></div></div>';
			if (isset($bcmErrorFields['bcmPhone'])) $formHTML .= '<div class="bcmContactField" style="margin-bottom:40px;"><div><span>A valid phone is required</span></div></div>';
		}

		if ($bmcConfig['bmc_customcontact_mobile_s'] == "1") {
			$formHTML .= '<div class="bcmContactField">Your Mobile Number<div><input type="text" name="bcmMobile" id="bcmMobile" value="'.$bcmPreFill['bcmMobile'].'" /></div></div>';
			if (isset($bcmErrorFields['bcmMobile'])) $formHTML .= '<div class="bcmContactField" style="margin-bottom:40px;"><div><span>A valid mobile is required</span></div></div>';
		}

		if ($bmcConfig['bmc_customcontact_email_s'] == "1")  {
			$formHTML .= '<div class="bcmContactField">Your Email Address<div><input type="text" name="bcmEmail" id="bcmEmail" value="'.$bcmPreFill['bcmEmail'].'" /></div></div>';
			if (isset($bcmErrorFields['bcmEmail'])) $formHTML .= '<div class="bcmContactField" style="margin-bottom:40px;"><div><span>A valid email is required</span></div></div>';
		}


		if ($bmcConfig['bmc_customcontact_comment_s'] == "1")  {

			$formHTML .= '<div class="bcmContactField">Tell us about your needs';

					if (isset($bcmErrorFields['bcmMessage'])) $formHTML .= ' <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>A message is required</span></div>';

			$formHTML .= '<br /><textarea name="bcmMessage" id="bcmMessage">'.$bcmPreFill['bcmTextarea'].'</textarea></div>';

		}

$formHTML .= '
        <div class="bcmContactField"><div><input type="checkbox" name="bcmSubscribe" id="bcmSubscribe" value="1" ';

		if ($bcmPreFill['bcmSubscribe'] == 1) $formHTML .= 'checked="checked" ';

$formHTML .= '/></div> Join Newsletter</div>
        <div style="width:520px;text-align:right;"><input type="submit" name="bcmSubmit" id="bcmSubmit" value="Contact Me" /></div>';
if ($bmcConfig['bmc_customcontact_creatorlink'] == "1")
	$formHTML .= '<div id="bcmccreators"><a href="http://www.davidwhitehouse.co.uk/out/base" target="_blank">Base CRM</a> Plugin created by <a href="http://www.davidwhitehouse.co.uk" title="David Whitehouse" target="_blank">David Whitehouse</a> and <a href="http://www.stormgate.co.uk" title="StormGate" target="_blank">StormGate</a></div>';

$formHTML .= '
    </form>
</div>';

	return $formHTML;

}
add_shortcode( 'bmccustomcontact', 'bmc_customcontact_showform' );



#} Initialisation - enqueueing scripts/styles
function bmc_customcontact__init(){

  	global $bmc_customcontact_slugs;

	#} Admin only
	if (is_admin() && $_GET['page'] == $bmc_customcontact_slugs['config']) {

		#} Admin CSS
		wp_enqueue_style('wPluginCSSADM', plugins_url('/css/wPluginAdmin.css',__FILE__) );

		#} Admin JS
		wp_enqueue_script('wPluginJSAdmin', plugins_url('/js/wPluginAdmin.js',__FILE__) );

	}

	if (!empty($_POST['bcmToken'])){

		#} Contact Form Posted

		#} Validate request
		$validRequest = true;

			#} Get Vars
			$posted = array();
			$posted['bcmName'] 			= sanitize_text_field($_POST['bcmName']);
			$posted['bcmCompany'] 		= sanitize_text_field($_POST['bcmCompany']);
			if (validate_numeric($posted['bcmPhone']))
				$posted['bcmPhone'] 	= $_POST['bcmPhone'];
			else
				$posted['bcmPhone'] 	=  preg_replace('/[^0-9 ]/i', '', $_POST['bcmPhone']);
			if (validate_numeric($posted['bcmMobile']))
				$posted['bcmMobile'] 	= $_POST['bcmMobile'];
			else
				$posted['bcmMobile'] 	=  preg_replace('/[^0-9 ]/i', '', $_POST['bcmMobile']);
			$posted['bcmEmail'] 		= is_email($_POST['bcmEmail']);
			$posted['bcmMessage'] 		= sanitize_text_field($_POST['bcmMessage']);
			$posted['bcmSubscribe'] 	= intval($_POST['bcmSubscribe']);


			#} Identify which is required from options
			$bmcConfig['bmc_customcontact_fullname_r'] = 				get_option('bmc_customcontact_fullname_r');					#Default: 1
			$bmcConfig['bmc_customcontact_company_r'] = 				get_option('bmc_customcontact_company_r');					#Default: 1
			$bmcConfig['bmc_customcontact_phone_r'] = 					get_option('bmc_customcontact_phone_r');					#Default: 1
			$bmcConfig['bmc_customcontact_mobile_r'] = 					get_option('bmc_customcontact_mobile_r');					#Default: 1
			$bmcConfig['bmc_customcontact_email_r'] = 					get_option('bmc_customcontact_email_r');					#Default: 1
			$bmcConfig['bmc_customcontact_comment_r'] = 				get_option('bmc_customcontact_comment_r');					#Default: 1

			#} Check through requireds
			global $bcmErrorFields; $bcmErrorFields = array(); $validRequest = true;
			if ($bmcConfig['bmc_customcontact_fullname_r'] == "1") 	if (empty($posted['bcmName'])) 		{ $validRequest = false; $bcmErrorFields['bcmName'] = 1; }
			if ($bmcConfig['bmc_customcontact_company_r'] == "1") 	if (empty($posted['bcmCompany'])) 	{ $validRequest = false; $bcmErrorFields['bcmCompany'] = 1; }
			if ($bmcConfig['bmc_customcontact_phone_r'] == "1") 	if (empty($posted['bcmPhone'])) 	{ $validRequest = false; $bcmErrorFields['bcmPhone'] = 1; }
			if ($bmcConfig['bmc_customcontact_mobile_r'] == "1") 	if (empty($posted['bcmMobile'])) 	{ $validRequest = false; $bcmErrorFields['bcmMobile'] = 1; }
			if ($bmcConfig['bmc_customcontact_email_r'] == "1") 	if (empty($posted['bcmEmail'])) 	{ $validRequest = false; $bcmErrorFields['bcmEmail'] = 1; }
			if ($bmcConfig['bmc_customcontact_comment_r'] == "1") 	if (empty($posted['bcmMessage'])) 	{ $validRequest = false; $bcmErrorFields['bcmMessage'] = 1; }

			# If a fail.
			if (!$validRequest) {
				#} Record vals for refill
				global $bcmPreFill; $bcmPreFill = $posted;
			}


		if ($validRequest){

			#} Get Options
			$bmcConfig = array();
			$bmcConfig['bmc_customcontact_mailchimp_apikey'] = 			get_option('bmc_customcontact_mailchimp_apikey');			#Default: ""
			$bmcConfig['bmc_customcontact_mailchimp_apiListID'] = 		get_option('bmc_customcontact_mailchimp_apiListID');		#Default: ""
			$bmcConfig['bmc_customcontact_email'] = 					get_option('bmc_customcontact_email');						#Default: ""
			$bmcConfig['bmc_customcontact_base_username'] = 			get_option('bmc_customcontact_base_username');				#Default: ""
			$bmcConfig['bmc_customcontact_base_userpass'] = 			get_option('bmc_customcontact_base_userpass');				#Default: ""
			$bmcConfig['bmc_customcontact_base_dealName'] = 			get_option('bmc_customcontact_base_dealName');				#Default: "contactForm"
			$bmcConfig['bmc_customcontact_mailchimp_apiListID'] = 		get_option('bmc_customcontact_mailchimp_apiListID');		#Default: ""
			$bmcConfig['bmc_customcontact_redirUrl'] = 					get_option('bmc_customcontact_redirUrl');					#Default: ""

			#} Pretty field names for email etc
			$fieldNames = array(
									'bcmName' => 'Name',
									'bcmCompany' => 'Company',
									'bcmPhone' => 'Phone',
									'bcmMobile' => 'Mobile',
									'bcmEmail' => 'Email',
									'bcmMessage' => 'Message',
									'bcmSubscribe' => 'Subscribe Checkbox'

								);

			#} Try and split name
				$nameParts = explode(' ',$posted['bcmName']);
				if (count($nameParts) == 2){

					$firstName 	= $nameParts[0];
					$lastName 	= $nameParts[1];

				} else {

					if (count($nameParts) == 1){
						$firstName = $posted['bcmName'];
						$lastName = '';
					} else {
						#Not a straight forward split :/ Probably Dr. Joe Blogs or smt
						$lastName 	= array_pop($nameParts);
						$firstName 	= implode(' ',$nameParts);
					}

				}

			#} Added check for mailchimp API Key.
			if (!empty($bmcConfig['bmc_customcontact_mailchimp_apikey'])) {

				#} First save to Mailchimp API if subscriber
				if ($posted['bcmSubscribe'] == 1){

					#} Used mailchimp api class in the end.
					$mailchimpSend = registerMailChimpEmail(
																$bmcConfig['bmc_customcontact_mailchimp_apikey'],
																$bmcConfig['bmc_customcontact_mailchimp_apiListID'],
																$posted['bcmEmail'],
																$firstName,
																$lastName
																#} Other params not avail for mc
															);


					if ($mailchimpSend[0]) {
						$mailchimpStr = 'User was successfully subscribed';
					} else {
						$mailchimpStr = 'There was an error subscribing user:<br />'.$mailchimpSend[1];
					}

				} else $mailchimpStr = 'User did not check subscribe box';

			} else $mailchimpStr = 'Mailchimp Not used (no API key)';

			#} Save to Base (inc google cookie)

				require_once('includes/baseFunctions.php');

				#} Get Google Analytics cookie

				require_once('includes/class.gaparse.php');
				$googleCookie = new GA_Parse($_COOKIE);

				#} Get base auth
				$baseToken = base_getAuth($bmcConfig['bmc_customcontact_base_username'],$bmcConfig['bmc_customcontact_base_userpass']);

				if (!empty($baseToken)){

						#} If company, add it:
						if (!empty($posted['bcmCompany']))
							$companyBaseContactID = base_addCompany($baseToken,$posted['bcmCompany']);
						else
							$companyBaseContactID = '';

						#} Create contact

							#} Last name is required, if theres only a first then put "?"
							if (empty($lastName)) $lastName = '?';

							#} Actual API call
							$contactBaseID = base_addIndividual($baseToken,$lastName,$firstName,$companyBaseContactID,$posted['bcmPhone'],$posted['bcmMobile'],$posted['bcmEmail']);

							#} Add thier comment as a note:
							#if (!empty($contactBaseID) && !empty($posted['bcmMessage'])) $noteID = base_addNoteToContact($baseToken,$contactBaseID,'Message from contact form:'."\r\n".$_POST['bcmMessage']);#SPECIFICALLY sends it un-sanitized.

							#} Add source
							if (!empty($googleCookie->campaign_source))
								$sourceID = base_addOrGetSource($baseToken,$googleCookie->campaign_source.'/'.$googleCookie->campaign_medium);
							else
								$sourceID = 0;

							#} Add deal
							if (!empty($contactBaseID) && !empty($bmcConfig['bmc_customcontact_base_dealName']))
								$dealID = base_addDeal($baseToken,$contactBaseID,$bmcConfig['bmc_customcontact_base_dealName'],$sourceID);
							else
								$dealID = '';

							#} Add thier comment as a note:
							if (!empty($dealID) && !empty($posted['bcmMessage'])) $noteID = base_addNoteToDeal($baseToken,$dealID,'Message from contact form:'."\r\n".$_POST['bcmMessage']); #SPECIFICALLY sends it un-sanitized.



							#} Build baseStr
							$baseStr = '';
							if (!empty($companyBaseContactID)) 	$baseStr .= 'Company record created, ID: '.$companyBaseContactID.'<br />';
							if (!empty($contactBaseID)) 		$baseStr .= 'Contact record created, ID: '.$contactBaseID.'<br />';
							if (!empty($noteID)) 				$baseStr .= 'Note added to contact, ID: '.$noteID.'<br />';
							if (!empty($dealID)) 				$baseStr .= 'Deal record created, ID: '.$dealID.'<br />';
							if (!empty($sourceID)) 				$baseStr .= 'Source record created/found, ID: '.$sourceID.'<br />';



				} else $baseStr = 'There was a problem connecting to Base.';

			#} Email it too?

			$serverFromAddr = get_bloginfo('admin_email');
			if (!empty($bmcConfig['bmc_customcontact_email']) && !empty($serverFromAddr)){

				$to = $bmcConfig['bmc_customcontact_email'];
				$subject = 'Contact via Web Form';
				$headers = "From: ".$serverFromAddr."\r\n";
				$headers .= "Reply-To: ".$serverFromAddr."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				$message = '<html><head><style type="text/css">body, p, td, h2 { font-family: Arial, Helvetica, sans-serif; }</style></head><body><h2>Contact via Web Form</h2>';
				$message .= '<table width="800" border="0">
							  <tr>
								<td width="323" valign="top"><strong>Recieved</strong></td>
								<td width="467">'.date('F j, Y, g:i a',time()).'</td>
							  </tr>
							  <tr>
								<td valign="top"><strong>Subscribed via MailChimp</strong></td>
								<td>'.$mailchimpStr.'</td>
							  </tr>
							  <tr>
								<td valign="top"><strong>Saved to Base</strong></td>
								<td>'.$baseStr.'</td>
							  </tr>
							  <tr>
								<td valign="top"><strong>Contact Form Details:</strong></td>
								<td>&nbsp;</td>
							  </tr>
							  <tr>
								<td>&nbsp;</td>
								<td valign="top">';
				foreach ($posted as $field => $val) {
					$valStr = $val;
					if ($field == 'bcmSubscribe') if ($val == 1) $valStr = 'Checked'; else $valStr = 'Not Checked';
					$message .= '<strong>'.$fieldNames[$field].'</strong>: '.nl2br($valStr).'<br />';
				}

				#} Add Google Analytics Cookie Data.
				if (!empty($googleCookie->campaign_source)) 	$message .= "<strong>Campaign source:</strong> ".$googleCookie->campaign_source."<br />";
				if (!empty($googleCookie->campaign_name)) 		$message .= "<strong>Campaign name:</strong> ".$googleCookie->campaign_name."<br />";
				if (!empty($googleCookie->campaign_medium)) 	$message .= "<strong>Campaign medium:</strong> ".$googleCookie->campaign_medium."<br />";
				if (!empty($googleCookie->campaign_content)) 	$message .= "<strong>Campaign content:</strong> ".$googleCookie->campaign_content."<br />";
				if (!empty($googleCookie->campaign_term)) 		$message .= "<strong>Campaign term:</strong> ".$googleCookie->campaign_term."<br />";

				$message .= '</td>
							  </tr>
							</table></body></html>';

				mail($to, $subject, $message, $headers);

			}

			#} Do redirect.
			header("Location: ".$bmcConfig['bmc_customcontact_redirUrl']);
			exit();

		}

	}



}

#} Added to deal with mailchimp API, expects verified email
function registerMailChimpEmail($apiKey,$listID,$email,$fname='',$lname='',$address='',$city='',$state='',$state='',$zip=''){

	require_once('includes/MCAPI.class.php');
	// grab an API Key from http://admin.mailchimp.com/account/api/
	$api = new MCAPI($apiKey);

	// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
	// Click the "settings" link for the list - the Unique Id is at the bottom of that page.
	#$list_id = $listID;

	// Merge variables are the names of all of the fields your mailing list accepts
	// Ex: first name is by default FNAME
	// You can define the names of each merge variable in Lists > click the desired list > list settings > Merge tags for personalization
	// Pass merge values to the API in an array as follows
	$mergeVars = array();

		if (!empty($fname)) 	$mergeVars['FNAME'] = $fname;
		if (!empty($lname)) 	$mergeVars['LNAME'] = $lname;
		if (!empty($address)) 	$mergeVars['ADDRESS'] = $address;
		if (!empty($city)) 		$mergeVars['CITY'] = $city;
		if (!empty($state)) 	$mergeVars['STATE'] = $state;
		if (!empty($zip)) 		$mergeVars['ZIP'] = $zip;


	if($api->listSubscribe($listID, $email, $mergeVars) === true) {
		// It worked!
		return array(true,'');
	}else{
		// An error ocurred, return error message
		return array(false,$api->errorMessage); #'Error: ' . $api->errorMessage;
	}


}

#} Added for phone no's
function validate_numeric($str)
{
	return preg_match('/^[0-9 ]+$/',$str);
}


#} Add le admin menu
function bmc_customcontact__admin_menu() {

	global $bmc_customcontact_slugs; #} Req

	add_menu_page( 'Base/MailChip Form', 'Base/MailChip', 'manage_options', $bmc_customcontact_slugs['config'], 'bmc_customcontact_pages_configs', plugins_url('i/icon16.png',__FILE__));

}

#} Options page
function bmc_customcontact_pages_configs() {

	global $wpdb, $bmc_customcontact_version;	#} Req

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}


?>
<div id="sgpBody">
    <div class="wrap">
	    <h2>Base & MailChimp Contact Form Config v<?php echo $bmc_customcontact_version; ?></h2>
    </div>

    <p id="sgpDesc">Here you can set the configuration options for your Base & MailChimp Contact Form Plugin.  To use the plugin simply place the following shortcode in a page or a post: [bmccustomcontact]</p>
<?php

			 if ($_GET['save'] == "1"){

                    bmc_customcontact_html_save_config();

			} else {

					bmc_customcontact_html_config();

			}

?>
</div>
<?php
}


#} Options HTML
function bmc_customcontact_html_config(){

	global $wpdb, $bmc_customcontact_db_version, $bmc_customcontact_t, $bmc_customcontact_slugs;	#} Req

	$bmcConfig = array();
    $bmcConfig['bmc_customcontact_mailchimp_apikey'] = 			get_option('bmc_customcontact_mailchimp_apikey');			#Default: ""
	$bmcConfig['bmc_customcontact_mailchimp_apiListID'] = 		get_option('bmc_customcontact_mailchimp_apiListID');		#Default: ""
	$bmcConfig['bmc_customcontact_email'] = 					get_option('bmc_customcontact_email');						#Default: ""
	$bmcConfig['bmc_customcontact_base_username'] = 			get_option('bmc_customcontact_base_username');				#Default: ""
	$bmcConfig['bmc_customcontact_base_userpass'] = 			get_option('bmc_customcontact_base_userpass');				#Default: ""
	$bmcConfig['bmc_customcontact_base_dealName'] = 			get_option('bmc_customcontact_base_dealName');				#Default: "contactForm"
    $bmcConfig['bmc_customcontact_redirUrl'] = 					get_option('bmc_customcontact_redirUrl');					#Default: ""

	# Additions for config v1.2.2
    $bmcConfig['bmc_customcontact_creatorlink'] = 				get_option('bmc_customcontact_creatorlink');				#Default: 1
    $bmcConfig['bmc_customcontact_fullname_s'] = 				get_option('bmc_customcontact_fullname_s');					#Default: 1
    $bmcConfig['bmc_customcontact_fullname_r'] = 				get_option('bmc_customcontact_fullname_r');					#Default: 1
    $bmcConfig['bmc_customcontact_company_s'] = 				get_option('bmc_customcontact_company_s');					#Default: 1
    $bmcConfig['bmc_customcontact_company_r'] = 				get_option('bmc_customcontact_company_r');					#Default: 1
    $bmcConfig['bmc_customcontact_phone_s'] = 					get_option('bmc_customcontact_phone_s');					#Default: 1
    $bmcConfig['bmc_customcontact_phone_r'] = 					get_option('bmc_customcontact_phone_r');					#Default: 1
    $bmcConfig['bmc_customcontact_mobile_s'] = 					get_option('bmc_customcontact_mobile_s');					#Default: 1
    $bmcConfig['bmc_customcontact_mobile_r'] = 					get_option('bmc_customcontact_mobile_r');					#Default: 1
    $bmcConfig['bmc_customcontact_email_s'] = 					get_option('bmc_customcontact_email_s');					#Default: 1
    $bmcConfig['bmc_customcontact_email_r'] = 					get_option('bmc_customcontact_email_r');					#Default: 1
    $bmcConfig['bmc_customcontact_comment_s'] = 				get_option('bmc_customcontact_comment_s');					#Default: 1
    $bmcConfig['bmc_customcontact_comment_r'] = 				get_option('bmc_customcontact_comment_r');					#Default: 1

    ?><form action="?page=<?php echo $bmc_customcontact_slugs['config']; ?>&save=1" method="post">
        <table width="715" border="0" cellpadding="0" cellspacing="0" id="sgpConfig">

            <tr><td class="sgFieldLabelHD" colspan="2">MailChimp Settings</td></tr>

          <tr>
            <td class="sgFieldLabel">MailChimp API Key:</td>
            <td class="sgField">
                <input type="text" name="bmc_customcontact_mailchimp_apikey" value="<?php echo $bmcConfig['bmc_customcontact_mailchimp_apikey']; ?>" />
            </td>
          </tr>
          <tr>
            <td class="sgFieldLabel">MailChimp List ID:</td>
            <td class="sgField">
                <input type="text" name="bmc_customcontact_mailchimp_apiListID" value="<?php echo $bmcConfig['bmc_customcontact_mailchimp_apiListID']; ?>" />
            </td>
          </tr>

            <tr><td class="sgFieldLabelHD" colspan="2">Base Settings</td></tr>

          <tr>
            <td class="sgFieldLabel">Base API Email:</td>
            <td class="sgField">
                <input type="text" name="bmc_customcontact_base_username" value="<?php echo $bmcConfig['bmc_customcontact_base_username']; ?>" />
            </td>
          </tr>
          <tr>
            <td class="sgFieldLabel">Base API Password:</td>
            <td class="sgField">
                <input type="password" name="bmc_customcontact_base_userpass" value="<?php echo $bmcConfig['bmc_customcontact_base_userpass']; ?>" />
            </td>
          </tr>
          <tr>
            <td class="sgFieldLabel">Default Deal Name:</td>
            <td class="sgField">
                <input type="text" name="bmc_customcontact_base_dealName" value="<?php echo $bmcConfig['bmc_customcontact_base_dealName']; ?>" />
            </td>
          </tr>

            <tr><td class="sgFieldLabelHD" colspan="2">General Settings</td></tr>

          <tr>
            <td class="sgFieldLabel">Redirect to:</td>
            <td class="sgField">
                <input type="text" name="bmc_customcontact_redirUrl" value="<?php echo $bmcConfig['bmc_customcontact_redirUrl']; ?>" />
            </td>
          </tr>
          <tr>
            <td class="sgFieldLabel">Email to send lead to:</td>
            <td class="sgField">
                <input type="text" name="bmc_customcontact_email" value="<?php echo $bmcConfig['bmc_customcontact_email']; ?>" />
            </td>
          </tr>


            <tr><td class="sgFieldLabelHD" colspan="2">Field Settings</td></tr>

          <tr>
            <td class="sgFieldLabel">Field Select:</td>
          	<td class="sgField">
            	<table width="300" border="0" cellpadding="0" cellspacing="0" id="sgpConfigFields">
                	<thead>
                    	<tr>
                            <th class="sgFieldLabel">Field</th>
                            <th>Show</th>
                            <th>Required</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<tr>
                        	<td class="sgFieldLabel">Full Name</td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_fullname_s" id="bmc_customcontact_fullname_s" value="1" <?php if ($bmcConfig['bmc_customcontact_fullname_s'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_fullname_r" id="bmc_customcontact_fullname_r" value="1" <?php if ($bmcConfig['bmc_customcontact_fullname_r'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                        </tr>
                    	<tr>
                        	<td class="sgFieldLabel">Company</td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_company_s" id="bmc_customcontact_company_s" value="1" <?php if ($bmcConfig['bmc_customcontact_company_s'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_company_r" id="bmc_customcontact_company_r" value="1" <?php if ($bmcConfig['bmc_customcontact_company_r'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                        </tr>
                    	<tr>
                        	<td class="sgFieldLabel">Phone Number</td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_phone_s" id="bmc_customcontact_phone_s" value="1" <?php if ($bmcConfig['bmc_customcontact_phone_s'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_phone_r" id="bmc_customcontact_phone_r" value="1" <?php if ($bmcConfig['bmc_customcontact_phone_r'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                        </tr>
                    	<tr>
                        	<td class="sgFieldLabel">Mobile Number</td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_mobile_s" id="bmc_customcontact_mobile_s" value="1" <?php if ($bmcConfig['bmc_customcontact_mobile_s'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_mobile_r" id="bmc_customcontact_mobile_r" value="1" <?php if ($bmcConfig['bmc_customcontact_mobile_r'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                        </tr>
                    	<tr>
                        	<td class="sgFieldLabel">Email Address</td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_email_s" id="bmc_customcontact_email_s" value="1" <?php if ($bmcConfig['bmc_customcontact_email_s'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_email_r" id="bmc_customcontact_email_r" value="1" <?php if ($bmcConfig['bmc_customcontact_email_r'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                        </tr>
                    	<tr>
                        	<td class="sgFieldLabel">Comment</td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_comment_s" id="bmc_customcontact_comment_s" value="1" <?php if ($bmcConfig['bmc_customcontact_comment_s'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                            <td>
                            	<input type="checkbox" name="bmc_customcontact_comment_r" id="bmc_customcontact_comment_r" value="1" <?php if ($bmcConfig['bmc_customcontact_comment_r'] == "1") echo ' checked="checked"'; ?> />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
          </tr>

          <tr>
            <td class="sgFieldLabel">Show Creator Link:</td>
            <td class="sgField">
                <input type="checkbox" name="bmc_customcontact_creatorlink" value="1" <?php if ($bmcConfig['bmc_customcontact_creatorlink'] == "1") echo ' checked="checked"'; ?> />
            </td>
          </tr>


          <tr><td class="sgFieldLabelHD" colspan="2">Save Changes</td></tr>
          <tr>
            <td class="sgFieldLabel">&nbsp;</td>
            <td class="sgField" style="padding-bottom:100px;"><input type="submit" value="Save Config" /></td>
          </tr>
          </table></form>
 <? }



#} Save options changes
function bmc_customcontact_html_save_config(){

	global $wpdb, $bmc_customcontact_db_version, $bmc_customcontact_t, $bmc_customcontact_slugs;	#} Req

	$bmcConfig = array();
    $bmcConfig['bmc_customcontact_mailchimp_apikey'] = 			$_POST['bmc_customcontact_mailchimp_apikey'];			#Default: ""
	$bmcConfig['bmc_customcontact_mailchimp_apiListID'] = 		$_POST['bmc_customcontact_mailchimp_apiListID'];		#Default: ""
	$bmcConfig['bmc_customcontact_email'] = 					$_POST['bmc_customcontact_email'];						#Default: ""
	$bmcConfig['bmc_customcontact_base_username'] = 			$_POST['bmc_customcontact_base_username'];				#Default: ""
	$bmcConfig['bmc_customcontact_base_userpass'] = 			$_POST['bmc_customcontact_base_userpass'];				#Default: ""
	$bmcConfig['bmc_customcontact_base_dealName'] = 			$_POST['bmc_customcontact_base_dealName'];				#Default: "contactForm"
    $bmcConfig['bmc_customcontact_redirUrl'] = 					$_POST['bmc_customcontact_redirUrl'];					#Default: ""

	# Additions for config v1.2.2
    $bmcConfig['bmc_customcontact_creatorlink'] = 				$_POST['bmc_customcontact_creatorlink'];				#Default: 1
    $bmcConfig['bmc_customcontact_fullname_s'] = 				$_POST['bmc_customcontact_fullname_s'];					#Default: 1
    $bmcConfig['bmc_customcontact_fullname_r'] = 				$_POST['bmc_customcontact_fullname_r'];					#Default: 1
    $bmcConfig['bmc_customcontact_company_s'] = 				$_POST['bmc_customcontact_company_s'];					#Default: 1
    $bmcConfig['bmc_customcontact_company_r'] = 				$_POST['bmc_customcontact_company_r'];					#Default: 1
    $bmcConfig['bmc_customcontact_phone_s'] = 					$_POST['bmc_customcontact_phone_s'];					#Default: 1
    $bmcConfig['bmc_customcontact_phone_r'] = 					$_POST['bmc_customcontact_phone_r'];					#Default: 1
    $bmcConfig['bmc_customcontact_mobile_s'] = 					$_POST['bmc_customcontact_mobile_s'];					#Default: 1
    $bmcConfig['bmc_customcontact_mobile_r'] = 					$_POST['bmc_customcontact_mobile_r'];					#Default: 1
    $bmcConfig['bmc_customcontact_email_s'] = 					$_POST['bmc_customcontact_email_s'];					#Default: 1
    $bmcConfig['bmc_customcontact_email_r'] = 					$_POST['bmc_customcontact_email_r'];					#Default: 1
    $bmcConfig['bmc_customcontact_comment_s'] = 				$_POST['bmc_customcontact_comment_s'];					#Default: 1
    $bmcConfig['bmc_customcontact_comment_r'] = 				$_POST['bmc_customcontact_comment_r'];					#Default: 1

    #} Save down
	update_option('bmc_customcontact_mailchimp_apikey', 		$bmcConfig['bmc_customcontact_mailchimp_apikey']);
	update_option('bmc_customcontact_mailchimp_apiListID', 		$bmcConfig['bmc_customcontact_mailchimp_apiListID']);
	update_option('bmc_customcontact_email', 					$bmcConfig['bmc_customcontact_email']);
	update_option('bmc_customcontact_base_username', 			$bmcConfig['bmc_customcontact_base_username']);
	update_option('bmc_customcontact_base_userpass', 			$bmcConfig['bmc_customcontact_base_userpass']);
	update_option('bmc_customcontact_base_dealName', 			$bmcConfig['bmc_customcontact_base_dealName']);
	update_option('bmc_customcontact_redirUrl', 				$bmcConfig['bmc_customcontact_redirUrl']);

	# Additions for config v1.2.2
	update_option('bmc_customcontact_creatorlink', 				$bmcConfig['bmc_customcontact_creatorlink']);
	update_option('bmc_customcontact_fullname_s', 				$bmcConfig['bmc_customcontact_fullname_s']);
	update_option('bmc_customcontact_fullname_r', 				$bmcConfig['bmc_customcontact_fullname_r']);
	update_option('bmc_customcontact_company_s', 				$bmcConfig['bmc_customcontact_company_s']);
	update_option('bmc_customcontact_company_r', 				$bmcConfig['bmc_customcontact_company_r']);
	update_option('bmc_customcontact_phone_s', 					$bmcConfig['bmc_customcontact_phone_s']);
	update_option('bmc_customcontact_phone_r', 					$bmcConfig['bmc_customcontact_phone_r']);
	update_option('bmc_customcontact_mobile_s', 				$bmcConfig['bmc_customcontact_mobile_s']);
	update_option('bmc_customcontact_mobile_r', 				$bmcConfig['bmc_customcontact_mobile_r']);
	update_option('bmc_customcontact_email_s', 					$bmcConfig['bmc_customcontact_email_s']);
	update_option('bmc_customcontact_email_r', 					$bmcConfig['bmc_customcontact_email_r']);
	update_option('bmc_customcontact_comment_s', 				$bmcConfig['bmc_customcontact_comment_s']);
	update_option('bmc_customcontact_comment_r', 				$bmcConfig['bmc_customcontact_comment_r']);

    #} Msg
    bmc_customcontact_html_msg(0,"Saved options");

    #} Run standard
    bmc_customcontact_html_config();

}


#} Outputs HTML message
function bmc_customcontact_html_msg($flag,$msg,$includeExclaim=false){

    if ($includeExclaim){ $msg = '<div id="sgExclaim">!</div>'.$msg.''; }

    if ($flag == -1){
		echo '<div class="sgfail wrap">'.$msg.'</div>';
	}
	if ($flag == 0){
		echo '<div class="sgsuccess wrap">'.$msg.'</div>';
	}
	if ($flag == 1){
		echo '<div class="sgwarn wrap">'.$msg.'</div>';
	}
    if ($flag == 2){
        echo '<div class="sginfo wrap">'.$msg.'</div>';
    }
}

?>