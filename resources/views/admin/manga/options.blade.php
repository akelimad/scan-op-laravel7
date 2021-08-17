@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.manga.options'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-gear fa-fw"></i> {{ Lang::get('messages.admin.layout.options') }}
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

                        {{ Form::open(array('route' => 'admin.manga.options.save', 'role' => 'form')) }}
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="allow_duplicate_chapter" value="1" 
                                       <?php if (isset($mangaOptions->allow_duplicate_chapter) && $mangaOptions->allow_duplicate_chapter == '1') { ?> checked="checked" <?php } ?> >
                                {{Lang::get('messages.admin.settings.manga.allow-duplicate-chapter')}}
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="allow_download_chapter" value="1" 
                                       <?php if (isset($mangaOptions->allow_download_chapter) && $mangaOptions->allow_download_chapter == '1') { ?> checked="checked" <?php } ?>>
                                {{Lang::get('messages.admin.settings.manga.allow-download-chapter')}}
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="show_contributer_pseudo" value="1" 
                                       <?php if (isset($mangaOptions->show_contributer_pseudo) && $mangaOptions->show_contributer_pseudo == '1') { ?> checked="checked" <?php } ?>>
                                {{Lang::get('messages.admin.settings.manga.show-contributer-pseudo')}}
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="show_chapters_volume" value="1" 
                                       <?php if (isset($mangaOptions->show_chapters_volume) && $mangaOptions->show_chapters_volume == '1') { ?> checked="checked" <?php } ?>>
                                {{Lang::get('messages.admin.settings.manga.show-chapters-volume')}}
                            </label>
                        </div>

                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.admin.settings.update'), ['class' => 'btn btn-primary']) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop