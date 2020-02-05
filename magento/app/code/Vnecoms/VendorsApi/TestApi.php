<?php
function getToken($username, $password){
	$userData = array("username" => $username, "password" => $password);
	$ch = curl_init("http://192.168.26.101/rest/V1/integration/customer/token");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData))));
	 
	$token = curl_exec($ch);
	return $token;
}

function getUserInfo($token){
	$ch = curl_init("http://192.168.26.101/rest/V1/vendors/me");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}

function getOrders($token){
$url = "http://192.168.26.101/rest/V1/vendors/orders?".
'searchCriteria[pageSize]=5';
echo $url."\n\n";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}


function getBestselling($token){
$url = "http://192.168.26.101/rest/V1/vendors/report/bestselling?".
'limit=5';
echo $url."\n\n";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}

function getMostViewed($token){
$url = "http://192.168.26.101/rest/V1/vendors/report/mostviewed?".
'limit=5';
echo $url."\n\n";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}

function getProducts($token){
$url = "http://192.168.26.101/rest/V1/vendors/products?".
'searchCriteria[pageSize]=1';
echo $url."\n\n";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}

function getNotifications($token){
$url = "http://192.168.26.101/rest/V1/vendors/notifications?".
'searchCriteria[pageSize]=1';
echo $url."\n\n";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}

function getUnreadCount($token){
$url = "http://192.168.26.101/rest/V1/vendors/notifications/unreadcount";
echo $url."\n\n";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $token));
	 
	$token = curl_exec($ch);
	return $token;
}

function saveProducts($token){
	$url = "http://192.168.26.101/rest/V1/vendors/products";
	echo $url."\n\n";
	
	$dataJson = [
		"product"=> [
			"sku"=> "hungvt-test-product-api",
			"name"=> "Hungvt Test Product API",
			"attributeSetId"=> 4,
			"price"=> 77,
			"type_id"=> 'simple',
			"weight"=> 2,
			"status"=> 1,
			"visibility"=> 4,
			"extension_attributes" => [
				"stock_item"=>[
					'qty' => 565,
					'is_in_stock' => 1,
					'manage_stock' => 1,
					'use_config_manage_stock' => 1,
					'min_qty' => 0,
					'use_config_min_qty' => 1,
					'min_sale_qty' => 1,
					'use_config_min_sale_qty' => 1,
					'max_sale_qty ' => 10,
					'use_config_max_sale_qty' => 1,
					'is_qty_decimal' => 0,
					'backorders' => 0,
					'use_config_backorders' => 1,
					'notify_stock_qty' => 1,
					'use_config_notify_stock_qty' => 1
				],
				// "mediaGalleryEntries" => [
					// "media_type"=> "thumbnail",
          // "label"=> "This is sample label",
          // "position"=> 0,
          // "disabled"=> true,
          // "types"=> [
            // "thumbnail"
          // ],
          // "file"=> "01_14.jpg",
          // "content"=> [
            // "base64_encoded_data"=> base64_encode(file_get_contents('01_14.jpg')),
            // "type"=> "file/jpg",
            // "name"=> "01_14.jpg"
          // ],
				// ]
			],
			'custom_attributes' =>[
				"configurable_variation" => 217,
				"ves_enable_order" => 1,
				"vendor_id"=> 1,
				"approval"=> 2,
				[
					'attribute_code' => 'news_from_date',
					'value' => '07/18/2018'
				],
				[
					'attribute_code' => 'country_of_manufacture',
					'value' => 'BB'
				],
				[
					'attribute_code' => 'category_ids',
					'value' => [24,17]
				]
			],
			
		],
		"saveOptions" => true
	];
	
	$dataString = json_encode($dataJson);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		"Authorization: Bearer " . $token,
		'Content-Length: ' . strlen($dataString))
	);

	$result = curl_exec($ch);
	return $result;
}



// echo getToken('hungvt@vnecoms.com', 'Admin123')."\n\n";


$token = "5lxhyx5mw79n236957br9y5bvc6kfqyd";
echo (saveProducts($token))."\n";
exit;
$items = json_decode(getUnreadCount($token),true);
var_dump($items);
