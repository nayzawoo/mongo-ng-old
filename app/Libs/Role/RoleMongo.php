<?php namespace App\Libs\Role;

/**
 * Created by Nay Zaw Oo<nayzawoo.me@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM
 */

use App\Libs\Role\Traits\RoleTrait;
use Jenssegers\Mongodb\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use App\Libs\Role\Exceptions\RoleAlreadyExistsException;
use App\Libs\Role\Exceptions\RoleNotFoundException;

class RoleMongo extends Model
{

    protected $collection;

    protected $fillable = ['name', 'desc', 'slug', 'permissions'];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->collection = config('entrust.roles_table');
    }

    public function createRole( $name, $desc = null)
    {
        $slug = Str::slug($name);

        if ( $this->whereSlug($slug)->first() ) {
            throw new RoleAlreadyExistsException("Role Already Exists!!", 1);
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->desc = $desc;
        $this->save();
        return $this;
    }

    public function attachPermissions( array $permissions )
    {
        $this->permissions = $permissions;
        return $this->save();
    }

    public function addPermissions( array $permissions )
    {
        $thisPermissions = is_array($this->permissions) ? $this->permissions : [];
        $this->permissions = array_unique(array_merge($thisPermissions, $permissions));
        return $this->save();
    }

    public function getBySlug( $slug )
    {
        $role = $this->whereSlug($slug)->first();
        if (!$role) {
            throw new RoleNotFoundException("Group Not Found", 1);
        }
        return $role;
    }

    public function findBySlug( $slug )
    {
        return $this->getBySlug($slug);
    }

    public function getPermissions()
    {
        return is_array($this->permissions) ? $this->permissions : [];
    }
}
