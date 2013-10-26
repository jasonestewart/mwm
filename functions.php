<?php
define('MWM_BASE_DIR', get_stylesheet_directory_uri());
define('MWM_JS_DIR', MWM_BASE_DIR . '/js');
define('MWM_CSS_DIR', MWM_BASE_DIR . '/css');

function enqueue_styles() {
  wp_register_style('pure-base', 
		    "http://yui.yahooapis.com/pure/0.3.0/base-min.css",
		    array(),
		    '1',
		    'all');

  wp_register_style('pure-forms', 
		    "http://yui.yahooapis.com/pure/0.3.0/forms-min.css",
		    array('pure-base'),
		    '1',
		    'all');
  wp_register_style('pure-buttons', 
		    "http://yui.yahooapis.com/pure/0.3.0/buttons-min.css",
		    array('pure-base'),
		    '1',
		    'all');
  wp_register_style('bootstrap-modal', 
		    MWM_CSS_DIR . '/bootstrap-modal.css',
		    array(),
		    '2.3.2',
		    'all');
}

add_action( 'wp_enqueue_scripts', 'enqueue_styles' );

function mwm_enqueue_scripts() {
  wp_register_script('password-change',
		     MWM_JS_DIR . '/password.js',
		     array('jquery'),
		     '4.0.1');
  wp_register_script('pledge-change',
		     MWM_JS_DIR . '/pledge-change.js',
		     array('jquery'),
		     '0.0.1');
 wp_register_script('bootstrap-modal',
		     MWM_JS_DIR . '/bootstrap-modal.js',
		     array('jquery'),
		     '2.3.2');
		    
}

add_action( 'wp_enqueue_scripts', 'mwm_enqueue_scripts' );

function mwm_wp_nav_menu_items($items, $args) {
 
  // Make sure this is the Primary Menu.
  // You may need to modify this condition
  // depending on your theme.
  if ($args->theme_location == 'primary') {
    
    // CSS class to use for <li> item.
    $class = 'menu-item';
 
    if (is_user_logged_in()) {
      // User is logged in, link to welcome page.
      $extra = '
<li id="menu-item-logged-in-user" class="'.$class.'">
<a href="'.get_bloginfo("wpurl").'/edit-profile">
'.__('Edit Profile').', '.wp_get_current_user()->user_login.'!
</a>
</li>
';
    } else {
      // User is guest, link to login page.
      $extra = '
<li id="menu-item-logged-out-user" class="'.$class.'">
<a href="'.get_bloginfo("wpurl").'/wp-login.php">
'.__('Log in').'
</a>
</li>
';
    }
 
    // Add extra link to existing menu.
    $items = $items . $extra; 
  }
  
  // Return menu items.
  return $items;
  
}
 
// Hook into wp_nav_menu_items.
add_filter( 'wp_nav_menu_items', 'mwm_wp_nav_menu_items', 10, 2 );


function debug_query( $result, $data )
{
  global $current_user;
  get_currentuserinfo();

  if ( current_user_can( 'manage_options' ) )
    {
      global $wpdb, $blog_id;
        1 !== $blog_id
	  AND ! defined( 'DIEONDBERROR' )
	  AND define( 'DIEONDBERROR', true );

        $wpdb->show_errors     = true;
        $wpdb->suppress_errors = false;

        $output = '<pre style="white-space:pre-line;">';
	$output .= 'Last Error: ';
	$output .= var_export( $wpdb->last_error, true );

	$output .= "\n\nLast Query: ";
	$output .= var_export( $wpdb->last_query, true );

	if ( false === $result )
	  {
	    $result = new WP_Error( 'query_failed', 'No update.', $data );
	  }
	elseif ( 0 === $result )
	  {
	    $result = new WP_Error( 'update_failed', 'Updated zero rows.', $data );
	  }
	elseif ( 0 < $result )
	  {
	    $result = 'Success';
	  }
        $output .= '</pre>';

        // Only abort, if we got an error
        is_wp_error( $result ) 
	  AND exit( $output.$result->get_error_message() );
    }
}
?>
