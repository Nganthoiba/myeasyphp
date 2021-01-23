<?php
declare(strict_types = 1);
namespace MyEasyPHP\Libs\XML;

/**
 * Description of XMLEncoder
 * This will accept input data preferably in the form of array or object
 * @author Nganthoiba
 */
use SimpleXMLElement;
class XMLEncoder {
    //put your code here
    public static function encode($data){        
        $xml = '<?xml version = "1.0" encoding = "UTF-8" standalone = "no" ?>';
        return $xml."<container>".self::processData($data)."</container>";
        
        /*
        $xml = self::startEncoding(new SimpleXMLElement('<container/>'),$data);
        return $xml->asXML();
         */
    }
    
    private static function startEncoding(SimpleXMLElement $xml, $data, string $tagName=''):SimpleXMLElement{
        if(is_string($data)){
            if($tagName === ""){
                $xml->addChild("string",$data);
            }
            else{
                $xml->addChild($tagName, $data);
            }
            return $xml;
        }
        if(is_object($data)){
            $data = json_decode(json_encode($data),true);
        }
        if(is_array($data)){
            foreach ($data as $key=>$val){
                $tag = (is_int($key))?(($tagName === '')?'element':$tagName):$key;                
                if(is_array($val)){
                    $xml = self::startEncoding($xml,$val,$tag);
                }
                else{
                    $xml->addChild($tag, ''.$val);
                }                
            }
        }
        return $xml;
    }
    
    private static function processData($data, $tagName=''){       
        $xmlString = '';
        if(is_string($data)){
            return $tagName===''?"<string>{$data}</string>":"<{$tagName}>{$data}</{$tagName}>";
        }
        //if data is an object then convert it into array, if it is already an array
        //then OK
        if(is_object($data)){
            $data = json_decode(json_encode($data),true);
        }
        if(is_array($data)){
            foreach ($data as $key=>$val){
                $tag = (is_int($key))?'element':$key;
                if(is_array($val)){
                    $xmlString .= "<{$tag}>".self::processData($val)."</{$tag}>";
                }
                else{
                    $xmlString .= "<{$tag}>{$val}</{$tag}>";
                }
            }
        }
        return $xmlString;
    }
}
