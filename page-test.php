<?php if ( !is_user_logged_in() ) { auth_redirect(); } ?>                      

<?php
/**                                                                             
 * The template for creating new pledges for a user                             
 *                                                                             
Template Name: Test Template
 */        

function my_styles() {
  wp_enqueue_style('pure-forms');
  wp_enqueue_style('pure-buttons');
  wp_enqueue_script('pledge-change');
}
add_action( 'wp_enqueue_scripts', 'my_styles', 20);
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
} else {
  echo "<p class='error'>Bad Profile Count: " . count($res) . "</p>";
}
$res = $user->withCondition('year = ?', array($current_year))->ownPledge;
if (count($res) == 1) {
  $pledge = array_pop($res);
}

/*
 * Defaults
 */
$income = 0;
$cons_amount = 0; 
$sav_amount = 0; 

/*
 * Handle a form submission
 */
if(count($_POST) > 0) {
  if (isSet($pledge)) {
    // can't update the pledge year
  } else {
    $pledge = R::dispense('pledge');
    $user->ownPledge[] = $pledge;
    $pledge->year = $current_year;
  }
  $pledge->sav_amount = $_POST['sav_amount'];
  $pledge->cons_amount = $_POST['cons_amount'];
  $pledge->charity = $_POST['charity'];

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
  $cons_amount = $_POST['cons_amount'];
  $sav_amount = $_POST['sav_amount'];
  $charity = $_POST['charity'];
} else {
  if (isSet($pledge)) {
    $cons_amount = $pledge->cons_amount; 
    $sav_amount = $pledge->sav_amount; 
    $charity = $pledge->charity; 
  }
}

if(isSet($profile)){
  $income = $profile->income;
}

// decide if we're updating or registering a new pledge
if(isSet($cons_amount)) {
  $pledge_text = "You have already made a pledge for " . $current_year . ".";
  $submit = 'Update My Pledge';
} else {
  $pledge_text = "How much do you want to pledge for " . $current_year . "?";
  $submit = 'Make a Pledge';
}

/*
 * Configure and display the form using the template engine
 */
$smarty->assign('name', $current_user->display_name);
$smarty->assign('current_year',$current_year);
$smarty->assign('income',$income);
$smarty->assign('cons_amount', $cons_amount);
$smarty->assign('sav_amount', $sav_amount);
$smarty->assign('charity', $charity);
$smarty->assign('pledge_text', $pledge_text);
$smarty->assign('submit', $submit);

$smarty->display("pledge-form.html");
?>

                       </div><!-- .entry-content -->
		</div><!-- #content -->
	</div><!-- #primary -->

<?php /* get_sidebar(); */ ?>
<?php get_footer('mwm'); ?>