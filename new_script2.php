<?php

define('XML_URL', 'https://bcrm_org.s3.amazonaws.com/xml/275/1591607442_5_275_59411.xml');
//define('XML_URL', '1591607442_5_275_59411.xml');
define('XML_FILENAME', 'old_type.xml');

function pr($msg) {
	print (is_string($msg) ? $msg : '<pre>'.print_r($msg, TRUE).'</pre>').'<br>';
}

function xml_append(SimpleXMLElement $to, SimpleXMLElement $from) {
	$toDom = dom_import_simplexml($to);
	$fromDom = dom_import_simplexml($from);
	$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
}

function get_data($url) {
    $ch = curl_init();
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, "Googlebot/2.1...");

	ob_start();
    curl_exec($ch);
    curl_close($ch);
	$data = ob_get_contents();
	ob_end_clean();
    return $data;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

$loadEntities = libxml_disable_entity_loader(false);
if ($external_xml = simplexml_load_file(XML_URL)) {

	$new_xml = new SimpleXMLElement('<Listings></Listings>');

	$i = 1;
	foreach ($external_xml->Property as $external_xml_property) {
		$Listing = $new_xml->addChild('Listing');
		//$Listing->addAttribute('type', 'latest');
		$Listing->addChild('count', $i++);
		$Listing->addChild('Ad_Type', ((String)$external_xml_property->Property_purpose == 'Buy' ? 'Rent' : 'Sale'));
		$Listing->addChild('Property_Ref_No', (String)$external_xml_property->Property_Ref_No);
		//$Listing->addChild('', (String)$external_xml_property->Property_Status);
		//$Listing->addChild('', (String)$external_xml_property->Transaction_Number);
		$Listing->addChild('Unit_Type', (String)$external_xml_property->Property_Type);
		$Listing->addChild('Unit_Model', (String)$external_xml_property->Property_Type);
		//$Listing->addChild('', (String)$external_xml_property->Furnished);
		$Listing->addChild('Emirate', (String)$external_xml_property->City);
		$Listing->addChild('Community', (String)$external_xml_property->Locality);
		$Listing->addChild('Property_Name', htmlspecialchars((String)$external_xml_property->Tower_Name));
		$Listing->addChild('Property_Title', htmlspecialchars((String)$external_xml_property->Property_Title));
		$Listing->addChild('Web_Remarks', htmlspecialchars($external_xml_property->Property_Description));
		$Listing->addChild('Unit_Builtup_Area', (String)$external_xml_property->Property_Size);
		//$Listing->addChild('', (String)$external_xml_property->Property_Size_Unit);
		$Listing->addChild('Bedrooms', (String)$external_xml_property->Bedrooms);
		//$Listing->addChild('', (String)$external_xml_property->Bathroom);
		$Listing->addChild('Price', (String)$external_xml_property->Price);
		$Listing->addChild('Listing_Agent', (String)$external_xml_property->Listing_Agent);
		$Listing->addChild('Listing_Agent_Phone', (String)$external_xml_property->Listing_Agent_Phone);
		$Listing->addChild('Listing_Agent_Email', (String)$external_xml_property->Listing_Agent_Email);
		$Facilities = $Listing->addChild('Facilities');
		$Features = $external_xml_property->Features;
		foreach ($Features->Feature as $feature) {
			$Facilities->addChild('facility', htmlspecialchars((String)$feature));
		}
		$images = $Listing->addChild('Images');
		$Images = $external_xml_property->Images;
		foreach ($Images->Image as $image) {
			$images->addChild('image', htmlspecialchars((String)$image));
		}
		//$Listing->addChild('', (String)$external_xml_property->Videos);
		$Listing->addChild('Last_Updated', (String)$external_xml_property->Last_Updated);
		$Listing->addChild('Listing_Date', (String)$external_xml_property->Last_Updated);
		$Rent_Frequency = (String)$external_xml_property->Rent_Frequency;
		$Listing->addChild('Frequency', 'per ' . (endsWith($Rent_Frequency, 'ly') ? substr($Rent_Frequency, 0, -2) : $Rent_Frequency));
		//$Listing->addChild('', (String)$external_xml_property->featured_on_companywebsite);
		//$Listing->addChild('', (String)$external_xml_property->Exclusive_Rights);
		$Listing->addChild('Longitude', explode(',', $external_xml_property->geopoints)[0]);
		$Listing->addChild('Latitude', explode(',', $external_xml_property->geopoints)[1]);
		$Listing->addChild('completion_status', (String)$external_xml_property->Completion_Status);
	}

	$new_xml->asXML(XML_FILENAME);
} else {
  die('Can not open XML from bcrm_org.s3.amazonaws.com');
  print_r(external_xml);
}

?>
