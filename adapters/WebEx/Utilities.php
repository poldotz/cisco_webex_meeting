<?php

namespace Smx\SimpleMeetings\WebEx;

class Utilities
{
    /*
     * Method to load XML template file, create XML object, inject user credentials
     * and return a SimpleXMLElement object for further alterations.
     * @param $action Should be a valid action name mapping to an XML file
     * @return SimpleXMLElement
     * @throws ErrorException Exception thrown when unable to load or parse the XML file.
     */
    public static function loadXml($action, $username, $password, $sitename){
        libxml_use_internal_errors(true);
        $file = __DIR__.'/xml/'.$action.'.xml';
        
        $xml = simplexml_load_file($file);
        if($xml){
            $xml->header->securityContext->webExID = $username;
            $xml->header->securityContext->password = $password;
            $xml->header->securityContext->siteName = $sitename;
            return $xml;
        } else {
            $errors = '';
            foreach(libxml_get_errors() as $error) {
                $errors .= ",\t" . $error->message;
            }
            $error = "Unable to load file $file, errors occured: $errors";
            throw new \ErrorException($error,105);
        }
    }
    
    /*
     * Performs API call to WebEx XML API
     * @param String|SimpleXMLElement $xml Either the full XML to be sent or a SimpleXMLElement object
     * @return SimpleXMLElement The bodyContent section of successful API response
     * @throws ErrorException when the API call fails or it is unable to parse response.
     */
    public static function callApi($xml, $sitename){
        if(is_object($xml)){
            $xml = $xml->asXML();
        }
        
        $url = "https://".$sitename.".webex.com/WBXService/XMLService";
        
        $client = new \Zend\Http\Client();
        $client->setUri($url);
        $client->setMethod('POST');
        $client->setRawBody($xml);
        
        $response = $client->send();
        if($response && $response->getStatusCode() == '200'){
            $body = $response->getBody();
            /*
             * Clear out unnecessary namespaces from returned XML
             */
            $body = preg_replace('/<[a-z]{1,}:/','<',$body);
            $body = preg_replace('/<\/[a-z]{1,}:/','</',$body);
            
            libxml_use_internal_errors(true);
            $results = new \SimpleXMLElement($body);
            if($results){
                if($results->header->response->result == 'SUCCESS'){
                    return $results->body->bodyContent;
                } else {
                    $error = "An error was returned from the API: ".
                            $results->header->response->reason->__toString().' ('.
                            $results->header->response->exceptionID->__toString().')';
                    throw new \ErrorException($error,108);
                }
            } else {
                $errors = '';
                foreach(libxml_get_errors() as $error) {
                    $errors .= ",\t" . $error->message;
                }
                $error = "Unable to parse XML response, errors occured: $errors";
                throw new \ErrorException($error,106);
            }
        } else {
            $error = "API call returned an HTTP status code of ".$response->getStatusCode();
            throw new \ErrorException($error,107);
        }
    }
}