@section('menu')
<?php $menus = json_decode($settings['site.menu']); ?>
<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-menu">
            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button> 
        <h1 style="margin: 0px;"><a class="navbar-brand" href="{{route('front.index')}}">{{$settings['site.name']}}</a></h1>
    </div>

    <div class="collapse navbar-collapse" id="navbar-menu">
        @if(Config::get('subscribe'))
        <ul class="nav navbar-nav @if(Config::get('orientation') === 'rtl') navbar-left @else navbar-right @endif">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> @if(Confide::user()){{Confide::user()->username}}@endif<span class="caret"></span></a>
                <ul class="dropdown-menu profil-menu">
                    @if(!Confide::user())
                    <li>
                        <a href="{{action('UsersController@create')}}">
                            <i class="fa fa-pencil-square-o"></i> {{Lang::get('messages.front.home.subscribe')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{action('UsersController@login')}}">
                            <i class="fa fa-sign-in"></i> {{Lang::get('messages.front.home.login')}}
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{route('user.profil.index', Confide::user()->username)}}">
                            <i class="fa fa-user"></i> {{Lang::get('messages.front.myprofil.my-profil')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{route('bookmark.index')}}">
                            <i class="fa fa-heart"></i> {{Lang::get('messages.front.bookmarks.title')}}
                        </a>
                    </li>
                    @if(Entrust::can('add_manga') || Entrust::can('add_chapter') || Entrust::can('manage_posts'))
                    <li role="separator" class="divider"></li>
                    @endif
                    @if(Entrust::can('add_manga') || Entrust::can('add_chapter'))
                    <li>
                        <a href="{{route('admin.manga.index')}}">
                            <i class="fa fa-plus"></i> {{Lang::get('messages.front.myprofil.add-manga-chapter')}}
                        </a>
                    </li>
                    @endif
                    @if(Entrust::can('manage_posts'))
                    <li>
                        <a href="{{route('admin.posts.index')}}">
                            <i class="fa fa-plus"></i> {{Lang::get('messages.front.myprofil.add-post')}}
                        </a>
                    </li>
                    @endif
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{route('admin.index')}}">
                            <i class="fa fa-cogs"></i> {{Lang::get('messages.front.home.dashboard')}}
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{action('UsersController@logout')}}">
                            <i class="fa fa-sign-out"></i> {{Lang::get('messages.front.home.logout')}}
                        </a>
                    </li>  
                    @endif
                </ul>
            </li>
        </ul>
        @endif
        <ul class="nav navbar-nav @if(Config::get('orientation') === 'rtl') navbar-left @else navbar-right @endif">
            <li class="search dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i></a>
                <div class="dropdown-menu">
                    <form class="navbar-form">
                        <div class="navbar-form @if(Config::get('orientation') === 'rtl') navbar-left @else navbar-right @endif" role="search">
                            <div class="form-group">
                                <input id="autocomplete" class="form-control" type="text" placeholder="{{Lang::get('messages.front.menu.search')}}" style="border-radius:0;"/>
                            </div>
                        </div>
                    </form>
                </div>
            </li>
        </ul>

        <!-- menu -->
        <ul class="nav navbar-nav @if(Config::get('orientation') === 'rtl') navbar-left @else navbar-right @endif">
            @if(isset($menus->home))
            <li>{{link_to_route('front.index', Lang::get('messages.front.menu.home'))}}</li>
            @endif
            @if(isset($menus->mangalist))
            <li>{{link_to_route('front.manga.list', Lang::get('messages.front.menu.manga-list'))}}</li>
            @endif
            @if(isset($menus->latest_release))
            <li>{{link_to_route('front.manga.latestRelease', Lang::get('messages.front.home.latest-release'))}}</li>
            @endif
            @if(isset($menus->news))
            <li>{{link_to_route('front.manga.latestNews', Lang::get('messages.front.home.news'))}}</li>
            @endif
            @if(isset($menus->random))
            <li>{{link_to_route('front.manga.random', Lang::get('messages.front.menu.random-manga'))}}</li>
            @endif
            @if(isset($menus->adv_search))
            <li>{{link_to_route('front.advSearch', Lang::get('messages.front.home.adv-search'))}}</li>
            @endif
            <!-- custom menu -->
            @if(isset($menus->label) && count($menus->label)>0)
            @foreach($menus->label as $index => $menu)
            <li><a href="{{$menus->url[$index]}}">{{$menu}}</a></li>
            @endforeach
            @endif
        </ul>
    </div>
</nav>

<style>
    .searching {
        background-image: url('{{asset("images/ajax-loader.gif")}}');
        background-position: 95% 50%;
        background-repeat: no-repeat;
    }
</style>
<script>
    $('#autocomplete').autocomplete({
        serviceUrl: "{{ action('FrontController@search') }}",
        onSearchStart: function (query) {
            $('#autocomplete').addClass('searching');
        },
        onSearchComplete: function (query, suggestions) {
            $('#autocomplete').removeClass('searching');
        },
        onSelect: function (suggestion) {
            showURL = "{{ route('front.manga.show', 'SELECTION') }}";
            window.location.href = showURL.replace('SELECTION', suggestion.data);
        }
    });
</script>
@stop

