@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.manga.index'))

@section('head')
{{ Jraty::js() }}
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list fa-fw"></i> {{ Lang::get('messages.admin.manga.list') }}
                @if(Entrust::can('add_manga'))
                <div class="pull-right">
                    {{ link_to_route('admin.manga.create', Lang::get('messages.admin.manga.create'), null, array('class' => 'btn btn-primary btn-xs pull-right', 'role' => 'button')) }}
                </div>
                @endif
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alphabetic-box">
                            <div class="list-alphabet">
                                <span class="alphabetic">
                                    <a href="{{ route('front.manga.list', array('alpha' => ''))}}" class="alphabet">ALL</a>
                                </span>
                                <span class="alphabetic">
                                    <a href="{{ route('front.manga.list', array('alpha' => 'Other'))}}" class="alphabet">#</a>
                                </span>
                                @foreach (range('A', 'Z') as $char)
                                <span class="alphabetic">
                                    <a href="{{ route('front.manga.list', array('alpha' => $char))}}" class="alphabet">{{ $char }}</a>
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div id="waiting" style="position: relative; display: none;" class="col-sm-12">
                        <img src="{{ asset('images/ajax-loader.gif') }}" 
                             style="position: absolute; right: 10px; top: 10px;" />
                    </div>

                    <div class="content">
                        @include('admin.manga.filter')
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
<script>
    var alpha = "";

    // filter by alphabet
    $(document).on('click', '.alphabet', function(e) {
        e.preventDefault();

        alpha = $(this).attr('href').split('alpha=')[1];
        getMangaList(1);
    });

    // paginate
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();

        var page = $(this).attr('href').split('page=')[1];
        getMangaList(page);
    });
    
    function getMangaList(page) {
        $('#waiting').show();
        
        $.ajax({
            url: 'filterMangaList',
            data: {'page': page, 'alpha': alpha}
        }).done(function(data) {
            $('#waiting').hide();
            $('.content').html(data);
        });
    }
</script>
@stop