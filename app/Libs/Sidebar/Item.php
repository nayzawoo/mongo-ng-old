<?php namespace App\Libs\Sidebar;

use Illuminate\Support\Collection;

/**
 * Sidebar Item
 */
class Item
{
    protected $currentRoute;

    public function __construct()
    {
        $this->currentRoute = app('router')->currentRouteName();
    }

    public function format($item)
    {
        $item           = collect($item);
        $item->icon     = $item->get('icon');
        $item->name     = $item->get('name');
        $item->hasChild = $item->has('child');
        $item->url      = $this->getUrl($item);
        $item->active   = $this->isActive($item);
        return $item;
    }

    public function isActive(Collection $item)
    {
        // Parent
        if ($item->has("child")) {
            $child = $item->get("child");
            return in_array($this->currentRoute, array_pluck($child, 'route'));
        }

        // Child
        if (!$item->has("route")) {
            return false;
        }

        return $this->currentRoute === $item->get("route");
    }

    public function getUrl(Collection $item)
    {
        $route = $item->get('route');
        if ($route == null || $route == '#' || $route == '') {
            return 'javascript:;';
        }
        return route($route);
    }
}
