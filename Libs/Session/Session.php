<?php
declare(strict_types=1);
namespace MyEasyPHP\Libs\Session;

/**
 * Description of Session
 * To access session data
 * @author Nganthoiba
 */
class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE){
            ini_set('session.use_only_cookies', 'true'); // Forces sessions to only use cookies. 
            ini_set("session.cookie_httponly", 'true');//(No xxs)This will prevent from javascript to display session cookies over browser
            ini_set('session.httponly','true');/* securing cookies and session*/
            session_start(); // Start the php session
        }
        return session_regenerate_id(true); 
        // regenerated the session, delete the old one.
    }
    //to read data from session
    public static function read(string $key){
        self::start();
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }
        return "";
    }
    
    //to write data to session
    public static function write(string $key, $value){
        self::start();
        $_SESSION[$key] = $value;
    }
    //to remove data from session
    public static function remove(string $key){
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function display(){
        self::start();
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    }
    
    public static function destroy(){
        if ( session_id() !== '' )
        {
            $_SESSION = array();
            // If it's desired to kill the session, also delete the session cookie.
            // Note: This will destroy the session, and not just the session data!
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
        }
    }
}
