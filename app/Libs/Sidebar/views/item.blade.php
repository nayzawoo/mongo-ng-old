<li class="start {{$menu->active ? 'active open' : ''}}">
    <a href="{{$menu->url}}">
    <i class="{{$menu->icon}}"></i>
        <span class="title">{{$menu->name}}</span>
    @if ($menu->hasChild)
        <span class="arrow {{$menu->active ? 'open' : ''}}"></span>
    @endif
    </a>
    {!!$child!!}
</li>