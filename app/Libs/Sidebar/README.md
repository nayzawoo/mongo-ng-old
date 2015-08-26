### Sidebar

1) Create Config

config/sidebar.php

--------------
2) Create Views

Sidebar/views

---------------
3) Bind View

```
\View::creator(
    'backend.partials.left-sidebar',
    'App\Libs\Sidebar\SidebarCreator'
);
```

-------------
4) Render

{!!$sidebar!!}