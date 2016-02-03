<?php

if( !$_SERVER['HTTP_REFERER'] ) {
  echo 'You need to login from <a href="http://whatevs:8888">here</a>';

//} elseif( isset( $_GET['token'] ) ) {
//  $stored_token = 'abc'; // retrieve token from db
// if( true || $_GET['token'] != $stored_token ){
//   $loc = preg_replace( '/\?.*?$/', '', $_SERVER['HTTP_REFERER'] );
//   exit( $loc );
//   header( "Location: " . $loc . $params );
// }

} else {
  // create a token
  $token = 'abc';

  // save the token
  // redirect back with the token

  //handle params from POST'ed login form
  $params = '';

  $params .= "?is_in=0";

  if( !isset( $_POST['login_allow'] ) ) {
    $params .= "&is_authed=0";
  } else {
    $params .= "&token=" . $token;
  }

  // redirect back to where you came from
  $loc = preg_replace( '/\?.*?$/', '', $_SERVER['HTTP_REFERER'] );
  header( "Location: " . $loc . $params );
}
?>
