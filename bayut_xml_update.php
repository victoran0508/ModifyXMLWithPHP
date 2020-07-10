<?php

define('XML_URL', 'http://xml.propspace.com/feed/xml.php?cl=1692&pid=8245&acc=8807');
define('XML_FILENAME', 'bayut.xml');

function pr($msg) {
  print (is_string($msg) ? $msg : '<pre>'.print_r($msg, TRUE).'</pre>').'<br>';
}

function xml_append(SimpleXMLElement $to, SimpleXMLElement $from) {
  $toDom = dom_import_simplexml($to);
  $fromDom = dom_import_simplexml($from);
  $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
}

if ($external_xml = simplexml_load_file(XML_URL)) {
  if ($saved_xml = simplexml_load_file(XML_FILENAME)) {
    
    $new_xml = simplexml_load_string('<Listings></Listings>');
  
    foreach ($external_xml->Listing as $external_xml_listing) {
      $original_refno = (string)$external_xml_listing->Property_Ref_No;
      $original_title = (string)$external_xml_listing->Property_Title;
      
      if ( ($saved_xml_node = $saved_xml->xpath("/Listings/Listing/Web_Remarks[contains(text(), '$original_refno')]/..")) || ($saved_xml_node = $saved_xml->xpath("/Listings/Listing/Property_Title[text()='$original_title']/.."))) {
        $saved_xml_node = $saved_xml_node[0]; // get first found object (there should be only one)
        foreach ($saved_xml_node as $saved_xml_node_field) {         
          $external_xml_listing->Property_Ref_No = $saved_xml_node->Property_Ref_No;
          $external_xml_listing->Unit_Reference_No = $saved_xml_node->Unit_Reference_No;
          $external_xml_listing->Listing_Date = $saved_xml_node->Listing_Date;
          $external_xml_listing->Last_Updated = $saved_xml_node->Last_Updated;
        }        
      }
      xml_append($new_xml, $external_xml_listing);
    }
    $new_xml->asXML(XML_FILENAME);
  } else {
    die('Can not open local XML for update');
  }
} else {
  die('Can not open XML from propspace.com');
}

?>
