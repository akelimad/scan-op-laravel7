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
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Chewy" type="text/css" />

        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/colorful/style.css') }}

        {{ HTML::script('js/vendor/modernizr-2.6.2-respond-1.1.0.min.js') }}
        {{ HTML::script('js/vendor/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/vendor/bootstrap.min.js') }}
	{{ HTML::script('js/vendor/jquery.autocomplete.min.js') }}
        {{ HTML::script('js/colorful/main.js') }}

        <!-- CarouFredSel  -->
        {{ HTML::script('js/colorful/jquery.carouFredSel-6.2.1-packed.js') }}
        {{ HTML::script('js/colorful/jquery.touchSwipe.min.js') }}

        <!-- Responsive Menu -->
        {{ HTML::script('js/colorful/jquery.slicknav.min.js') }}
        {{ HTML::style('css/colorful/slicknav.css') }}

        @if(Config::get('orientation') === 'rtl')
        {{ HTML::style('css/bootstrap-rtl.min.css') }}
        {{ HTML::style('css/rtl.css') }}
        @endif
        
        <script>
            $(document).ready(function() {
                $('.menu').slicknav();
            });
        </script>

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
        
        <div class="mobile_sitename">
            <h1>{{$settings['site.name']}}</h1>
        </div>
        <div class="body-wrap">
            <div class="content">
                <!--container-->
                <div class="container">
                    <!-- row -->
                    <div class="row">
                        <div class="col-sm-12">
                            <!-- Website Menu -->
                            @yield('menu')
                            <!--/ Website Menu -->
                        </div>
                    </div>
                    <!--/ row -->

                    <!-- row -->
                    <div class="row">
                        <div class="col-sm-12">
                            <!-- Widget Hot Manga -->
                            @yield('hotmanga')
                            <!--/ Widget Hot Manga -->
                        </div>
                    </div>
                    <!--/ row -->

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
                            <!-- Widget Manga TimeLine -->
                            @yield('content')
                            <!-- Widget Manga TimeLine -->
                        </div>
                    </div>
                    <!--/ row -->

                    <script>
                        $(document).ready(function() {
                            var url = window.location.href;
                            var element = $('ul.nav a').filter(function() {
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
                <!--/ container -->
                
                <div style="text-align: center; font-size: 10px; position: absolute; left: 0px; right: 0px; bottom: 5px; visibility: hidden;">
                    Powered by <a href="http://getcyberworks.com/works/mangareadercms/demo/" title="my Manga Reader CMS">my Manga Reader CMS</a> v1.8
                </div>
            </div>
        </div>
    </body>
</html>
