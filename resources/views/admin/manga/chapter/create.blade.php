@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.manga.chapter.create', $manga))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-book fa-fw"></i> {{ Lang::get('messages.admin.chapter.create.title') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                {{ Form::open(array('route' => 'admin.manga.chapter.store')) }}
                <div class="form-group">
                    {{Form::label('name', Lang::get('messages.admin.chapter.create.chapter-name'))}}
                    {{Form::text('name', '', array('class' => 'form-control'))}}
                    {{ $errors->first('name', '<label class="error" for="name">:message</label>') }}
                </div>

                <div class="form-group">
                    {{Form::label('number', Lang::get('messages.admin.chapter.create.number'))}}
                    {{Form::text('number', '', array('class' => 'form-control'))}}
                    {{ $errors->first('number', '<label class="error" for="number">:message</label>') }}
                </div>

                <div class="form-group">
                    {{Form::label('slug', Lang::get('messages.admin.chapter.create.slug'))}}
                    {{Form::text('slug', '', array('class' => 'form-control', 'placeholder' => Lang::get('messages.admin.chapter.create.slug-placeholder')))}}
                    {{ $errors->first('slug', '<label class="error" for="slug">:message</label>') }}
                </div>              

                <div class="form-group">
                    {{Form::label('volume', Lang::get('messages.admin.chapter.create.volume'))}}
                    {{Form::text('volume', '', array('class' => 'form-control'))}}
                </div>


                <div class="actionBtn">
                    {{ link_to_route('admin.manga.show', Lang::get('messages.admin.chapter.back'), $manga->id, array('class' => 'btn btn-default btn-xs')); }}

                    @if(Entrust::can('add_chapter'))
                    {{Form::submit(Lang::get('messages.admin.chapter.create.create-chapter'), array('class' => 'btn btn-primary btn-xs'))}}
                    {{Form::hidden('mangaId', $manga->id, array('id' => 'mangaId'))}}
                    @endif
                </div>
                {{ Form::close() }}
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop
