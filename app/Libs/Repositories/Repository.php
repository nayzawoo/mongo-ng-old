<?php

namespace App\Libs\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Closure;

abstract class Repository
{
    protected $namespace = 'App\Models';

    /**
     * @var App
     */
    private $app;

    /**
     * @var
     */
    protected $model;

    /**
     * Data identifier
     **/
    protected $identifier  = '_id';

    /**
     * Constructor
     **/
    public function __construct()
    {
        $this->app = new App;
        $this->makeModel();
    }

    public function fill( $data )
    {
        $model = $this->model->create($data);
        $model->save();
        return $model;
    }

    public function paginate( $limit )
    {
        return $this->model->orderBy('desc', $this->identifier)->paginate($limit);
    }

    public function model()
    {
        return $this->model;
    }

    public function makeModel()
    {
        $class = "{$this->namespace}\\{$this->model}";
        $model = $this->app->make($class);
        if (!$model instanceof Model) {
            throw new Exception("Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function get()
    {
        return $this->model()->orderBy($this->identifier, 'DESC')->get();
    }

    public function getById($id)
    {
        $result = $this->model()->findOrFail($id);

        return $result->first();
    }

    public function forSelect($name, $isMethod = false)
    {
        $result = [];
        foreach ($this->model()->get() as $value) {
            if ($isMethod) {
                $result[$value->id] = trim($value->$name());
            } else {
                $result[$value->id] = trim($value->$name);
            }
        }
        return $result;
    }

    public function update( $data, $id, Closure $callback = null)
    {
        return $this->model()->findOrFail($id)->update($data);
    }

    public function findForEdit( $id )
    {
        // Disable Automatic Translate
        return $this->model
                ->disableTranslate()
                ->findOrFail($id);
    }

    public function fillable()
    {
        return $this->model->fillable;
    }

    /**
     * Handle dynamic calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (count($args)) {
            case 0:
                return $this->model->$method();

            case 1:
                return $this->model->$method($args[0]);

            case 2:
                return $this->model->$method($args[0], $args[1]);

            case 3:
                return $this->model->$method($args[0], $args[1], $args[2]);

            case 4:
                return $this->model->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$this->model, $method], $args);
        }
    }
}
