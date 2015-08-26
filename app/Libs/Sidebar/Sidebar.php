<?php namespace App\Libs\Sidebar;

use App\Libs\Sidebar\Item;
use App\Libs\Sidebar\Renderer;

/**
 * Sidebar Helper
 */

class Sidebar
{
    protected $items;

    /**
     * App\Libs\Sidebar\Item
     */
    protected $item;

    /**
     * App\Libs\Sidebar\Renderer
     */
    protected $renderer;

    /**
     * Rendered Template
     * @var [type]
     */
    protected $template = '';

    public function __construct()
    {
        $this->renderer = new Renderer;
        $this->item     = new Item;
        $this->items    = config('sidebar.items');
    }

    public function create()
    {
        foreach ($this->items as $item) {
            $this->mergeTemplate($this->createItem($item));
        }
    }

    public function createItem(array $item)
    {
        $subitems = null;

        if (isset($item['child'])) {
            $subitems = $this->createSubitem($item['child']);
        }

        $data = $this->format($item);
        return $this->renderer->renderItem($data, $subitems);
    }

    public function createSubitem(array $subitems)
    {
        $tmp = '';
        foreach ($subitems as $subitem) {
            $data = $this->format($subitem);
            $tmp .= $this->renderer->renderSubitem($data);
        }
        return $this->renderer->wrapSubitems($tmp);
    }

    public function format(array $data)
    {
        return $this->item->format($data);
    }

    public function mergeTemplate($template)
    {
        $this->template .= $template;
    }

    public function get()
    {
        $this->create();
        return $this->template;
    }
}
