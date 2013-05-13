<?php

/*!
 * Base CRM and MailChimp Plugin
 * http://www.stormgate.co.uk
 * V1.2.2
 *
 * Copyright 2012, Woody Hayday, StormGate Ltd, David Whitehouse
 *
 * Date: 24/08/2012
 *
 * Quick helper functions for interfacing with BASE 
 * Written by W.H of StormGate ltd. 27/07/2012 - Copyright StormGate 2012.
 * Please do not modify/re-use without getting approval from StormGate Ltd.
 *
 */
	
	function base_getAuth($e,$p){
	
		$url = 'https://sales.futuresimple.com/api/v1/authentication.json';
		$tokenJsonResponse = wCURL($url,array('email'=>$e,'password'=>$p));
		$jsonToken = json_decode($tokenJsonResponse);
		if (isset($jsonToken->authentication->token)) return $jsonToken->authentication->token; else return false;
		
	}
	
	
	function base_addCompany($token,$name){
		
		if (!empty($name) && !empty($token)){
			
			$url = 'https://sales.futuresimple.com/api/v1/contacts.json';
			$postArr = array('contact'=>array('name'=>$name,'is_organisation'=>true));
			$jsonResponse = wCurl($url,$postArr,array('X-Pipejump-Auth'=>$token));
			$jsonContact = json_decode($jsonResponse);
			if (isset($jsonContact->contact->id)) return $jsonContact->contact->id; else return false;		
			
		} else return false;		
		
	}
	
	#} Token and last name req.
	function base_addIndividual($token,$lastName,$firstName='',$companyBaseContactID='',$phone='',$mobile='',$email=''){
		
		if (!empty($lastName) && !empty($token)){
			
			$url = 'https://sales.futuresimple.com/api/v1/contacts.json'; 
			$postArr = array('contact'=>array('is_organisation'=>false));
			
			# Last Name is Req. http://dev.futuresimple.com/api/methods/contact-create
													$postArr['contact']['last_name'] = $lastName;
			if (!empty($firstName)) 				$postArr['contact']['first_name'] = $firstName;
			if (!empty($companyBaseContactID)) 		$postArr['contact']['contact_id'] = $companyBaseContactID;
			if (!empty($phone)) 					$postArr['contact']['phone'] = $phone;
			if (!empty($mobile)) 					$postArr['contact']['mobile'] = $mobile;
			if (!empty($email)) 					$postArr['contact']['email'] = $email;
			
			$jsonResponse = wCurl($url,$postArr,array('X-Pipejump-Auth'=>$token));
			$jsonContact = json_decode($jsonResponse);
			if (isset($jsonContact->contact->id)) return $jsonContact->contact->id; else return false;		
			
		} else return false;		
		
	}
	
	function base_addDeal($token,$contactID,$name,$sourceID=0){
		
			if (!empty($contactID) && !empty($token) && !empty($name)){
			
			$url = 'https://sales.futuresimple.com/api/v1/deals.json';
			$postArr = array();
			
			# Contact id and name Req. http://dev.futuresimple.com/api/methods/deal-create
									$postArr['entity_id'] 	= $contactID;
									$postArr['name'] 		= $name;
			if (!empty($sourceID)) 	$postArr['source_id'] 	= $sourceID;
			
			$jsonResponse = wCurl($url,$postArr,array('X-Pipejump-Auth'=>$token));
			$jsonDeal = json_decode($jsonResponse);
			if (isset($jsonDeal->deal->id)) return $jsonDeal->deal->id; else return false;		
			
		} else return false;		
		
	}
	
	function base_addNoteToContact($token,$contactID,$noteContent){
		
		if (!empty($contactID) && !empty($noteContent) && !empty($token)){
			
			$url = 'https://sales.futuresimple.com/api/v1/contacts/'.(int)$contactID.'/notes.json';
			$postArr = array('note'=>array('content'=>$noteContent));
			$jsonResponse = wCurl($url,$postArr,array('X-Pipejump-Auth'=>$token));
			$jsonNote = json_decode($jsonResponse);
			if (isset($jsonNote->note->id)) return $jsonNote->note->id; else return false;		
			
		} else return false;		
		
	}
	function base_addNoteToDeal($token,$dealID,$noteContent){
		
		if (!empty($dealID) && !empty($noteContent) && !empty($token)){
			
			$url = 'https://sales.futuresimple.com/api/v1/deals/'.(int)$dealID.'/notes.json'; 
			$postArr = array('note'=>array('content'=>$noteContent));
			$jsonResponse = wCurl($url,$postArr,array('X-Pipejump-Auth'=>$token));
			$jsonNote = json_decode($jsonResponse);
			if (isset($jsonNote->note->id)) return $jsonNote->note->id; else return false;		
			
		} else return false;		
		
	}
	
	function base_addOrGetSource($token,$sourceName){
		
		if (!empty($sourceName) && !empty($token)){
			
		# First check sources - perhaps later these could be sensibly cached.
		$sources = base_getSources($token); $foundID = 0;
		if ($sources != 7) foreach ($sources as $source)
			if ($source['source']['name'] == $sourceName) { $foundID = $source['source']['id']; break; }

			
			if (empty($foundID)){
				
				$url = 'https://sales.futuresimple.com/api/v1/sources.json'; 
				$postArr = array('source'=>array('name'=>$sourceName));
				$jsonResponse = wCurl($url,$postArr,array('X-Pipejump-Auth'=>$token));
				$jsonSource = json_decode($jsonResponse);
				if (isset($jsonSource->source->id)) return $jsonSource->source->id; else return false;		
				
			} else return $foundID;
		
		} else return false;	
			
		
	}
	
	function base_getSources($token){
		
		if (!empty($token)){
			
		# First check sources
			
			$url = 'https://sales.futuresimple.com/api/v1/sources.json';
			$jsonResponse = wCurl($url,'',array('X-Pipejump-Auth'=>$token));
			$jsonSources = json_decode($jsonResponse);
			if (isset($jsonSources[0]->source->id)) return wObjectToArray($jsonSources); else return 7;		
			
		} else return false;		
		
	}
		
	#} Added for api contact
	function wCURL($url, $post = null, $headers = null, $retries = 3) 
	{
		$curl = curl_init($url);
	
		if (is_resource($curl) === true)
		{
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);#use facebook default
			
			if (isset($headers)){
			
				if (is_array($headers)) {
					
					foreach ($headers as $name => $val)
						curl_setopt($curl, CURLOPT_HTTPHEADER,	array($name.': '.$val));
					
				} else curl_setopt($curl, CURLOPT_HTTPHEADER,	array($headers));
	
			}	
			
			if (isset($post) === true)
			{
				if (!empty($post)){
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, (is_array($post) === true) ? http_build_query($post, '', '&') : $post);
				}
			}
	
			$result = false;
	
			while (($result === false) && (--$retries > 0))
			{
				$result = curl_exec($curl);
			}
			
			curl_close($curl);
		}
		
		return $result;
	}
	
	function wObjectToArray( $object )
		{
			if( !is_object( $object ) && !is_array( $object ) )
			{
				return $object;
			}
			if( is_object( $object ) )
			{
				$object = get_object_vars( $object );
			}
			return array_map( 'wObjectToArray', $object );
		}

?>