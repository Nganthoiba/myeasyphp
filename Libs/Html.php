<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\ViewData;

/**
 * Description of Html:
 * All the html related functions will be defined in this class. This class will be used in view files
 *
 * @author Nganthoiba
 */
class Html {
    //Directory webroot/Assets is the root of all css and js files. MyEasyPHP framework assumes that all your related 
    //js files are in the js directory and css files in css directory. So if you want to use a css file e.g. my_style.css,
    //place that file in this css folder and use the function loadCss('my_style.css'); or you can create as many as sub-folders
    //in the directory but you have to pass the path to that css files that you want to use, e.g loadCss('folder1/folder2/.../my_style.css') 
    //and the function will load the css in your html view file. The same thing goes for js files. 
    //Please note that the functions loadCss() and loadJs() will be used/invoked/called only in the view files only in the section
    //of the page where you want to load.
    
    //#Note: Before using any class file, make sure that you use the correct namespace of that file.
    public static function loadCss($path_to_css){
        if(is_array($path_to_css) && sizeof($path_to_css)>0){
            foreach($path_to_css as $css){
                self::loadCss($css);
            }
        }
        else if(is_string($path_to_css) && trim($path_to_css)!==""){
            $path =  Config::get('Assets')."/css/".$path_to_css.'.css';
            echo '<link rel="stylesheet" href="'.$path.'" type="text/css" />'."\r\n";
        }
        
    }
    public static function loadJs($path_to_js){
        if(is_array($path_to_js) && sizeof($path_to_js)>0){
            foreach($path_to_js as $js){
                self::loadJs($js);
            }
        }
        else if(is_string($path_to_js) && trim($path_to_js)!==""){
            $path =  Config::get('Assets')."/js/".$path_to_js.'.js';
            echo '<script src="'.$path.'" type="text/javascript"></script>'."\r\n";
        }
    }
    
    //This will load any css or js file within Assets directory
    public static function loadAssets($path_to_assets){
        //check for whether array or simple string
        if(is_array($path_to_assets) && count($path_to_assets)>0)
        {
            foreach($path_to_assets as $asset){
                self::loadAssets($asset);
            }
        }
        else if(is_string($path_to_assets) && trim($path_to_assets)!==""){
            //getting extension to know whether css file or js file 
            $ext = substr(trim($path_to_assets), -3);
            $path = Config::get('Assets')."/".$path_to_assets;
            //$path = "../Webroot/Assets/".$path_to_assets;
            if($ext == ".js"){
                //load javascript file
                echo '<script src="'.$path.'" type="text/javascript"></script>'."\r\n";
            }
            else if($ext == "css"){
                //load css file
                echo '<link rel="stylesheet" href="'.$path.'" type="text/css" />'."\r\n";
            }
        }
    }
    
    //to get image full url
    public static function getImage($img_path/*Image path*/){
        return  Config::get('Assets')."/".$img_path;
    }
    //function to include partial view files 
    public static function include($filepath = "", ViewData $view_Data=null, $modelObj = null){
        $output = NULL;
        if(trim($filepath)!==""){
            ob_start();//turns on output buffering
            $viewData = $view_Data;
            $model = $modelObj;
            require_once VIEWS_PATH.$filepath.".view.php";
            $output = ob_get_clean();
        }
        echo $output;
    }
    
    //method to generate a url
    public static function hyperlink($controller,$action="",$params = ""){
        $link = trim($controller)==""?Config::get('host')."/":Config::get('host')."/".$controller."/".$action;
        if((is_string($params) && trim($params) !== "") || is_numeric($params)){
            $link .= "/".$params;
        }
        else if(is_array($params)){
            foreach ($params as $param){
                $link .= "/".$param;
            }
        }
        return $link;
    }
    
    //function to set container view where the view will be loaded
    public function setContainer($view_container_path){
        //Note: any view file must be inside the Views directory or within its sub directory
        Config::set('default_view_container',$view_container_path);        
    }
}
