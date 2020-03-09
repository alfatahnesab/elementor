<?php
/**
 * Plugin Name: Elementor Form Create New User
 * Description: Create a new user using elementor form
 * Author:      Alfatah Nesab
 * Author URI:  https://alfatahnesab.com
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_action( 'elementor_pro/forms/new_record',  'alfa_elementor_form_create_new_user' , 10, 2 );

function alfa_elementor_form_create_new_user($record,$ajax_handler)
{
    $form_name = $record->get_form_settings('form_name');
    //Check that the form is the "create new user form" if not - stop and return;
    if ('Create New User' !== $form_name) {
        return;
    }
    
    $form_data = $record->get_formatted_data();
    
    $username=$form_data['Email']; //Get tne value of the input with the label "User Name"
    $password = $form_data['Password']; //Get tne value of the input with the label "Password"
    $email=$form_data['Email'];  //Get tne value of the input with the label "Email"
    $redirect=$form_data['Page Redirect URL'];  //Get tne value of the input with the label "Email"
    $phone=$form_data['Phone Number'];  //Get tne value of the input with the label "Phone Number"
    $practice=$form_data['Practice Field'];  //Get tne value of the input with the label "Practice Field"
    $role=$form_data['Role'];  //Get tne value of the input with the label "Role"

		
    $user = wp_create_user($username,$password,$email); // Create a new user, on success return the user_id no failure return an error object

    if (is_wp_error($user)){ // if there was an error creating a new user
        $ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); //add the message
        $ajax_handler->is_success = false;
        return;
    }
    
    $first_name=$form_data["First Name"]; //Get tne value of the input with the label "First Name"
    $last_name=$form_data["Last Name"]; //Get tne value of the input with the label "Last Name"
       
	wp_update_user(array("ID"=>$user,"first_name"=>$first_name,"last_name"=>$last_name)); // Update the user with the first name and last name
	update_user_meta( $user, 'user_role', $role ); 
	update_user_meta( $user, 'user_phone', $phone ); 
	update_user_meta( $user, 'user_practice', $practice ); 

    /* Automatically log in the user and redirect the user to the home page */
    $creds= array( // credientials for newley created user
        "user_login"=>$username,
        "user_password"=>$password,
        "remember"=>true
    );
    
    $signon = wp_signon($creds); // sign in the new user
    if ($signon)
      // Set redirect action
	$ajax_handler->add_response_data( 'redirect_url', $redirect );
      //  $ajax_handler->add_response_data( 'redirect_url', get_home_url() ); // optinal - if sign on succsfully - redierct the user to the home page
}