<?php
namespace MyEasyPHP\Libs\ext;
/**
 * Description of Base64encoder
 *
 * @author Nganthoiba
 */
use Exception;
class Base64encoder {
    //this function will decode the base 64 encoded image file and store the file in the given directory
    //and return the full path of the image.
    public static function decodeToPNG(string $b64encoded,$file_name){
        // Obtain the original content (usually binary data)
        $data = explode(',',$b64encoded);
        $bin = base64_decode($data[1]);

        // Load GD resource from binary data
        $im = imageCreateFromString($bin);

        // Make sure that the GD library was able to load the image
        // This is important, because you should not miss corrupted or unsupported images
        if (!$im) {
            throw new Exception('Base64 value is not a valid image',405);
        }
        $file_name = str_replace("-", "_", $file_name);
        // Specify the location where you want to save the image
        $img_file = FILES_PATH.'images/'.$file_name.'.png';
        if(file_exists($img_file)){
            unlink($img_file);
        }
        if(!is_dir(FILES_PATH.'images')){
            //if directory does not exists
            mkdir(FILES_PATH.'images',0755,true);
        }
            
        if(!is_writable(FILES_PATH.'images')){
            throw new Exception("Consult with web administration, directory ".FILES_PATH.'images is restricted, '
                    . 'file cannot be copied.',405);
        }

        // Save the GD resource as PNG in the best possible quality (no compression)
        // This will strip any metadata or invalid contents (including, the PHP backdoor)
        // To block any possible exploits, consider increasing the compression level
        imagepng($im, $img_file, 0);
        return str_replace(FILES_PATH,"",$img_file);//return path of the image
    }
    
    public static function encodeImagetoBase64($path){
        //$path: path to image file i.e. location
        if(is_null($path)|| $path=="" || !file_exists(FILES_PATH.$path)){
            $path = FILES_PATH.'/images/user.png'; 
        }
        
        $type = pathinfo(FILES_PATH.$path, PATHINFO_EXTENSION);
        $data = file_get_contents(FILES_PATH.$path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
    
    public static function decodeBase64ToImage(string $encodedStr, string $filePath,string $file_name){
        // Obtain the original content (usually binary data)
        $data = explode(',',$encodedStr);
        $bin = base64_decode($data[1]);

        // Load GD resource from binary data
        $im = imageCreateFromString($bin);

        // Make sure that the GD library was able to load the image
        // This is important, because you should not miss corrupted or unsupported images
        if (!$im) {
            throw new Exception('Base64 value is not a valid image',405);
        }
        
        // Specify the location where you want to save the image
        
        $img_file = $filePath.DS.$file_name.'.png';
        $img_fullFilePath = FILES_PATH.$img_file;
        if(file_exists($img_fullFilePath)){
            unlink($img_fullFilePath);
        }
        if(!is_dir($filePath)){
            //if directory does not exists
            mkdir(FILES_PATH.$filePath,0755,true);
        }
            
        if(!is_writable(FILES_PATH.'images')){
            throw new Exception("Consult with web administration, directory ".FILES_PATH.'images is restricted, '
                    . 'file cannot be copied.',405);
        }

        // Save the GD resource as PNG in the best possible quality (no compression)
        // This will strip any metadata or invalid contents (including, the PHP backdoor)
        // To block any possible exploits, consider increasing the compression level
        imagepng($im, $img_fullFilePath, 0);
        return $img_file;//return path of the image
    }
    
}
