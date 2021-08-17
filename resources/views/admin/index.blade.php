@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.index'))

@section('content')
<div class="row">
    <div class="col-md-12">
        @if (Session::has('updateSuccess'))
        <div class="alert text-center alert-info ">
            {{ Session::get('updateSuccess') }}
        </div>
        @endif
    </div>
    <div class="col-lg-12">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <i class="fa fa-star fa-fw"></i> {{ Lang::get('messages.admin.dashboard.hotmanga') }}
                @if(Entrust::can('manage_hotmanga'))
                <div class="pull-right">
                    {{ link_to_route('admin.manga.hot', Lang::get('messages.admin.dashboard.edit-hotlist'), null, array('class' => 'btn btn-primary btn-xs', 'role' => 'button')) }}
                </div>
                @endif
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                @if (count($hotmanga)>0)
                <ul class="dash_hot">
                    @foreach ($hotmanga as $manga)
                    <li>
                        <a href='{{ url("/admin/manga/{$manga->id}") }}'>
                            @if ($manga->cover)
                            <img width="100" height="100" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_thumb.jpg")}}' alt='{{ $manga->name }}' />
                            @else
                            <img width="100" height="100" src='images/no-image.png' alt='{{ $manga->name }}' />
                            @endif
                            <div class="caption">
                                <h6>{{ $manga->name }}</h6>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="center-block">
                    <p>{{ Lang::get('messages.admin.dashboard.hotlist-empty') }}</p>
                </div>
                @endif
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-pencil-square-o fa-fw"></i> {{ Lang::get('messages.admin.dashboard.latest-added-manga') }}
                @if(Entrust::can('add_manga'))
                <div class="pull-right">
                    {{ link_to_route('admin.manga.create', Lang::get('messages.admin.dashboard.create-manga'), null, array('class' => 'btn btn-primary btn-xs', 'role' => 'button')) }}
                </div>
                @endif
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                @if (count($mangas)>0)
                <div class="list-group">
                    @foreach ($mangas as $manga)
                    <div class="list-group-item">
                        <div class="media-left">
                            <a href='@if(Entrust::can("view_manga") || Entrust::can("add_manga") || Entrust::can("edit_manga") || Entrust::can("delete_manga")) {{url("/admin/manga/{$manga->id}")}} @endif'>                                
                                @if ($manga->cover)
                                <img width="50" height="50" class="media-object" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_thumb.jpg")}}' alt='{{ $manga->name }}' />
                                @else
                                <img width="50" height="50" src='images/no-image.png' alt='{{ $manga->name }}' />
                                @endif
                            </a>
                        </div>
                        <div class="media-body">
                            <h5 class="media-heading">
                                @if(Entrust::can('view_manga') || Entrust::can('add_manga') || Entrust::can('edit_manga') || Entrust::can('delete_manga'))
                                {{ link_to("/admin/manga/{$manga->id}", $manga->name) }}
                                @else
                                {{ $manga->name }}
                                @endif
                            </h5>
                            <div class="pull-right">
                                <i class="fa fa-user"></i>
                                <small>{{ $manga->user->username }}</small>
                            </div>
                            <div>
                                <i class="fa fa-calendar-o"></i>
                                <small>{{ App::make("HelperController")->formateCreationDate($manga->created_at) }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if(Entrust::can('view_manga') || Entrust::can('add_manga') || Entrust::can('edit_manga') || Entrust::can('delete_manga'))
                {{ link_to_route('admin.manga.index', Lang::get('messages.admin.dashboard.view-all-manga'), null, array('class' => 'btn btn-default btn-block')) }}
                @endif
                @else
                <div class="center-block">
                    <p>{{ Lang::get('messages.admin.dashboard.no-manga') }}</p>
                </div>
                @endif
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-book fa-fw"></i> {{ Lang::get('messages.admin.dashboard.latest-added-chapter') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                @if (count($chapters)>0)
                <div class="list-group">
                    @foreach ($chapters as $chapter)
                    <a href='@if(Entrust::can("view_chapter") || Entrust::can("add_chapter") || Entrust::can("edit_chapter") || Entrust::can("delete_chapter")) {{ url("/admin/manga/{$chapter->manga_id}/chapter/{$chapter->id}") }} @endif' class="list-group-item">
                        <div class="pull-right">
                            <i class="fa fa-user"></i>
                            <small>{{ $chapter->username }}</small>
                        </div>
                        <h5 class="list-group-item-heading">
                            <b>{{ $chapter->manga_name. " #". $chapter->number }}</b>
                        </h5>
                        <div class="pull-right">
                            <i class="fa fa-calendar-o"></i>
                            <small>{{ App::make("HelperController")->formateCreationDate($chapter->created_at) }}</small>
                        </div>
                        <p class="list-group-item-text"><em>{{ $chapter->name }}</em></p>

                    </a>
                    @endforeach
                </div>
                @else
                <div class="center-block">
                    <p>{{ Lang::get('messages.admin.dashboard.no-chapter') }}</p>
                </div>
                @endif
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop