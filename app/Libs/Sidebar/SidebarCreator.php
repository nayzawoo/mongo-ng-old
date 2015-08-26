<?php namespace App\Libs\Sidebar;

use App\Libs\Sidebar\Sidebar;

class SidebarCreator
{
    /**
     * @var YourSidebar
     */
    protected $sidebar;

    /**
     * @var SidebarRenderer
     */
    protected $renderer;

    /**
     * @param YourSidebar $sidebar
     * @param SidebarRenderer       $renderer
     */
    public function __construct()
    {
        $this->sidebar = new Sidebar;
    }

    /**
     * @param $view
     */
    public function create($view)
    {
        $view->sidebar = $this->sidebar->get();
    }
}