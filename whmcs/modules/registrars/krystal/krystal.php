<?php

function krystal_getAPI($params)
{
	require_once(dirname(__FILE__) . "/API.class.php");
	return new \Krystal\API1\API($params["API_Username"], $params["API_Secret"]);
}

function krystal_getDomainId(&$API, $domain)
{
	return $API->call("services/domains/find", "GET", ["domain" => $domain])["payload"]["domain"]["id"];
}

function krystal_getConfigArray()
{
	return
	[
		"FriendlyName" =>
		[
			"Type" => "System",
			"Value" => "Krystal Hosting Ltd"
		],
		
		"API_Username" =>
		[
			"Type" => "text",
			"Size" => "20",
			"Description" => "Enter your API username here"
		],
		
		"API_Secret" =>
		[
			"Type" => "text",
			"Size" => "40",
			"Description" => "Enter your API secret here"
		],
		
		"Use_Credit_Card" =>
		[
			"Type" => "yesno",
			"Description" => "Tick to attempt to charge your CC for domain orders. If disabled, and you have no credit balance in your account, your orders will fail."
		],
	];
}

function krystal_GetNameservers($params)
{
	$API = krystal_getAPI($params);

	try
	{
		return $API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/nameservers", "GET")["payload"]["nameservers"];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_SaveNameservers($params)
{	
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/nameservers", "PUT", [], ["nameservers" => [$params["ns1"], $params["ns2"], $params["ns3"], $params["ns4"]]]);
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_GetRegistrarLock($params)
{
	$API = krystal_getAPI($params);

	try
	{
		return ($API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/locking", "GET")["payload"]["locked"]) ? "locked" : "unlocked";
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_SaveRegistrarLock($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/locking", "PUT", [], ["locked" => (($params["lockenabled"] == "locked") ? true : false)]);
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_ReleaseDomain($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/release", "POST", [], ["ips_tag" => $params["transfertag"]]);
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_RegisterDomain($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/", "POST", ["use_cc" => (($params["Use_Credit_Card"] == "on") ? "1" : "")],
		[
			"domain" =>
			[
				"domain" => $params["domainname"],
				"term" => $params["regperiod"],
				"order_type" => "registration",
				"additional_fields" => $params["additionalfields"],
				"nameservers" => [$params["ns1"], $params["ns2"], $params["ns3"], $params["ns4"]]
			],
			
			"contact_details" =>
			[
				"firstname" => $params["firstname"],
				"lastname" => $params["lastname"],
				"address1" => $params["address1"],
				"address2" => $params["address2"],
				"city" => $params["city"],
				"state" => $params["state"],
				"postcode" => $params["postcode"],
				"country" => $params["country"],
				"email" => $params["email"],
				"phonenumber" => $params["phonenumber"]
			]
		]);
		
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_TransferDomain($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/", "POST", ["use_cc" => (($params["Use_Credit_Card"] == "on") ? "1" : "")],
		[
			"domain" =>
			[
				"domain" => $params["domainname"],
				"term" => $params["regperiod"],
				"order_type" => "transfer",
				"additional_fields" => $params["additionalfields"],
				"nameservers" => [$params["ns1"], $params["ns2"], $params["ns3"], $params["ns4"]]
			],
			
			"contact_details" =>
			[
				"firstname" => $params["firstname"],
				"lastname" => $params["lastname"],
				"address1" => $params["address1"],
				"address2" => $params["address2"],
				"city" => $params["city"],
				"state" => $params["state"],
				"postcode" => $params["postcode"],
				"country" => $params["country"],
				"email" => $params["email"],
				"phonenumber" => $params["phonenumber"]
			]
		]);
		
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_RenewDomain($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/renew", "POST", ["use_cc" => (($params["Use_Credit_Card"] == "on") ? "1" : "")], ["term" => $params["regperiod"]]);
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_GetContactDetails($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$contacts = $API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/contacts", "GET")["payload"]["contacts"];
		
		$newContacts = [];
		
		foreach($contacts as $contact => $contactSet)
		{
			if(!isset($newContacts[$contact])) $newContacts[$contact] = [];
			
			foreach($contactSet as $fieldName => $value)
			{
				$fieldName = str_replace("_", " ", $fieldName);
				$newContacts[$contact][$fieldName] = $value;
			}
		}
		
		return $newContacts;
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_SaveContactDetails($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/contacts", "PUT", [], ["contacts" => $params["contactdetails"]]);
		return ["error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_GetEPPCode($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$payload = $API->call("services/domains/" . krystal_getDomainId($API, $params["domainname"]) . "/epp_code", "GET")["payload"];
		if($payload["emailed"]) return ["eppcode" => "", "error" => ""];
		return ["eppcode" => $payload["epp_code"], "error" => ""];
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_TransferSync($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$domain = $API->call("services/domains/find", "GET", ["domain" => $params["domain"]])["payload"]["domain"];
		
		$values = [];
		
		// Add in the expiry if we have it
		if($domain["expirydate"]) $values["expirydate"] = $domain["expirydate"];
		
		if($domain["status"] == "active")
		{
			$values["completed"] = true;
		}
		elseif($domain["status"] != "pending transfer")
		{
			$values["failed"] = true;
			$values["reason"] = "Please contact us for more information";
		}
		
		return $values;
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}

function krystal_Sync($params)
{
	$API = krystal_getAPI($params);

	try
	{
		$domain = $API->call("services/domains/find", "GET", ["domain" => $params["domain"]])["payload"]["domain"];
		
		$values = [];
		
		// Add in the expiry if we have it
		$values["expirydate"] = $domain["expirydate"];
		
		if($domain["status"] == "active")
		{
			$values["active"] = true;
		}
		else
		{
			$values["expired"] = true;
		}
		
		return $values;
	}
	catch(\Krystal\API1\Exceptions\Exception $e)
	{
		return ["error" => get_class($e) . " - " . $e->getMessage()];
	}
}






























































