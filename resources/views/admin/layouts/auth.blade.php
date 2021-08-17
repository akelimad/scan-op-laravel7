<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

		@yield('head')
		
        {{ HTML::style('css/bootstrap.min.css') }}
		{{ HTML::style('css/admin/main.css') }}
		
        {{ HTML::script('js/vendor/modernizr-2.6.2-respond-1.1.0.min.js') }}
        {{ HTML::script('js/vendor/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/vendor/bootstrap.min.js') }}

		@if(Config::get('orientation') === 'rtl')
        {{ HTML::style('css/bootstrap-rtl.min.css') }}
        @endif
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="row">
				@yield('content')
            </div>
		</div>
    </body>
</html>
