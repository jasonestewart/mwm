<?php if ( !is_user_logged_in() ) { auth_redirect(); } ?>                      

<?php
/**                                                                             
 * The template for creating new pledges for a user                             
 *                                                                             
Template Name: Profile Edit Template
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

/* setup the ORM engine */
R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);

/*
 * Fetch the user and profile information from the DB
 */
$user = R::load('user',$current_user->ID);
$res = $user->withCondition('year = ?', array($current_year))->ownProfile;
if (count($res) == 1) {
  $profile = array_pop($res);
}

// default text for returning users
$displayname_text = "<p>We ask that you let us know what name to use when we address you in any of the forms you may complete and any emails that we send to you:</p>";
$password_text = "<p>If you want to change your password, clicking here will open a dialog window:</p>";

// we'll need to fetch the user meta data
// to check to see if this is a first time user
$meta_arr = $user->withCondition('meta_key = ?', 
				 array('default_password_nag'))->ownMeta;

$require_password_reset = false;
if (count($meta_arr) > 0) {
  $require_password_reset = true;
}

/*
 * Handle a form submission
 */
$displayname = $current_user->display_name;
if(count($_POST) > 0) {

  // see if the password form was submitted
  if(isSet($_POST['pass2'])) {
    $hash = wp_hash_password( trim( $_POST['pass2']));
    $user->password = $hash;

    if ($require_password_reset) {
      // need to remove the password nag
      $meta = array_pop($meta_arr);
      unset($user->ownMeta[$meta->id]);
      $require_password_reset = false;
    }
  }

  if(isSet($_POST['displayname'])) {
    $user->displayname = $_POST['displayname'];
    $displayname = $user->displayname;
  }
  if (isSet($profile)) {
    // can't change profile year
  } else {
    $profile = R::dispense('profile');
    $profile->year = $current_year;
    $user->ownProfile[] = $profile;
  }
  $profile->income = $_POST['inc_amount'];
  $profile->income_source = $_POST['inc_source'];
  $profile->employment_status = $_POST['emp_status'];

  try {
    R::store($user);
  } catch(Exception $ex) {
    echo "<p class='error'>ERROR: " . $ex->getMessage() . "</p>";
  }
}

if ($require_password_reset) {
  $displayname_text = "<h3>Please Set Your Name</h3>" . $displayname_text;
  $password_text = "<h3>Please Change Your Password</h3><p>Your current password is insecure because it has been sent via email</p>";
}

if(isSet($profile)){
  $income = $profile->income;
  $emp_status = $profile->employment_status;
  $inc_source = $profile->income_source;
}

/*
 * Configure and display the form using the template engine
 */

$smarty->display("password-change.html");

$smarty->assign('displayname_text', $displayname_text);
$smarty->assign('password_text', $password_text);
$smarty->assign('displayname', $displayname);
$smarty->assign('current_year',$current_year);
$smarty->assign('emp_radios', array('self-employed'=>'Self-Employed',
				    'unemployed'=>'Unemployed',
				    'employed'=>'Employed'));
$smarty->assign('emp_status',$emp_status);
$smarty->assign('inc_source',$inc_source);
$smarty->assign('income',$income);
$smarty->assign('cons_amount', $cons_amount);
$smarty->assign('sav_amount', $sav_amount);
$smarty->assign('charity', $charity);
$smarty->assign('pledge_text', $pledge_text);
$smarty->assign('submit', $submit);

$smarty->display("profile-form.html");
?>

                       </div><!-- .entry-content -->
		</div><!-- #content -->
	</div><!-- #primary -->

<?php /* get_sidebar(); */ ?>
<?php get_footer('mwm'); ?>