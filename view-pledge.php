<?php
/**
 * The template for displaying pledges for a user
 *

Template Name: View Pledge
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
  <h1>This is the view pledges page</h1>


<div id="pledge">
  <?php if ( is_user_logged_in() ) {
  $current_user = wp_get_current_user();
$user_id = $current_user->ID;
// echo 'User ID: ' . $user_id . '<br />';
// echo 'Username: ' . $current_user->user_login . '<br />';

$first = $current_user->user_firstname;
// echo 'User first name: ' . $first . '<br />';

$args = array('user_id' => $user_id,);
$sub_results = ninja_forms_get_subs( $args ); // This is an array of submission results.
$data = $sub_results[0]['data'][0]['user_value'];
echo "Pledge for " . $first . " is: " . $data;

} else {
  echo "Sorry, you must be logged in to view your pledges.";
}
?>
</div><!-- #pledge -->


		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer('mwm'); ?>