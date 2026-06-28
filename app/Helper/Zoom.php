<?php

namespace App\Helper;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;

require_once $_SERVER['DOCUMENT_ROOT']."/services/php-jwt-master/src/BeforeValidException.php";
require_once $_SERVER['DOCUMENT_ROOT']."/services/php-jwt-master/src/ExpiredException.php";
require_once $_SERVER['DOCUMENT_ROOT']."/services/php-jwt-master/src/SignatureInvalidException.php";
require_once $_SERVER['DOCUMENT_ROOT']."/services/php-jwt-master/src/JWT.php";

//include_once 'services/php-jwt-master/Zoom_Api.php'; 

use \Firebase\JWT\JWT;

class Zoom
{
	
	
	public static $zoom_api_key;
	public static $zoom_api_secret;
	
	
	
	
	
	public static function  setting($key, $secret )
	{
		self:: $zoom_api_key		=	$key;
		self:: $zoom_api_secret	=	$secret;		
	}
	// Function to generate JWT Token 
	private static function generateJWTKey()
	{
		$key 		=	self::$zoom_api_key; 
		$secret 	=	self :: $zoom_api_secret;
		$token		= 	array(
								"iss" 	=> $key,
								"exp"	=>	time()+3600
							   );
		return JWT ::encode($token, $secret);
							   	
	}
	// Function to create meeting 
	public static function createMeeting($data = array(), $zoom_email)
	{
	
		$post_time 		=	$data['start_date'];
		$start_time		=	gmdate("Y-m-d\TH:i:s", strtotime($post_time));
		
		
		$createMeetingArray					=	array();
		$createMeetingArray['topic']		=	$data['topic'];
		$createMeetingArray['agenda']		=	!empty($data['agenda'])? $data['agenda'] : "";
		$createMeetingArray['type']			=	!empty($data['type'])? $data['type'] : 2;
		$createMeetingArray['start_time']	=	$start_time;
		$createMeetingArray['timezone']		=	'Asia/Kolkata';
		$createMeetingArray['password']		=	!empty($data['password'])? $data['password'] : "";
		$createMeetingArray['duration']		=	!empty($data['duration'])? $data['duration'] : "";
		
		$createMeetingArray['settings']		=	array(
														'join_before_host'	=>  !empty($data['join_before_host'])? true : false,
														'host_video'		=>  !empty($data['option_host_video'])? true : false,
														'participant_video'	=>  !empty($data['option_participants_video'])? true : false,
														'mute_upon_entry'	=>  !empty($data['option_mute_participants'])? true : false,
														'enforce_login'		=>  !empty($data['option_enforce_login'])? true : false,
														'auto_recording'	=>  !empty($data['option_auto_recording'])? $data['option_auto_recording'] : "",
														'alternative_hosts'	=>  isset($alternative_host_ids)? $alternative_host_ids:""		
													);
		
		
		
		return self::sendRequest($createMeetingArray, $zoom_email);
	}
	public static function sendRequest($data, $zoom_email)
	{
		$email				= 	$zoom_email;
		$request_url		=	"https://api.zoom.us/v2/users/".$email."/meetings";
		
		$headers			=	array(
										"authorization: Bearer ".self::generateJWTKey(),
										"content-type: application/json",
										"Accept:application/json"
								 );
								 
	
	
			$postFields		=	json_encode($data);
		
			$ch				=	curl_init();
			curl_setopt_array($ch, array(
											CURLOPT_URL					=> $request_url,
											CURLOPT_RETURNTRANSFER		=>	true,
											CURLOPT_ENCODING			=>	"",
											CURLOPT_MAXREDIRS			=>	10,
											CURLOPT_TIMEOUT				=>	30,
											CURLOPT_HTTP_VERSION		=>	CURL_HTTP_VERSION_1_1,
											CURLOPT_CUSTOMREQUEST		=>	"POST",
											CURLOPT_POSTFIELDS			=>	$postFields,
											CURLOPT_HTTPHEADER			=>	$headers			
										)
							);
			
			
			$response		=	curl_exec($ch);
			$err			=	curl_error($ch);
			curl_close($ch);
			
			if(!$response)
			{
				return $err;
			}
			
			return json_decode($response);					 
	
	} 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	
	
	
	
	
	
}