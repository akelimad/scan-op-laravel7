<!doctype html>
<!--[if lt IE 8 ]><html lang="{{ App::getLocale() }}" class="ie7"> <![endif]-->
<!--[if IE 8 ]><html lang="{{ App::getLocale() }}" class="ie8"> <![endif]-->
<!--[if IE 9 ]><html lang="{{ App::getLocale() }}" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="{{ App::getLocale() }}"> <!--<![endif]-->
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title>@yield('title')</title>
        <meta name="description" content="@yield('description')"/>
        <meta name="keywords" content="@yield('keywords')"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>

        @if(!is_null($settings['seo.google.webmaster']) || "" !== $settings['seo.google.webmaster'])        
        <meta name="google-site-verification" content="{{$settings['seo.google.webmaster']}}" />
        @endif

        <link rel="canonical" href="{{route('front.index')}}"/>

        {{ HTML::style('css/bootswatch/'.$variation.'/bootstrap.min.css') }}
        {{ HTML::style('css/style.css') }}
        {{ HTML::style('css/font-awesome.min.css') }}

        {{ HTML::script('js/vendor/modernizr-2.6.2-respond-1.1.0.min.js') }}
        {{ HTML::script('js/vendor/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/vendor/bootstrap.min.js') }}
        {{ HTML::script('js/vendor/jquery.autocomplete.min.js') }}
        {{ HTML::script('js/main.js') }}

        @if(Config::get('orientation') === 'rtl')
        {{ HTML::style('css/bootstrap-rtl.min.css') }}
        {{ HTML::style('css/rtl.css') }}
        @endif

        @yield('header')

        <!--[if lt IE 9]>
        {{ HTML::script('js/vendor/html5shiv.js') }}
        {{ HTML::script('js/vendor/respond.min.js') }}
        <![endif]-->
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        @if(!is_null($settings['seo.google.analytics']) || "" !== $settings['seo.google.analytics'])
        @include('front.analyticstracking')
        @endif

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Website Menu -->
                    @yield('menu')
                    <!--/ Website Menu -->
                </div>
            </div>

            <!-- row -->
            <div class="row">
                <div class="col-sm-12">
                    @yield('allpage')
                </div>
            </div>
            <!--/ row -->

            <!-- row -->
            <div class="row"> 
                <div class="col-sm-4 col-sm-push-8">
                    @yield('sidebar')
                </div>
                <div class="col-sm-8 col-sm-pull-4">
                    @yield('hotmanga')

                    <div class="col-sm-12">
                        @yield('content')
                    </div>
                </div>
            </div>
            <!--/ row -->

            <?php $menus = json_decode($settings['site.menu']); ?>
            <!-- row -->
            <div class="row"> 
                <div class="col-sm-12">
                    <div class="row">
                        <div class="manga-footer">
                            <ul class="@if(Config::get('orientation') === 'rtl') pull-left @else pull-right @endif">
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
                                <!-- custom menu -->
                                @if(isset($menus->label) && count($menus->label)>0)
                                @foreach($menus->label as $index => $menu)
                                <li><a href="{{$menus->url[$index]}}">{{$menu}}</a></li>
                                @endforeach
                                @endif
                            </ul>
                            &copy;&nbsp;<?php echo date("Y") ?>&nbsp;
                            <a href="{{route('front.index')}}">{{$settings['site.name']}}</a>
                            &nbsp;
                            <a href="{{URL::to('/contact-us')}}" title="{{Lang::get('messages.front.home.contact-us')}}"><i class="fa fa-envelope-square"></i></a>
                            &nbsp;
                            <a href="{{URL::to('/feed')}}" title="{{Lang::get('messages.front.home.rss-feed')}}" style="color: #FF9900"><i class="fa fa-rss-square"></i></a>
                            <div style="text-align: center; font-size: 10px; position: absolute; left: 0px; right: 0px; bottom: 5px; visibility: hidden;">
                                Powered by <a href="http://getcyberworks.com/works/mangareadercms/demo/" title="my Manga Reader CMS">my Manga Reader CMS</a> v1.8
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function () {
                    var url = window.location.href;
                    var element = $('ul.nav a').filter(function () {
                        if (url.charAt(url.length - 1) == '/') {
                            url = url.substring(0, url.length - 1);
                        }

                        return this.href == url;
                    }).parent();

                    if (element.is('li')) {
                        element.addClass('active');
                    }
                });
            </script>
        </div>
    </body>
</html>