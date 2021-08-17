@section('menu')
<?php $menus = json_decode($settings['site.menu']); ?>
<div style="position:relative">
    <a class="navbar-brand" href="{{route('front.index')}}">
        <h1>{{$settings['site.name']}}</h1>
    </a>

    <ul class="menu boxed clearfix bg-image-home">
        <!-- custom menu -->
        @if(isset($menus->label) && count($menus->label)>0)
        @foreach($menus->label as $index => $menu)
        <li><a href="{{$menus->url[$index]}}">{{$menu}}</a></li>
        @endforeach
        @endif
        @if(isset($menus->adv_search))
        <li><a href="{{route('front.advSearch')}}"><i class="glyphicon glyphicon-search"></i>{{ Lang::get('messages.front.home.adv-search') }}</a></li>
        @endif
        @if(isset($menus->random))
        <li><a href="{{route('front.manga.random')}}"><i class="menu-icon menu-icon-7"></i>{{ Lang::get('messages.front.menu.random-manga') }}</a></li>
        @endif
        @if(isset($menus->news))
        <li><a href="{{route('front.manga.latestNews')}}"><i class="glyphicon glyphicon-edit"></i>{{ Lang::get('messages.front.home.news') }}</a></li>
        @endif
        @if(isset($menus->latest_release))
        <li><a href="{{route('front.manga.latestRelease')}}"><i class="glyphicon glyphicon-th-list"></i>{{ Lang::get('messages.front.home.latest-release') }}</a></li>
        @endif
        @if(isset($menus->mangalist))
        <li><a href="{{route('front.manga.list')}}"><i class="menu-icon menu-icon-5"></i>{{ Lang::get('messages.front.menu.manga-list') }}</a></li>
        @endif
        @if(isset($menus->home))
        <li><a href="{{route('front.index')}}"><i class="menu-icon menu-icon-1"></i>{{ Lang::get('messages.front.menu.home') }}</a></li>
        @endif
    </ul>
</div>
@stop

