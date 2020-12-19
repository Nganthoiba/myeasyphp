<?php
/**
 * Description of Upload
 * This class is to upload files in desired location
 * @author Nganthoiba
 * Addition classes used: libs/Response.class.php
 */

namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\Response;

class Upload {
    //allowed MIME types
    private $allowed_mime_types;
    private $file_upload_directory;//directory or path where the file will be uploaded
    private $file_upload_key_name; //name of the uploaded file
    private $max_size;//maximum file size
    private $response;
    public function __construct() {
        $this->response = new Response();
        $this->allowed_mime_types = [
                "JPG" => "image/jpeg", 
                "jpg" => "image/jpeg",		
                "jpeg" => "image/jpeg",
                "png" => "image/png", 
                "pdf"=>"application/pdf",
                "PDF"=>"application/pdf"
            ];
        $this->file_upload_directory = UPLOAD_PATH.DS;
        $this->file_upload_key_name = "";
        $this->file_upload_directory = "";        
        $this->max_size = 2 * 1024 * 1024;// maximum file size is set to 2 MB by default.
    }
    public function setAllowedMimeTypes($mime_types = array()){
        if(!is_array($mime_types) || sizeof($mime_types)==0){
            return;
        }
        $this->allowed_mime_types = $mime_types;
    }
    //this function is very much mandatory
    public function setFileUploadKeyName($upload_name){
        $this->file_upload_key_name = $upload_name;
    }
    
    public function setMaxFileSizeLimit($max){
        $this->max_size = $max;
    }
    
    //function to upload multiple files
    public function uploadMultipleFiles($upload_directory=""){
        if(trim($upload_directory)!==""){
            $this->file_upload_directory = $upload_directory;
        }
        if(trim($this->file_upload_key_name) === ""){
            return $this->response->set([
                "msg"=>"Upload file key name is not set."
            ]);
        }
        if(!is_dir($this->file_upload_directory)){
            //if directory does not exist
            mkdir($this->file_upload_directory,0755,true);
        }

        if(!is_writable($upload_directory)){
            return $this->response->set([
                "msg" => "Directory is not writable.Missing file permission."
            ]);
        }
        if(!isset($_FILES[$this->file_upload_key_name]) || empty($_FILES[$this->file_upload_key_name])){
            return $this->response->set([
                "msg" => "No file is uploaded. "
            ]);
        }
        // Count number of uploaded files in array
        $total = count($_FILES[$this->file_upload_key_name]['name']);
        if($total === 0){
            return $this->response->set([
                "msg" => "No file is uploaded. "
            ]);
        }
        
        $file_paths = array();
        $errors = array();
        
        //validate all the files
        for( $i=0 ; $i < $total ; $i++ ) {
            $this->response = $this->validate($_FILES,$i);
            if ($this->response->status === false){
                return $this->response;
            }
        }
        //after all files are found valid, we start writing each file in memory
        for( $i=0 ; $i < $total ; $i++ ) {
            $temp_name = $_FILES[$this->file_upload_key_name]['tmp_name'][$i];
            $file_name = $_FILES[$this->file_upload_key_name]['name'][$i];
            $file_error = $_FILES[$this->file_upload_key_name]["error"][$i];
            //Set new file path
            $uploaded_file_path = rtrim($this->file_upload_directory,'/')."/".basename($file_name);
            //Upload the file into the temp dir
            if(move_uploaded_file($temp_name, $uploaded_file_path)) {
                $file_paths[] = $uploaded_file_path;
            }
            else{
                $errors[] = $file_error;
                break;
            }
            
        }
        if(sizeof($file_paths)===0){
            $this->response->set([
                "status"=>false,
                "status_code"=>403,
                "msg"=>"No file has been uploaded.",
                "error" => $this->response->error===null?$errors:array_merge($errors,$this->response->error)
            ]);
        }
        else{
            $this->response->set([
                "msg"=>count($file_paths)." file(s) has been uploaded.",
                "status_code"=>200,
                "error"=> $this->response->error===null?$errors:array_merge($errors,$this->response->error),
                "data"=>[
                    "file_paths"=>$file_paths
                    ]
                ]);
        }
        return $this->response;
    }
    
    //function to upload single file
    public function uploadSingleFile($upload_directory=""){
        if(trim($upload_directory)!==""){
            $this->file_upload_directory = $upload_directory;
        }
        if(trim($this->file_upload_key_name) === ""){
            return $this->response->set([
                "msg"=>"Upload file key name is not set."
            ]);
        }
        if(!isset($_FILES[$this->file_upload_key_name]) || empty($_FILES[$this->file_upload_key_name])){
            return $this->response->set([
                "msg" => "No file is uploaded. "
            ]);
        }
        $temp_name = $_FILES[$this->file_upload_key_name]['tmp_name'];
        $file_name = $_FILES[$this->file_upload_key_name]['name'];
        $file_error = $_FILES[$this->file_upload_key_name]["error"];
             
        $this->response = $this->validate($_FILES);
        if($this->response->status){
            //if validation status is true
            
            if(!is_dir($this->file_upload_directory)){
                //if directory does not exist
                mkdir($this->file_upload_directory,0755,true);
            }
            
            if(!is_writable($upload_directory)){
                return $this->response->set([
                    "status_code"=>403,
                    "msg" => "Directory is not writable. Missing file permission."
                ]);
            }
            //full file path
            $uploaded_file_path = rtrim($this->file_upload_directory,'/')."/".basename($file_name);
            if(file_exists($uploaded_file_path)){
                return $this->response->set([
                    "status_code"=>403,
                    "msg" => "A file with the same name as $file_name already exists, it cann't be replaced."
                ]);
            }
            $upload_status = move_uploaded_file($temp_name, $uploaded_file_path);
            if($upload_status){
                $this->response->set([
                    "msg" => "File uploaded successfully.",
                    "status"=>true,
                    "status_code"=>200,
                    "data"=>[
                        "file_paths"=>[$uploaded_file_path]
                    ]
                ]);
            }
            else{
                $this->response->set([
                    "msg" => "File upload failed.",
                    "error" => [$file_error,"Failed to write file to disk."]
                ]);
            }
        }
        return $this->response;
    }
    
    //function to validate file which is uploaded
    private function validate($upload_file,$index = -1){
        $this->response->status = false;
        if($index === -1){
            $temp_name = $upload_file[$this->file_upload_key_name]['tmp_name'];
            $file_size = $upload_file[$this->file_upload_key_name]['size'];
            $file_name = $upload_file[$this->file_upload_key_name]['name'];
            $file_type = $upload_file[$this->file_upload_key_name]['type'];
        }
        else{
            $temp_name = $upload_file[$this->file_upload_key_name]['tmp_name'][$index];
            $file_size = $upload_file[$this->file_upload_key_name]['size'][$index];
            $file_name = $upload_file[$this->file_upload_key_name]['name'][$index];
            $file_type = $upload_file[$this->file_upload_key_name]['type'][$index];
        }
        $file_base_name = basename($file_name);
        //die("Base Name: ".$file_base_name);
        if(trim($file_base_name) === ""){
            return $this->response->set([
                "status_code"=>400,
                "msg"=>"File is not uploaded, empty file."
            ]);
        }
        
        /**** Validating file extension ****/
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);//getting file extension
        if(!array_key_exists($ext, $this->allowed_mime_types)){
            return $this->response->set([
                "status_code"=>400,
                "msg"=>"Unsupported file format. '$file_name'",
                "error"=>[$ext." file is not allowed."]
            ]);
        }
        
        /**** Preventing more than one file extensions ****/
        $file_parts = explode(".",$file_name);
        if(count($file_parts)>2){
            array_shift($file_parts);
            return $this->response->set([
                "status_code"=>400,
                "msg"=>"'$file_name' has more than one file extensions which is not allowed.",
                "error"=>["More than one file extensions","file extensions"=>$file_parts]
            ]);
        }
        
        /**** Validating MIME types ****/
        $mime_type = $this->getMimeType($temp_name);//getting MIME type
        if(!in_array($mime_type, array_values($this->allowed_mime_types))){
            return $this->response->set([
                "status_code"=>400,
                "msg"=>"'$file_name' has invalid or unsupported MIME type.",
                "error"=>["Invalid or unsupported MIME type:".$mime_type]
            ]);
        }
        
        /**** Validating whether file extension matches the correct mime type and prevent if not matched ****/
        if($mime_type !== $this->allowed_mime_types[$ext]){
            return $this->response->set([
                "status_code"=>400,
                "msg"=>"'$file_name' has wrong MIME type.",
                "error"=>["Wrong MIME type:".$mime_type." whereas file extension is: ".$ext]
            ]);
        }
        
        /**** Validation file size limit ****/
        if($file_size > $this->max_size){
            return $this->response->set([
                "status_code"=>400,
                "msg"=>"File size must not exceed ".($this->max_size/1024)." MB",
                "error"=>["File size exceeds the limit. Supported file size is upto ".($this->max_size/1024)." MB"]
            ]);
        }
        return $this->response->set([
            "status"=>true,
            "msg"=>"Valid file"
        ]);
    }
    
    //function to get mime type of a file
    private function getMimeType($file) {
        $mtype = false;
        if($file === ""){
            return "Empty MIME type";
        }
        else if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $file);
            finfo_close($finfo);
        } 
        else if (function_exists('mime_content_type')) {
            $mtype = mime_content_type($file);
        } 
        return $mtype;
    }
}
