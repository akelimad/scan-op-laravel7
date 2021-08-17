@if(count($filter)>0)
@foreach ($filter as $manga)
<div class="col-sm-6">
    <div class="media" style="margin-bottom: 20px;">
        <div class="media-left">
            <a href="{{route('front.manga.show',$manga->slug)}}" class="thumbnail">
                @if ($manga->cover)
                <img width="100" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_250x350.jpg")}}' alt='{{ $manga->name }}'>
                @else
                <img width="100" style="height: 140px;" src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->name }}' />
                @endif
            </a>
        </div>
        <div class="media-body">
            <?php $rate = Jraty::get($manga->id); ?>
            <h5 class="media-heading"><a href="{{route('front.manga.show',$manga->slug)}}" class="chart-title"><strong>{{$manga->name}}</strong></a></h5>
            <div class="readOnly-{{$manga->id}}" style="display: inline-block;"></div> <span style="vertical-align: middle;">{{$rate->avg}}</span>
            <script>$('.readOnly-{{$manga->id}}').raty({path: "{{asset('/packages/escapeboy/jraty/raty/lib/img')}}", readOnly: true, score: "{{$rate->avg}}"});</script>
            <div>
                <i class="fa fa-eye"></i> {{$manga->views}}
            </div>
            @if (count($manga->categories)>0)
            <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 95%;">
                {{ App::make("HelperController")->listAsString($manga->categories, ', ') }}
            </div>
            @endif
            <?php $lastChapter = $manga->lastChapter(); ?>
            @if (!is_null($lastChapter))
            <div style="position: absolute; bottom: 20px">
                {{ link_to("/manga/$manga->slug/{$lastChapter->slug}", "#".$lastChapter->number." ".$lastChapter->name) }}
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach
<div class="row">
    <div class="col-xs-12">
        {{$filter->links()}}
    </div>
</div>
@else
<div class="center-block">
    <p>{{ Lang::get('messages.front.directory.no-manga') }}</p>
</div>
@endif