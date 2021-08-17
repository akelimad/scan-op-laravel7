@extends('admin.layouts.default')

@section('head')
{{ HTML::script('js/dropzone.js') }}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.manga.chapter.page.create', $manga, $chapter))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-book fa-fw"></i> {{ Lang::get('messages.admin.chapter.page.title') }}
                <div class="pull-right">
                    {{ link_to_route('admin.manga.chapter.show', Lang::get('messages.admin.chapter.page.back'), array('manga' => $manga->id, 'chapter' => $chapter->id), array('class' => 'btn btn-default btn-xs')); }}
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row" id="actions">
                    <div class="col-lg-7">
                        <!-- The fileinput-button span is used to style the file input field as button -->
                        @if(Entrust::hasRole('Admin') || (Auth::user()->id===$chapter->user->id && Entrust::can('add_chapter')))
                        <span class="btn btn-success fileinput-button dz-clickable">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>{{ Lang::get('messages.admin.chapter.page.add-images') }}</span>
                        </span>
                        <button class="btn btn-primary start" type="submit">
                            <i class="glyphicon glyphicon-upload"></i>
                            <span>{{ Lang::get('messages.admin.chapter.page.upload-save') }}</span>
                        </button>
                        @endif
                    </div>

                    <div class="col-lg-5">
                        <!-- The global file processing state -->
                        <span class="fileupload-process">
                            <div id="total-progress" class="progress progress-striped active" aria-valuenow="0" aria-valuemax="100" aria-valuemin="0" role="progressbar" style="opacity: 0;">
                                <div class="progress-bar progress-bar-success" data-dz-uploadprogress="" style="width: 100%;"></div>
                            </div>
                        </span>
                    </div>
                </div>

                <div class="table table-striped" id="previews">
                    <div id="template" class="row">
                        <!-- This is used as the file preview template -->
                        <div class="col-lg-4">
                            <span class="preview"><img data-dz-thumbnail /></span>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-5">
                                    <p class="name" data-dz-name></p>
                                </div>
                                <div class="col-lg-2">
                                    /
                                </div>
                                <div class="col-lg-5">
                                    <p class="size" data-dz-size></p>
                                </div>
                            </div>
                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                            </div>
                            <strong class="error text-danger" data-dz-errormessage></strong>
                        </div>
                        <div class="col-lg-4">
                            <button class="btn btn-primary start">
                                <i class="glyphicon glyphicon-upload"></i>
                                <span>{{ Lang::get('messages.admin.chapter.page.start') }}</span>
                            </button>
                            <button data-dz-remove class="btn btn-danger delete">
                                <i class="glyphicon glyphicon-trash"></i>
                                <span>{{ Lang::get('messages.admin.chapter.page.delete') }}</span>
                            </button>
                        </div>
                    </div>
                </div> 

                <script>
                    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                    var previewNode = document.querySelector("#template");
                    previewNode.id = "";
                    var previewTemplate = previewNode.parentNode.innerHTML;
                    previewNode.parentNode.removeChild(previewNode);

                    var imagesUploadZone = new Dropzone(".panel-body", {
                        url: "{{ action('PageController@store', array('manga' => $manga->id, 'chapter' => $chapter->id)) }}",
                        thumbnailWidth: 100,
                        thumbnailHeight: 100,
                        parallelUploads: 1,
                        acceptedFiles: 'image/*',
                        previewTemplate: previewTemplate,
                        autoQueue: false, // Make sure the files aren't queued until manually added
                        previewsContainer: "#previews", // Define the container to display the previews
                        clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
                    });

                    imagesUploadZone.on("addedfile", function (file) {
                        // Hookup the start button
                        file.previewElement.querySelector(".start").onclick = function () {
                            imagesUploadZone.enqueueFile(file);
                        };
                    });

                    // Update the total progress bar
                    imagesUploadZone.on("totaluploadprogress", function (progress) {
                        document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
                    });

                    imagesUploadZone.on("sending", function (file) {
                        // Show the total progress bar when upload starts
                        document.querySelector("#total-progress").style.opacity = "1";
                        // And disable the start button
                        file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
                    });

                    // Hide the total progress bar when nothing's uploading anymore
                    imagesUploadZone.on("queuecomplete", function (progress) {
                        document.querySelector("#total-progress").style.opacity = "0";
                        alert('Upload complete and page created, go back to the chapter page!');
                    });

                    // Setup the buttons for all transfers
                    // The "add files" button doesn't need to be setup because the config
                    // `clickable` has already been specified.
                    document.querySelector("#actions .start").onclick = function () {
                        imagesUploadZone.enqueueFiles(imagesUploadZone.getFilesWithStatus(Dropzone.ADDED));
                    };

                    /*imagesUploadZone.on("success", function(file, response) {
                     $.ajax({
                     url: "{{ action('ChapterController@show', array('manga' => $manga->id, 'chapter' => $chapter->id)) }}",
                     type: "GET"
                     });
                     $.post(
                     "{{ action('ChapterController@show', array('manga' => $manga->id, 'chapter' => $chapter->id)) }}");
                     });*/

                    imagesUploadZone.on("removedfile", function (file) {
                        deletefile(file);
                    });

                    function deletefile(value)
                    {
                        $.post(
                                "{{ action('FileUploadController@deleteImage') }}",
                                {manga: "{{ $manga->id }}", filename: value.name}
                        );
                    }
                </script>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop
