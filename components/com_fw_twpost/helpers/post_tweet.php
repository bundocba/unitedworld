<?php
require 'twitter/tmhOAuth.php';
class post_tweet{
	// Create an OAuth connection to the Twitter API
	function post($content , $consumer_key,$consumer_secret,$user_token,$user_secret){
		/*
		  echo '$content: '.$content.'</br>';
		  echo '$consumer_secret :'.$consumer_secret.'</br>';
		  echo '$consumer_key: '.$consumer_key.'</br>';
		  echo '$user_token: '.$user_token.'</br>';
		  echo '$user_secret: '.$user_secret.'</br>';exit;
		  */
		  $connection 		=  new tmhOAuth
		  							(array(
		  'consumer_key'    => trim($consumer_key),
		  'consumer_secret' => trim($consumer_secret),
		  'user_token'      => trim($user_token),
		  'user_secret'     => trim($user_secret),
										));

		// Send a tweet
		$code = $connection->request('POST', 
			$connection->url('1.1/statuses/update'), 
			array('status' => $content));
		
		//echo $code;exit;
		// A response code of 200 is a success
		if ($code == 200) {
		 	return 1;
		} else {
		  	return $code;
		}
	}
}