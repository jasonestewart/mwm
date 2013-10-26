<?php if ( !is_user_logged_in() ) { auth_redirect(); } ?>                      

<?php                                                                          /**                                                                             * The template for creating new pledges for a user                             *                                                                             Template Name: Password Change Template
 */        

function my_enqueue() {
  wp_enqueue_style('pure-forms');
  wp_enqueue_style('pure-buttons');
  wp_enqueue_script('password-change');
  wp_enqueue_script('bootstrap-modal');
  wp_enqueue_style('bootstrap-modal');
}
add_action( 'wp_enqueue_scripts', 'my_enqueue', 20);
get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
		<div class="entry-content">

<?php
/* find the current user information */
global $current_user;
get_currentuserinfo();

$current_year = date('Y');

/* setup the template engine */
define('SMARTY_CHILDTHEMES', true);
$smarty = smarty_get_instance();

// default text for returning users
$displayname_text = "<p>We ask that you let us know what name to use when we address you in any of the forms you may complete and any emails that we send to you:</p>";
$password_text = "<p>If you want to change your password, clicking here will open a dialog window:</p>";

$require_password_reset = false;
if (get_user_option('default_password_nag')) {
  $require_password_reset = true;
}

/*
 * Handle a form submission
 */
$displayname = $current_user->display_name;
if(count($_POST) > 0) {

  // see if the password form was submitted
  if(isSet($_POST['pass2'])) {
    wp_set_password($_POST['pass2'],$current_user->ID);

    if ($require_password_reset) {
      // need to remove the password nag
      delete_user_option($current_user->ID,'default_password_nag');
      $require_password_reset = false;
    }
  }

}

if ($require_password_reset) {
  $displayname_text = "<h3>Please Set Your Name</h3>" . $displayname_text;
  $password_text = "<h3>Please Change Your Password</h3><p>Your current password is insecure because it has been sent via email</p>";
}

/*
 * Configure and display the form using the template engine
 */

$smarty->assign('displayname_text', $displayname_text);
$smarty->assign('password_text', $password_text);
$smarty->assign('displayname', $current_user->display_name);

$smarty->display("password-change2.html");

?>

                       </div><!-- .entry-content -->
		</div><!-- #content -->
	</div><!-- #primary -->

<?php /* get_sidebar(); */ ?>
<?php get_footer('mwm'); ?>