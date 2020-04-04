<?php
/**
 * Plugin Name: Elementor Form Create New User
 * Description: Create a new user using elementor pro form
 * Author:      Alfatah Nesab
 * Author URI:  https://alfatahnesab.com
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_action( 'elementor_pro/forms/new_record',  'alfa_elementor_form_create_new_user' , 10, 2 ); // This is the hooks of elementor form after form submit

function alfa_elementor_form_create_new_user($record,$ajax_handler) // creating function 
{
    $form_name = $record->get_form_settings('form_name');
    
    //Check that the form is the "Sign Up" if not - stop and return;
    if ('Sign Up' !== $form_name) {
        return;
    }
    
    $form_data = $record->get_formatted_data();
 
 //Get tne value of the input with the label  
    $username=$form_data['Email']; // You can choose any form field as username, but it should be unique.  
    $password = $form_data['Password'];  // Label "Password"
    $email=$form_data['Email'];  // Label "Email"
    
    // This is the required field to create a user in wordpress. Let create user with wordpress function wp_create_user();
		//wp_create_user( string $username, string $password, string $email = '' )
	$user = wp_create_user($username,$password,$email); // Create a new user, on success return the user_id no failure return an error object

    if (is_wp_error($user)){ // if there was an error creating a new user
        $ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); //add the message
        $ajax_handler->is_success = false;
        return;
    }
  // Before Add user meta data we need to get the value of First Name and Last Name in the variable. Like below:
    	$first_name=$form_data["First Name"]; //Get tne value of the input with the label "First Name"
    	$last_name=$form_data["Last Name"]; //Get tne value of the input with the label "Last Name"

  // Now we are going to add user related information which is called user meta data with the function wp_update_user
	wp_update_user(array("ID"=>$user,"first_name"=>$first_name,"last_name"=>$last_name)); // Update the user with the first name and last name

    /* Automatically log in the user and redirect the user to the home page */
    $creds= array( // credientials for newley created user
        "user_login"=>$username,
        "user_password"=>$password,
        "remember"=>true
    );
    
    $signon = wp_signon($creds); // sign in the new user
    if ($signon)
      
      // Set redirect action
      $ajax_handler->add_response_data( 'redirect_url', get_home_url() ); // optinal - if sign on succsfully - redierct the user to the home page
}
