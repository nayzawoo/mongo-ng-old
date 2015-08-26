<?php namespace App\Libs\Role\Traits;

/**
 * Created by Nay Zaw Oo<naythurain.071@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait UserTrait
{
    public function getRole()
    {
        return $this->hasOne(config('auth.role', 'App\Models\Role'), 'slug', 'role');
    }

    public function getId()
    {
        return $this->id;
    }

    public function is($name, $requireAll = false)
    {
        if (is_null($name)) {
            return false;
        }
        $roles = explode('||', $name);
        // Or 
        foreach ($roles as $role) {
            if ($this->role == $role) {
                return true;
            }
        }
        return false;
    }

    public function isNot($name, $requireAll = false) {
        return ! $this->is($name, $requireAll);
    }

    public function can($permissionNames, $requireAll = false)
    {

        if (is_null($permissionNames)) {
            return false;
        }

        $permissionNames = is_array($permissionNames) ? $permissionNames : [$permissionNames];

        $role = $this->getRole;
        if (is_null($role)) {
            return false;
        }

        $permissions = $role->getPermissions();

        foreach ($permissionNames as $permissionName) {
            if (!in_array($permissionName, $permissions)) {
                return false;
            }
        }

        return true;
    }

    public function attachRole($role)
    {
        if ($role instanceof \Jenssegers\Mongodb\Model) {
            $this->role = $role->slug;
        } else {
            $this->role = $role;
        }
        return $this->save();
    }

    public function detachRole($role)
    {
        $this->role = null;
    }

    public function findAllUsers()
    {
        return $this->get();
    }

    public function isBanned()
    {
        return !! $this->banned;
    }

    public function isActivated()
    {
        return !! $this->activated;
    }

    public function findUserById($id) {
        $user = $this->find($id);
        if (!$user)
            throw new ModelNotFoundException;
        return $user;
    }

    public function attemptActivation($code)
    {
        if ($this->activated) {
            throw new UserAlreadyActivatedException("Uesr is already activated", 1);
        }

        if (!$code || $this->activation_code !== $code ) {
            return false;
        }
        $this->activated = 1;
        return true;
    }
}
