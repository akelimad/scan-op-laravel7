@extends('front.layouts.colorful')

@section('title')
{{$settings['seo.title']}} | 
@stop

@section('header')
{{ HTML::script('js/dropzone.js') }}
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<div class="page-header">
    <h3>
        {{ Lang::get('messages.front.myprofil.edit-my-profil')}}
    </h3>
</div>

<div class="row" style="background-color: #fff; padding: 20px 0; margin: 0;">
    <div class="col-xs-12 col-sm-4">
        <div id="coverContainer">
            <div class="coverWrapper">
                <div class="previewWrapper">
                    @if($user->avatar == 1)
                    <img class="img-responsive img-rounded" src='{{asset("uploads/users/{$user->id}/avatar.jpg")}}' alt='{{$user->avatar}}'>
                    @else
                    <img width="200" height="200" class="placeholder" src="{{asset('uploads/users/placeholder.png')}}" alt="avatar placeholder"/>
                    @endif
                    <div id="previews">
                        <div id="previewTemplate">
                            <div class="dz-preview dz-file-preview">
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                    <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="uploadBtn">
                    <span class="btn btn-success fileinput-upload btn-xs dz-clickable ">
                        <i class="fa fa-plus"></i>
                        <span>{{ Lang::get('messages.front.myprofil.upload-avatar') }}</span>
                    </span>
                    <span class="btn btn-danger fileinput-remove btn-xs data-dz-remove disabled">
                        <i class="fa fa-times"></i>
                        <span>{{ Lang::get('messages.front.myprofil.delete-avatar') }}</span>
                    </span>
                </div>

                <div class="dz-error-message" style="color: red"></div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-user fa-fw"></i> {{ Lang::get('messages.front.myprofil.my-profil')}}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        @if (Session::has('updateSuccess'))
                        <div class="alert text-center alert-info ">
                            {{ Session::get('updateSuccess') }}
                        </div>
                        @endif

                        {{ Form::open(array('route' => array('user.profil.save', $user->id) , 'role' => 'form')) }}
                        <div class="form-group">
                            {{ Form::label('name', Lang::get('messages.admin.settings.profile.name')) }}
                            {{ Form::text('name', $user->name, array('class' => 'form-control')) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('username', Lang::get('messages.admin.settings.profile.username')) }}
                            {{ Form::text('username', $user->username, array('class' => 'form-control')) }}
                            {{ $errors->first('username', '<label class="error" for="username">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('password', Lang::get('messages.admin.settings.profile.pwd')) }}
                            {{ Form::password('password', array('class' => 'form-control')) }}
                            {{ $errors->first('password', '<label class="error" for="password">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', Lang::get('messages.admin.settings.profile.email')) }}
                            {{ Form::text('email', $user->email, ['class' => 'form-control']) }}
                            {{ $errors->first('email', '<label class="error" for="email">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.front.myprofil.edit'), ['class' => 'btn-primary']) }}
                            {{Form::hidden('cover', '', array('id' => 'mangacover'))}}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        @if (!is_null($user->avatar))
            $("span.fileinput-remove").removeClass("disabled");
            $("span.fileinput-upload").addClass("disabled");
            $('#mangacover').val('{{asset("uploads/users/{$user->id}/avatar.jpg")}}');

            $("span.fileinput-remove").off().on("click", function() {
                $('.previewWrapper').find('img').remove();
                $('.previewWrapper').append('<img width="200" height="200" class="placeholder" src="{{asset("uploads/users/placeholder.png")}}" alt="avatar placeholder"/>');
                $("span.fileinput-upload").removeClass("disabled");
                $("span.fileinput-remove").addClass("disabled");
                $('#mangacover').val("");
            });
        @else if ($('#mangacover').val().length) {
            $(".previewWrapper .placeholder").remove();
            $("span.fileinput-upload").addClass("disabled");
            $("span.fileinput-remove").removeClass("disabled");
            $('.previewWrapper').append("<img class='img-responsive' width='200' height='200' src='" + $('#mangacover').val() + "' />");
            $("span.fileinput-remove").off().on("click", function() {
                deletefile($('#mangacover').val().replace(/^.*[\\\/]/, ''));
            });
        }
        @endif
    });
    
    function deletefile(value) {
    $.post(
        "{{ action('FileUploadController@deleteAvatar') }}",
        {filename: value}, function() {
            $('.previewWrapper').find('img').remove();
            $('.previewWrapper').append('<img width="200" height="200" class="placeholder" src="{{asset("uploads/users/placeholder.png")}}" alt="avatar placeholder"/>');
            $("span.fileinput-upload").removeClass("disabled");
            $("span.fileinput-remove").addClass("disabled");
            $('#mangacover').val("");
        });
    }

    // Get the template HTML and remove it from the document
    var previewTemplate = $('#previews').html();
    $('#previewTemplate').remove();
    var myDropzone = new Dropzone("#coverContainer", {
        url: "{{ action('FileUploadController@uploadAvatar') }}",
        thumbnailWidth: 200,
        thumbnailHeight: 200,
        acceptedFiles: 'image/*',
        previewTemplate: previewTemplate,
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-upload" // Define the element that should be used as click trigger to select files.
    });
    
    myDropzone.on("sending", function(file) {
        $(".previewWrapper .placeholder").remove();
        $("span.fileinput-upload").addClass("disabled");
    });
    
    myDropzone.on("success", function(file, response) {
        $('#previewTemplate').remove();
        $('.previewWrapper').append("<img class='img-responsive' width='200' height='200' src='" + response.result + "' />");
        $('#mangacover').val(response.result);
        $("span.fileinput-remove").removeClass("disabled");
        $("span.fileinput-remove").off().on("click", function() {
            @if (!is_null($user->avatar))
                $('.previewWrapper').find('img').remove();
                $('.previewWrapper').append('<img width="200" height="200" class="placeholder" src="{{asset("uploads/users/placeholder.png")}}" alt="avatar placeholder"/>');
                $("span.fileinput-upload").removeClass("disabled");
                $("span.fileinput-remove").addClass("disabled");
                $('#mangacover').val("");
            @else
                deletefile($('#mangacover').val().replace(/^.*[\\\/]/, ''));
            @endif
        });
    });
    
    myDropzone.on("error", function(file, response) {
        $('.dz-error-message').html(response.error.type + ': ' + response.error.message);
    });
</script>
@stop
