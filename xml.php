<?php

define('XML_URL', 'http://xml.propspace.com/feed/xml.php?cl=1692&pid=8245&acc=8807');
define('XML_FILENAME', 'bayut.xml');

function pr($msg) {
  print (is_string($msg) ? $msg : '<pre>'.print_r($msg, TRUE).'</pre>').'<br>';
}

$user_random_numbers = array();

function getRandomNumber($min = 1, $max = 999) {
  global $user_random_numbers;
  do {
    $rand = rand($min, $max);
  } while (in_array($rand, $user_random_numbers));
  $user_random_numbers[] = $rand;
  return $rand;
}

if ($xml = simplexml_load_file(XML_URL)) {      
  $k = 0;
  foreach ($xml->Listing as $xml_listing) {
    $property_ref_no = isset($xml_listing->Property_Ref_No) ? preg_replace('#[0-9]+#i', date('d').getRandomNumber(), $xml_listing->Property_Ref_No) : NULL;
    $listing_date = date('Y-m-d h:i:s a', time()-rand(1, 60*60*24)); 
    
    $xml->Listing[$k]->Property_Ref_No = $property_ref_no;
    $xml->Listing[$k]->Unit_Reference_No = $property_ref_no;    
    $xml->Listing[$k]->Listing_Date = $listing_date;
    $xml->Listing[$k]->Last_Updated = $listing_date;
    $k++;
  }
  $xml->asXML(XML_FILENAME);
}

?>
