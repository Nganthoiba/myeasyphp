<?php
declare(strict_types=1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Libs;

/**
 * Description of HttpStatus
 *
 * @author Nganthoiba
 */
class HttpStatus {
    public static $status = array(  
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad request',
            401 => 'Unauthorized request',
            403 => 'Forbidden',
            404 => 'Resource Not Found',   
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            500 => 'Internal Server Error'
        );
    public static $OK = 200;
    public static $Created = 201;
    public static $NoContent = 204;
    public static $BadRequest = 400;
    public static $Unauthorized = 401;
    public static $Forbidden = 403;
    public static $NotFound = 404;
    public static $MethodNotAllowed = 405;
    public static $NotAcceptable = 406;
    public static $ProxyAuthenticationRequired= 407;
    public static $RequestTimeOut= 408;
    public static $Conflict= 408;
    public static $InternalServerError= 500;
    
    public static function getStatus($code){
        return isset(self::$status[$code])? self::$status[$code]: "Unknown Error"; 
    }
}
