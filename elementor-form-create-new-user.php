<?php
/**
 * Plugin Name: Elementor Form Create New User
 * Description: Create a new user using elementor pro form
 * Author:      Alfatah Nesab
 * Author URI:  https://alfatahnesab.com
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Version:     1.0.0
 */

add_action( 'elementor_pro/forms/new_record',  'alfa_elementor_form_create_new_user' , 10, 2 );

function alfa_elementor_form_create_new_user($record,$ajax_handler) // creating function 
{
    $form_name = $record->get_form_settings('form_name');
    
    //Check that the form is the "Sign Up" if not - stop and return;
    if ('Sign Up' !== $form_name) {
        return;
    }
    
    $form_data  = $record->get_formatted_data();
 
    $username   = $form_data['Email'];
    $email      = $form_data['Email']; 
    $password   = $form_data['Password']; 

    
    $user = wp_create_user($username,$password,$email); 

    if (is_wp_error($user)){ 
        $ajax_handler->add_error_message("Failed to create new user: ".$user->get_error_message()); 
        $ajax_handler->is_success = false;
        return;
    }

    // Assign Primary field value in the created user profile
    $first_name   =$form_data["First Name"]; 
    $last_name    =$form_data["Last Name"];
    wp_update_user(array("ID"=>$user,"first_name"=>$first_name,"last_name"=>$last_name)); 

    // Assign Additional added field value in the created user profile
    $user_phone   =$form_data["Phone"]; 
    $user_bio     =$form_data["Bio"];
    update_user_meta($user, 'user_phone', $user_phone);    
    update_user_meta($user, 'user_bio', $user_bio); 

    /* Automatically log in the user and redirect the user to the home page */
    $creds= array(
        "user_login"=>$username,
        "user_password"=>$password,
        "remember"=>true
    );
    
    $signon = wp_signon($creds); 
    
    if ($signon) {
        $ajax_handler->add_response_data( 'redirect_url', get_home_url() );
    }
} 
