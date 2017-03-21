<?php namespace App\Libs\Role;

/**
 * Created by Nay Zaw Oo<nayzawoo.me@gmail.com>
 * User: nay
 * Date: D/M/Y
 * Time: MM:HH PM
 */

use App\Libs\Role\Contracts\PermissionInterface;
use App\Libs\Role\Traits\PermissionTrait;
use Illuminate\Support\Facades\Config;
use Jenssegers\Mongodb\Model;
use Illuminate\Support\Str;
use App\Libs\Role\Exceptions\PermissionAlreadyExistsException;

class PermissionMongo extends Model
{
    /**
     * The database collection used by the model.
     *
     * @var string
     */
    protected $collection;

    protected $fillable = ['name', 'desc', 'slug'];

    /**
     * Creates a new instance of the model.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->collection = config('entrust.permissions_table');
    }

    public function createPermission( $name, $desc = null)
    {
        $slug = Str::slug($name);

        if ($this->getBySlug($slug)) {
            throw new PermissionAlreadyExistsException("Permission Already Exists!!", 1);
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->desc = $desc;
        $this->save();
        return $this;
    }


    public function getBySlug( $slug )
    {
        return $this->whereSlug($slug)->first();
    }

}
