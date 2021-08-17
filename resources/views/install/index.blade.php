<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>my Manga Reader CMS - Installation</title>

        {{ HTML::style('css/bootstrap.min.css') }}

        {{ HTML::script('js/vendor/jquery-1.11.0.min.js') }}
        {{ HTML::script('js/vendor/bootstrap.min.js') }}

        <script type="text/javascript">
            $(document).ready(function() {
                $('form').submit(function(e) {
                    e.preventDefault();

                    $('#startInstallingBtn').addClass('disabled');
                    $('form label[class="text-danger"]').remove();
                    $('#resultPanel').hide();
                    $('#waiting').show();

                    var formData = new FormData();
                    formData.append('host', $('#host').val());
                    formData.append('name', $('#name').val());
                    formData.append('username', $('#username').val());
                    formData.append('password', $('#password').val());

                    $.ajax({
                        method: 'POST',
                        url: "{{ action('InstallerController@startInstall') }}",
                        processData: false,
                        contentType: false,
                        cache: false,
                        dataType: 'json',
                        data: formData,
                        success: function(response) {
                            $('#waiting').hide();

                            if (!response.success) {
                                $('#startInstallingBtn').removeClass('disabled');

                                $.each(response.errors, function(index, error) {
                                    $('#' + index).after('<label class="text-danger" for="host">' + error + '</label>');
                                });
                            } else {
                                $('#errorPanel').hide();
                                $('#resultPanel').show();
                                $('#successPanel').show();
                                $('#formInstall').hide();
                            }
                        },
                        error: function(xhr, status, error) {
                            $('#waiting').hide();
                            $('#startInstallingBtn').removeClass('disabled');
                            $('#resultPanel').show();

                            var err = xhr.responseJSON;
                            if (typeof err !== "undefined") {
                                $.each(err.errors, function(index, error) {
                                    $('#errorPanel p').remove();
                                    $('#errorPanel').show();
                                    $('#errorPanel').append('<p>' + error + '</p>');
                                });
                            }
                        }
                    });
                });
            });
        </script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div style="margin-top: 20px;"></div>
            <div id="resultPanel" class="row" style="display: none;">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="errorPanel" class="alert alert-danger" style="display: none;"></div>

                            <div id="successPanel" style="display: none;">
                                <div class="alert alert-info">
                                    <p>Congratulation! 'my Manga Reader' CMS was installed successfully!</p>
                                </div>

                                <ul>
                                    <li>Please connect to your <a href="{{URL::to('admin/login')}}">Dashboard</a> to change your password, and start adding your Manga Scan.</li>
                                    <li>The default login/password is <b>admin/admin</b></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="formInstall" class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="login-panel panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Installation of 'my Manga Reader' CMS!</h3>
                        </div>
                        <div class="panel-body">
                            {{ Form::open() }}
                            <fieldset>
                                <div class="form-group">
                                    {{Form::text('host','', array('id' => 'host', 'class' => 'form-control', 'placeholder' => 'Database Host', 'autofocus'=>'autofocus'))}}
                                </div>
                                <div class="form-group">
                                    {{Form::text('name','', array('id' => 'name', 'class' => 'form-control', 'placeholder' => 'Database Name'))}}
                                </div> 
                                <div class="form-group">
                                    {{Form::text('username','', array('id' => 'username', 'class' => 'form-control', 'placeholder' => 'Database Username'))}}
                                </div> 
                                <div class="form-group">
                                    <input id="password" type="password" name="password" class="form-control" placeholder="Database Password"/>
                                </div>

                                {{ Form::submit('Install', array('id' => 'startInstallingBtn', 'class' => 'btn btn-lg btn-success btn-block')) }}
                            </fieldset>
                            {{ Form::close() }}

                            <div id="waiting" style="display: none; margin-top: 20px; margin-bottom: 10px;">
                                <center>
                                    <img src="{{ asset('/images/ajax-loader.gif') }}" alt='Installing...'/>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </body>
</html>