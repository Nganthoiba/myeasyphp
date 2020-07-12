<?php
declare(strict_types=1);
namespace Libs\SimpleRoleProvider;
use MyEasyPHP\Libs\Config;
use MyEasyPHP\Libs\EasyEntityManager;
use MyEasyPHP\Libs\UUID;
use MyEasyPHP\Models\Entities\RoleProvider\User;
use MyEasyPHP\Models\Entities\RoleProvider\Role;
use MyEasyPHP\Models\Entities\RoleProvider\UserRole;

class RoleProvider
{
    public $em;
    public function __construct(){
        $this->em = new EasyEntityManager();
    }
    
    public function AddUsersToRoles($usernames, $roleNames)
    {
        $userRole;
        foreach ($usernames as $username)
        {
            foreach ($roleNames as $roleName)
            {
                $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
                $user = $this->em->read(new User())->where(["UserName"=>$username])->getFirst();
                if ($role != null && $user != null)
                {
                    $userRole = $this->em->read(new UserRole())->where(["UserId" => $user->Id, "RoleId" => $role->Id])->getFirst();
                    if ($userRole == null)
                    {
                        $userRole = new UserRole();
                        $userRole->UserId = $user->Id;
                        $userRole->RoleId = $role->Id;
                        $this->response = $this->em($userRole);
                    }
                }
            }
        }
        
        return $this->sendResponse($this->response);
    }

    public function CreateRole(string $roleName)
    {
        $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
        if ($role == null)
        {
            $role->Id = UUID::v4();
            $role->Name = $roleName;
            $this->response = $this->em($role);
        }

        return $this->sendResponse($this->response);
    }

    public function DeleteRole(string $roleName)
    {
        $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
        if ($role != null)
        {
            $this->response = $this->em($role);
        }

        return $this->sendResponse($this->response);
    }
    
    public function GetAllRoles()
    {
        $roles = $this->em->read(new Role())->toList();
        return $roles;
    }
    
    public function GetRolesForUser(string $username)
    {
        $user = $this->em->read(new User())->where(["UserName"=>$username])->getFirst();
        if ($user == null)
        {
            return null;
        }
        return $user->Roles();
    }

    public function GetUsersInRole(string $roleName)
    {
        $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
        if ($role == null)
        {
            return null;
        }
        return $role->Users();
    }

    public function IsUserInRole(string $username, string $roleName)
    {
        $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
        $user = $this->em->read(new User())->where(["UserName"=>$username])->getFirst();
        if ($role == null || $user == null)
        {
            return false;
        }
        foreach ($user->Roles() as $r)
        {
            if ($r->Id == $role->Id)
            {
                return true;
            }
        }
        return false;
    }

    public function RemoveUsersFromRoles($usernames, $roleNames)
    {
        foreach ($usernames as $username)
        {
            foreach ($roleNames as $roleName)
            {
                $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
                $user = $this->em->read(new User())->where(["UserName"=>$username])->getFirst();
                if ($role != null && $user != null)
                {
                    $userRole = new UserRole();
                    $userRole->UserId = $user->Id;
                    $userRole->RoleId = $role->Id;
                    $this->response = $this->em($userRole);
                }
            }
        }
        return $this->sendResponse($this->response);
    }

    public function IsRoleExists($roleNames)
    {
        $role = $this->em->read(new Role())->where(["Name"=>$roleName])->getFirst();
        return $role == null ? false : true;
    }
}
