<?php 
/*
Plugin Name: Oviex Contact Form
Plugin URI: http://oviex.com
Description: Oviex Contact form is a simple contact form which works out of the box. Use shortcode on posts or pages to generate Oviex Contact Form.
Version: 1.0
Author: Umang Goyal
Author URI: http://oviex.com
*/

$db_version = '1.0';
	
	add_action('wp_enqueue_scripts', 'style_front_data');
	
	function style_front_data(){
		wp_enqueue_style( 'style_css', trailingslashit(plugin_dir_url(__FILE__)).'assets/style.css'); 
	}
	
	// active and deactive hooks with function
		
	add_action( 'wp_ajax_ocf_form', 'ocf_form' );
	add_action( 'wp_ajax_nopriv_ocf_form', 'ocf_form' );
	
	function ocf_form()
	{		
		if(isset($_POST['ocf_squeeze_form']) && trim($_POST['ocf_squeeze_form']) == 'k4342'){
			$data = array();
			if(isset($_POST['name'])){
				$name = sanitize_text_field(trim($_POST['name']));
			}
			if(isset($_POST['last_name'])){
				$lastname = sanitize_text_field(trim($_POST['last_name']));
			}
			if(isset($_POST['email'])){
					$email = sanitize_email(trim($_POST['email']));
			}
			if(isset($_POST['phone'])){
			
				$phone = intval(trim($_POST['phone']));
			}
			if(isset($_POST['ip'])){
			
				if($_POST['ip'] === filter_var($_POST['ip'], FILTER_VALIDATE_IP))
				{
					$ip = $_POST['ip'];
				}
			}
			if(isset($_POST['comment'])){
				$comment = esc_attr($_POST['comment']);
			}
			
			$data['name'] = $name;
			$data['last_name'] = $lastname;
			$data['email'] = $email;
			$data['phone'] = $phone;
			$data['comment'] = $comment;
			$data['ip'] = $ip;
			
			/* If anyone wants the data they can add their functions to this hook*/
			do_action('save_ocf',$data);
			
			$default_email = get_option('admin_email');
			
			$to = get_option('ocf_to_email',$default_email);
			$from = get_option('ocf_from_email',$default_email);
			$from_name = get_option('ocf_from_name','Wordpress');
			$subject = get_option('ocf_form_subject','New contact form submitted on your website.');
			$headers = 'From: '. $from_name .' <'. $from.'>';
			$message ="Name: " .$name. "\n";
			$message .="Last Name: ".$lastname." \n";
			$message .="Email: ".$email." \n";
			$message .="Phone: ".$phone." \n";
			$message .="Ip: ".$ip." \n";
			$message .="Message: ".esc_html($comment)." \n\n\n\n";
				
			$message .="This email was sent from ".site_url();
			
				if(wp_mail($to, $subject, $message, $headers )){
						echo 1;
						wp_die();
				}
				else{
					echo 0;
					wp_die();
				}
		}
	}

	
function Oviex_general_info() 
	{		
		?>
		<div class="wrap">
			<h2>Oviex Contact Form</h2>
			<br />
			<br />
			<p>
				Welcome to Oviex Contact Form.<br />
				Oviex Contact Form works out of the box. Just use the shortcode mentioned below to generate simple contact form on any post/page.<br /><br />
			</P>
			<p>
				Shortcode to generate Oviex Contact Form</br>
				<code>
					[ocf_contact_form]
				</code>
			</p>
			<p>
				<strong>Tip:</strong> To save these submitted entries, Please use our Oviex Contact Form to DB Plugin.<br /><br />
				To view all of our plugins please <a href="http://oviex.com">click here</a>
				</br></br>
				Enjoy!!!
			</p>
		</div>
		<?php
	}
		add_shortcode( 'ocf_contact_form', 'ocf_shortcode' );
		function ocf_shortcode() 
		{
			?>
			<div id="mail-status" style="color:#f05227; font-size:18px; font-weight:400;"></div>
			<div id="ocf-wrapper">
				<form action="" method="post" accept-charset="utf-8" class="subform" id="subscription" name="lp" action="<?php $_SERVER['PHP_SELF']; ?>">
					<fieldset>
							<label>Name*</label><br />
							<input type="text"  name="ocf_name" id="ocf_name" style="color:#000;" placeholder="Name*" class="ocf_first_name_input">
							<input type="text"  name="ocf_lastname" id="ocf_lastname" placeholder="Last Name*" style="color:#000;" class="ocf_last_name_input">
							<label>Email Address*</label>
							<input type="email" style="color:#000;" name="ocf_email" id="ocf_email" class="ocf_email_input">	<br>	
							<label>Phone Number</label>
							<input type="text"  style="color:#000;" name="ocf_phone" id="ocf_phone" class="ocf_tel_input"><br>
							<label>Comment/Message</label>
							<textarea name="ocf_comment" class="ocf_email_input" id="ocf_comment"></textarea><br />
							<input type="hidden" name="ocf_squeeze_form" id="ocf_squeeze_form" value="k4342">
							<input type="hidden" id="ip" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>"><br><br>
							
							<div class="submit-container">
								<input class="submit-button btn button ocf_submit_input" type="submit" name="submit" value="SUBMIT">
							</div><br>
					</fieldset>
						
				</form>
			</div>
			<script>
				jQuery("#subscription").submit(function() 
				{
					var url = "#";
					var x=document.forms["lp"]["ocf_name"].value;
					var y=document.forms["lp"]["ocf_lastname"].value;
					var z=document.forms["lp"]["ocf_email"].value;

					if (x==null || x=="") { alert("Please enter your first name."); document.forms["lp"]["ocf_name"].focus(); return false; }
					if (y==null || y=="") { alert("Please enter your surname."); document.forms["lp"]["ocf_lastname"].focus(); return false; }
					if (z==null || z=="") { alert("Please enter an e-mail address."); document.forms["lp"]["ocf_email"].focus(); return false; }

					var $name  = jQuery('#ocf_name').val();
					var $last_name  = jQuery('#ocf_lastname').val();
					var $email  = jQuery('#ocf_email').val();
					var $phone  = jQuery('#ocf_phone').val();
					var $comment  = jQuery('#ocf_comment').val();
					var $ip  = jQuery('#ip').val();
					var $ocf_squeeze_form  = jQuery('#ocf_squeeze_form').val();
					var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';
					var success_message = '<?php echo get_option('ocf_form_success','Thank you for contacting us.');
					?>';
					var error_message = '<?php echo get_option('ocf_form_error','Sorry there was a problem submitting your form. Please try again later');
					?>';
					
				jQuery.ajax(
					{
					   contentType: "application/x-www-form-urlencoded;charset=utf-8",
					   type: "POST",
					   cache: false,
					   url: ajaxurl,
					   data: { name: $name, last_name: $last_name, email: $email, phone: $phone, ip: $ip, comment : $comment, ocf_squeeze_form: $ocf_squeeze_form , action: 'ocf_form'},
					   success: function(res)
						{
							if(res == 1 || res == '1' )
							{
								
								jQuery("#mail-status").html(success_message);
								jQuery("#subscription").hide();
								//alert("You will soon receive an e-mail with the confirmation link");
							}
							else
							{
								alert(error_message);
							}
					    }
					});
					return false;
				}); 
			</script>
			
			<?php 		
			
		}
		
	
function ocf_settings(){
		if(isset($_POST['ocf_option_submit'])){
			if (!isset( $_POST['ocf_save_options_nonce'] ) || ! wp_verify_nonce( $_POST['ocf_save_options_nonce'], 'ocf_save_options' )) {

				print 'Sorry, your nonce did not verify.';
				exit;

			} 
			else{
				if(isset($_POST['ocf_from_email'])){
					
					update_option('ocf_from_email',sanitize_email($_POST['ocf_from_email']));
				}
				if(isset($_POST['ocf_from_name'])){
					update_option('ocf_from_name',sanitize_text_field($_POST['ocf_from_name']));
				}
				if(isset($_POST['ocf_form_subject'])){
					update_option('ocf_form_subject',sanitize_text_field($_POST['ocf_form_subject']));
				}
				if(isset($_POST['ocf_to_email'])){
					update_option('ocf_to_email',sanitize_email($_POST['ocf_to_email']));
				}
				if(isset($_POST['ocf_form_error'])){
					update_option('ocf_form_error',sanitize_text_field($_POST['ocf_form_error']));
				}
				if(isset($_POST['ocf_form_success'])){
					update_option('ocf_form_success',sanitize_text_field($_POST['ocf_form_success']));
				}
				
				$message =  '<div class="updated notice notice-success is-dismissible below-h2" id="message"><p> Options Saved.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';	
			}
		}
		?>
<style>
td {
padding:10px;
}
</style>
			<div class="option-wrapper">
			<h2>Oviex Contact Form Options </h2><br />
			<?php
			
				if(!empty($message))
					echo $message;
			?>
			<form action="" method="post">
				<table>
					<tr>
						<td><h4>Email Options</h4></td>
					</tr>
					<tr>
						<td>To Email:</td>
						<td><input type="email" name="ocf_to_email" value="<?php echo get_option('ocf_to_email','demo@yourdomain.com'); ?>"/></td>
					</tr>
					<tr>
						<td>From Email:</td>
						<td><input type="email" name="ocf_from_email" value="<?php echo get_option('ocf_from_email','demo@yourdomain.com'); ?>"/></td>
					</tr>
					<tr>
						<td>From Name</td>
						<td><input type="text" name="ocf_from_name" value="<?php echo get_option('ocf_from_name','Wordpress'); ?>" /></td>
					</tr>
					<tr>
						<td>Subject </td>
						<td><input type="text" name="ocf_form_subject" value="<?php echo get_option('ocf_form_subject','New contact form submitted on your website'); ?>" /></td>
					</tr>
					<tr>
						<td>
							Success Message
						</td>
						<td><input type="text" name="ocf_form_success" value="<?php echo get_option('ocf_form_success','Thank you for contacting us. We will be in touch'); ?>" /></td>
					</tr>
					<tr>
						<td>
							Error Message
						</td>
						<td><input type="text" name="ocf_form_error" value="<?php echo get_option('ocf_form_error','Sorry there was an error submitting your form. Please try again later.'); ?>" /></td>
					</tr>
					<tr>
						<td>Save Changes</td> 
						<td>
						<?php
							wp_nonce_field( 'ocf_save_options', 'ocf_save_options_nonce' );
						?>
						<input type="submit" name="ocf_option_submit" class="btn" value="Save Settings" /></td>
					</tr>
				</table>
			</form>
			</div>

<?php		
}
		
add_action( 'admin_menu', 'ocf_submenu_page' );
	
function ocf_submenu_page() {
		add_menu_page('Oviex Contact Form', 'Oviex Contact Form', 'administrator', 'Oviex-contact-us-form', 'Oviex_general_info', 'dashicons-admin-generic');
		add_submenu_page('Oviex-contact-us-form', 'Settings - OB Contact Form', 'Settings', 'administrator', 'settings', 'ocf_settings'); 
	}
?>
