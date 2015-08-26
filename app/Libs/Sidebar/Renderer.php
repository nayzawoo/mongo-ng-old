<?php namespace App\Libs\Sidebar;

/**
 * Renderer
 */
class Renderer
{
    protected $factory;

    public function __construct()
    {
        $this->factory = app('Illuminate\Contracts\View\Factory');
    }

    public function renderItem($data, $child)
    {
        return $this->make('item', [
            'child' => $child,
            'menu'  => $data,
        ]);
    }

    public function wrapSubitems($data)
    {
        return $this->make('subitem-wrapper', [
            'menu' => $data,
        ]);
    }

    public function renderSubitem($data)
    {
        return $this->make('subitem', [
            'menu' => $data,
        ]);
    }

    public function make($view, $data)
    {
        return $this->factory->make(config('sidebar.' . $view), $data)->render();
    }
}
