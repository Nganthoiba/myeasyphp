<?php

/*
 * This file is responsible to check user authorization of an action of a controller 
 */

namespace MyEasyPHP\Libs;
use MyEasyPHP\Libs\Controller;
use MyEasyPHP\Models\LoginModel;
use MyEasyPHP\Libs\EasyEntityManager;
/**
 * Description of Authorization
 *
 * @author Nganthoiba
 */

class Authorization {
    
    //method to check if an action of a controller is authorized for any specific role or
    //group of roles or annonymous
    public static function isAuthorized(Controller $controller, string $action): bool{
        //first check if the action is annonymous action: anyone can access with or without authentication
        if(in_array(strtolower($action), $controller->getAnnonymousActions())){
            return true;
        }
        
        // Check Athentication is required for the controller (serves for each actions in controller)
        $isControllerAuthenticationRequired = $controller->isControllerAuthenticationRequired();
        if($isControllerAuthenticationRequired){
            if(!LoginModel::isAuthenticated()){
                return false;
            }
        }
               
        //check if authentication is required for the action
        $isActionAuthenticationRequired = $controller->isActionAuthenticationRequired($action);
        if($isActionAuthenticationRequired){           
            
            if(!LoginModel::isAuthenticated()){
                return false;
            }
            
            //getting user id
            $loginInfo = LoginModel::getLoginInfo();
            $userId = $loginInfo->Id;
            
            // Check action for authorization
            $authorizations = $controller->getAuthorizations();
            $authorizedRoles = self::getRoles($authorizations, $action);
           
            if(sizeof($authorizedRoles)==0){
                return true;//if no role is found, then it means the action is accessible by
                //any kind of user
            }
            //now getting all the roles of a user
            $em = new EasyEntityManager();
            /*
            $list = $em->readTable("UserRoles UR", ["UR.RoleId","R.Name as role_name"])
                    ->leftJoin("Roles R")->on("UR.RoleId = R.Id")->where([
                        "UR.UserId"=>$userId
                    ])->get();
             * 
             */
            $list = $em->readTable("users U",['R.role_id','R.role_name'])
                    ->join("roles R")->on("U.role_id = R.role_id")
                    ->where([
                        "U.user_id" => $userId
                    ])->get();
            $userRoles = [];
            foreach ($list as $row){
                $userRoles[] = strtolower($row['role_name']);
            }
            $commonRoles = array_intersect($userRoles, $authorizedRoles);
            if(is_null($commonRoles) || sizeof($commonRoles) == 0){
                return false;
            }
        }
        return true;
    }
    
    
    //method to get user roles permitted for an action of the specific controller
    //It can return either empty array (in case if there is no role specified) or an array of user roles
    public static function getRoles(array $authorizations, string $action){
        $roles = array();
        foreach ($authorizations as $row){
            $actions_n_roles = explode(":",$row);
            $action_name = trim($actions_n_roles[0]);
            if(strtolower($action_name) == strtolower($action)){
                if(isset($actions_n_roles[1])){
                    $roles = self::extractRoles($actions_n_roles[1]);
                    break;
                }
                break;
            }
        }
        return $roles;
    }
    
    //extract user roles
    public static function extractRoles($substring): array{
        $roles = explode(",",strtolower(trim($substring)));
        for($i=0; $i<sizeof($roles); $i++){
            $roles[$i] = trim($roles[$i]);
        }
        return $roles;
    }
}
