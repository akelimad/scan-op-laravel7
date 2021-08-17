@section('hotmanga')
@if (count($hotMangaList)>0)
<div class="col-sm-12">
    <h2 class="hotmanga-header"><i class="fa fa-star"></i>{{ Lang::get('messages.front.home.hot-updates') }}</h2>
    <hr/>

    <ul class="hot-thumbnails">
        @foreach ($hotMangaList as $manga)
        <li class="span3">
            <div class="photo" style="position: relative;">
                <div class="manga-name">
                    <a class="label label-warning" href="{{route('front.manga.show',$manga->manga_slug)}}">{{ $manga->manga_name }}</a>
                </div>
                <a class="thumbnail" style="position: relative; z-index: 10; background: rgb(255, 255, 255) none repeat scroll 0% 0%;" href='{{asset("/manga/$manga->manga_slug/$manga->chapter_slug") }}'>
                    @if ($manga->manga_cover)
                    <img src='{{asset("uploads/manga/{$manga->manga_slug}/cover/cover_250x350.jpg")}}' alt='{{ $manga->manga_name }}' />
                    @else
                    <img src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->manga_name }}' />
                    @endif
                </a>
                <div class="well">
                    <p>
                        <i class="fa fa-book"></i>
                        {{ "#".$manga->chapter_number."  ".$manga->chapter_name }}
                    </p>
                </div>
            </div></li>
        @endforeach
    </ul>
</div>
<div style="clear:both"></div>
@endif
@stop