@extends('admin.layouts.default')

@section('head')
<script>
    checked = Array();
    $(document).ready(function () {
        $('.panel-body').on('click', 'input[type="checkbox"]', function () {
            if ($(this).prop('checked') == true) {
                if ($(this).val() == 'all') {
                    checked = Array();
                    $('table input[type="checkbox"]').each(function () {
                        $(this).prop('checked', 'checked');
                        checked.push($(this).val());
                    });
                } else {
                    checked.push($(this).val());
                    allChecked = true;
                    $('table input[type="checkbox"]').each(function () {
                        if ($(this).prop('checked') != true) {
                            allChecked = false;
                        }
                    });

                    $('.panel-body input.all').prop('checked', allChecked);
                }
            } else {
                if ($(this).val() == 'all') {
                    checked = Array();
                    $('table input[type="checkbox"]').each(function () {
                        $(this).prop('checked', '');
                    });
                } else if (checked.indexOf($(this).val()) != -1) {
                    checked.splice(checked.indexOf($(this).val()), 1);
                    $('.panel-body input.all').prop('checked', '');
                }
            }
            
            $("#chapters-ids").val(checked.join(','));
        });
    });
</script>
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.manga.show', $manga))

@section('content')
<div class="row" style="margin: 0">
    <div class="col-lg-8">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-square fa-fw"></i> {{ $manga->name }}
                    <div class="pull-right">
                        @if(Entrust::hasRole('Admin') || (Auth::user()->id==$manga->user->id && (Entrust::can('edit_manga') || Entrust::can('delete_manga'))))
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                {{ Lang::get('messages.admin.manga.actions') }}
                                <span class="caret"></span>
                            </button>

                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>                                        
                                    {{ link_to_route('admin.manga.edit', Lang::get('messages.admin.manga.edit-manga'), $manga->id) }}
                                </li>
                                <li>
                                    {{ Form::open(array('route' => array('admin.manga.destroy', $manga->id), 'method' => 'delete')) }}
                                    {{ Form::submit(Lang::get('messages.admin.manga.delete-manga'), array('class' => 'delete-btn',  'onclick' => 'if (!confirm("'. Lang::get('messages.admin.manga.confirm-delete') .'")) {return false;}')) }}
                                    {{ Form::close() }}
                                </li>
                            </ul>
                        </div>
                        @endif
                        {{ link_to_route('admin.manga.index', Lang::get('messages.admin.manga.back'), null, array('class' => 'btn btn-default btn-xs')); }}
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        @if(!is_null($manga->status))
                        <dt>{{ Lang::get('messages.admin.manga.detail.status') }}</dt>
                        <dd>
                            @if($manga->status->id == 1)
                            <span class="label label-success">{{ $manga->status->label }}</span>
                            @else
                            <span class="label label-danger">{{ $manga->status->label }}</span>
                            @endif          
                        </dd>
                        @endif

                        @if(!is_null($manga->otherNames) && $manga->otherNames != "")
                        <dt>{{ Lang::get('messages.admin.manga.detail.other-names') }}</dt>
                        <dd>{{ $manga->otherNames }}</dd>
                        @endif

                        @if(!is_null($manga->author) && $manga->author != "")
                        <dt>{{ Lang::get('messages.admin.manga.detail.author') }}</dt>
                        <dd>{{ $manga->author }}</dd>
                        @endif

                        @if(!is_null($manga->releaseDate) && $manga->releaseDate != "")
                        <dt>{{ Lang::get('messages.admin.manga.detail.released') }}</dt>
                        <dd>{{ $manga->releaseDate }}</dd>
                        @endif

                        @if (count($manga->categories)>0)
                        <dt>{{ Lang::get('messages.admin.manga.detail.categories') }}</dt>
                        <dd>
                            {{ App::make("HelperController")->listAsString($manga->categories, ', ') }}
                        </dd>
                        @endif

                        @if(!is_null($manga->summary) && $manga->summary != "")
                        <dt>{{ Lang::get('messages.admin.manga.detail.summary') }}</dt>
                        <dd>{{ $manga->summary }}</dd>
                        @endif
                        
                        @if (count($manga->tags)>0)
                        <dt>{{ Lang::get('messages.admin.manga.create.tags') }}</dt>
                        <dd>
                            {{ App::make("HelperController")->listAsString($manga->tags, ', ') }}
                        </dd>
                        @endif
                    </dl>
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-list-alt fa-fw"></i> {{ Lang::get('messages.admin.manga.chapters', array('manganame' => $manga->name)) }}
                    @if(Entrust::can('add_chapter'))
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                {{ Lang::get('messages.admin.manga.create-chapter') }}
                                <span class="caret"></span>
                            </button>

                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>                                        
                                    {{ link_to_route('admin.manga.chapter.create', Lang::get('messages.admin.chapter.edit.manually'), $manga->id) }}
                                </li>
                                <li>
                                    {{ link_to_route('admin.manga.chapter.scraper', Lang::get('messages.admin.chapter.edit.grap-sites'), array('manga' => $manga->id)); }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div style="display: inline-block; margin-right: 10px" class="pull-right">
                        {{ Form::open(array('route' => array('admin.manga.chapter.destroyChapters', $manga->id), 'method' => 'delete')) }}
                        {{ Form::submit(Lang::get('messages.admin.chapter.edit.delete-chapters'), array('class' => 'btn btn-danger btn-xs delete',  'onclick' => 'if (!confirm("'. Lang::get('messages.admin.chapter.edit.confirm-delete-chapters'). '")) {return false;}')) }}
                        <input type="hidden" name="chapters-ids" id="chapters-ids"/>
                        {{ Form::close() }}
                    </div>
                    @endif
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" name="check-all" class="all" value="all"/></th>
                                    <th>{{ Lang::get('messages.admin.manga.chapter-number') }}</th>
                                    <th>{{ Lang::get('messages.admin.manga.chapter-name') }}</th>
                                    <th>{{ Lang::get('messages.admin.manga.owner') }}</th>
                                    <th style="width: 110px">{{ Lang::get('messages.admin.manga.created') }}</th>
                                </tr>
                            </thead>
                            @if (count($manga->chapters)>0)
                            @foreach ($manga->sortedChapters() as $chapter)
                            <tr>
                                <td><input type="checkbox" value="{{ $chapter->id }}"/></td>
                                <td>
                                    @if(Entrust::can('view_chapter') || Entrust::can('add_chapter') || Entrust::can('edit_chapter') || Entrust::can('delete_chapter'))
                                    {{ link_to("/admin/manga/$manga->id/chapter/$chapter->id", $chapter->number) }}
                                    @else
                                    {{ $chapter->number }}
                                    @endif
                                </td>
                                <td class="chapter-title">
                                    @if(Entrust::can('view_chapter') || Entrust::can('add_chapter') || Entrust::can('edit_chapter') || Entrust::can('delete_chapter'))
                                    {{ link_to("/admin/manga/$manga->id/chapter/$chapter->id", $chapter->name) }}
                                    @else
                                    {{ $chapter->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ $chapter->user->username }}
                                </td>
                                <td>
                                    {{ App::make("HelperController")->formateCreationDate($chapter->created_at) }}
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4">
                                    <div class="center-block">
                                        <p>{{ Lang::get('messages.admin.dashboard.no-chapter') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div id="coverContainer">
            <div class="coverWrapper">
                <div class="previewWrapper">
                    @if ($manga->cover)
                    <img class="img-responsive img-rounded" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_250x350.jpg")}}' alt='{{ $manga->name }}'>
                    @else
                    <i class="fa fa-file-image-o"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
