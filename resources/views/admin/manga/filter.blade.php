@if (count($mangas)>0)
<div class="col-sm-12">
    <?php $previous = null ?>
    @foreach ($mangas as $manga)
    <?php $firstLetter = substr($manga->name, 0, 1); ?>
    @if (strcasecmp($previous, $firstLetter))
    <div class="page-header {{ strtoupper($firstLetter )}}">
        <b>{{ strtoupper($firstLetter )}}</b>
    </div>
    @endif
    <?php $previous = $firstLetter; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="media manga-box">
                @if ($manga->hot)
                <div class="hot-mark"><i class="fa fa-bookmark"></i></div>
                @endif
                <div class="media-left manga-cover">
                    <a href='{{ url("/admin/manga/{$manga->id}") }}'>
                        @if ($manga->cover)
                        <img width="100" height="100" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_thumb.jpg")}}' alt='{{ $manga->name }}' />
                        @else
                        <img width="100" height="100" src='{{asset("images/no-image.png")}}' alt='{{ $manga->name }}' />
                        @endif
                    </a>
                </div>
                <div class="media-body" style="width: 100%;">
                    <div class="manga-created pull-right">
                        <i class="fa fa-user"></i>
                        <small>{{ $manga->user->username }},</small>
                        <i class="fa fa-calendar-o"></i>
                        <small>{{ (new App\Http\Controllers\Utils\HelperController)->formateCreationDate($manga->created_at) }}</small>
                    </div>
                    <h5 style="margin: 5px 0 0;">
                        {{ link_to("/admin/manga/{$manga->id}", $manga->name) }}
                    </h5>

                    <div class="readOnly-{{$manga->id}}" style="display: inline-block;"></div>

                    @php($avg = \App\ItemRating::getVotesAvg($manga->id))
                    <span style="vertical-align: middle;">{{ $avg }}</span>
                    <div class="rating">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star{{ $avg != null && $i <= $avg ? "":"-o" }} {{ $avg != null && $avg == $i ? "active":"" }}" aria-hidden="true" data-score="{{ $i }}" style="{{ $avg != null ? "color: orange;":"" }}"></i>
                        @endfor
                    </div>

                    <div>
                        <i class="fa fa-eye"></i> {{$manga->views}}
                    </div>

                    @if (count($manga->categories)>0)
                    <div class="categories">
                        <i class="fa fa-tags"></i>
                        {{ (new App\Http\Controllers\Utils\HelperController)->listAsString($manga->categories, ', ') }}
                    </div>
                    @endif
                    @if (!is_null($manga->lastChapter()))
                    <div class="manga-last-chapter">
                        <i class="fa fa-book"></i>
                        @if(Entrust::can('view_chapter') || Entrust::can('add_chapter') || Entrust::can('edit_chapter') || Entrust::can('delete_chapter'))
                        {{ link_to("/admin/manga/$manga->id/chapter/{$manga->lastChapter()->id}", "#".$manga->lastChapter()->number." ".$manga->lastChapter()->name) }}
                        @else
                        {{ "#".$manga->lastChapter()->number." ".$manga->lastChapter()->name }}
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="col-xs-12">
    {{$mangas->links()}}
</div>
@else
<div class="center-block">
    <p>{{ Lang::get('messages.admin.dashboard.no-manga') }}</p>
</div>
@endif