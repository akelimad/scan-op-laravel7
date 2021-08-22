<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        @yield('head')

            {{ HTML::style('assets/css/bootstrap.min.css') }}
            {{ HTML::style('assets/css/admin/main.css') }}

            {{ HTML::script('assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js') }}
            {{ HTML::script('assets/js/vendor/jquery-1.11.0.min.js') }}
            {{ HTML::script('assets/js/vendor/bootstrap.min.js') }}

        @if(Config::get('orientation') === 'rtl')
            {{ HTML::style('assets/css/bootstrap-rtl.min.css') }}
            @endif

            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    </head>
    <body>
        <div class="container">
            @yield('content')
		    </div>
    </body>
</html>
