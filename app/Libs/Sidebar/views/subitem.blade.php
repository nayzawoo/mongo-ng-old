<li class="{!! $menu->active ? 'active' : '';!!}">
    <a href="{{ $menu->url}}">
    <i class="{{$menu->icon}}"></i>
    {{$menu->name}}
    </a>
</li>