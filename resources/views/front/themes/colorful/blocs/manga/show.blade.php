@extends('front.layouts.colorful')

@section('title')
@if(isset($seo->info->title->global) && $seo->info->title->global == '1')
{{$settings['seo.title']}} | {{$manga->name}}
@else
{{ (new App\Http\Controllers\Utils\HelperController())->advSeoInfoPage($seo->info->title->value, $manga) }}
@endif
@stop

@section('description')
@if(isset($seo->info->description->global) && $seo->info->description->global == '1')
{{$settings['seo.description']}}
@else
{{ (new App\Http\Controllers\Utils\HelperController())->advSeoInfoPage($seo->info->description->value, $manga) }}
@endif
@stop

@section('keywords')
@if(isset($seo->info->keywords->global) && $seo->info->keywords->global == '1')
{{$settings['seo.keywords']}}
@else
{{ (new App\Http\Controllers\Utils\HelperController())->advSeoInfoPage($seo->info->keywords->value, $manga) }}
@endif
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('header')


    @section('allpage')
    <h2 class="widget-title">{{$manga->name}}</h2>
    @if(Auth::user())
    <span class="bookmark" style="float: right; display: inline-block; margin: 21px 0px 10.5px;">
        <a href="#"><i class="glyphicon glyphicon-heart-empty"></i>{{Lang::get('messages.front.bookmarks.bookmark')}}</a>
    </span>
    @endif

    <script>
        $('.bookmark a').click(function (e) {
            e.preventDefault();

            $.ajax({
                headers: {'x-csrf-token': "{{ csrf_token() }}"},
                url: "{{route('bookmark.store')}}",
                method: 'POST',
                data: {
                    'manga_id': '{{$manga->id}}',
                    'chapter_id': '0',
                    'page_slug': '0',
                },
                success: function (response) {
                    if (response.status == 'ok') {
                        alert("{{Lang::get('messages.front.bookmarks.bookmarked')}}");
                    }
                },
                error: function (response) {
                    alert("{{Lang::get('messages.front.bookmarks.error')}}");
                }
            });
        });
    </script>

    <div class="row">
        <div class="col-sm-4">
            <div class="boxed" style="width: 250px; height: 350px;">
                @if ($manga->cover)
                <img class="img-responsive" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_250x350.jpg")}}' alt='{{ $manga->name }}'/>
                @else
                <img width="250" height="350" src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->name }}' />
                @endif
            </div>
        </div>
        <div class="col-sm-8">
            <div class="widget-container boxed boxed-transparent">
                <dl class="dl-horizontal">
                    @if (!is_null($manga->type))
                    <dt>{{ Lang::get('messages.front.manga.type') }}</dt>
                    <dd>
                        {{ $manga->type->label }}
                    </dd>
                    @endif

                    @if(!is_null($manga->status))
                    <dt>{{ Lang::get('messages.front.manga.status') }}</dt>
                    <dd>
                        @if($manga->status->id == 1)
                        <span class="label label-success">{{ $manga->status->label }}</span>
                        @else
                        <span class="label label-danger">{{ $manga->status->label }}</span>
                        @endif
                    </dd>
                    @endif

                    @if(!is_null($manga->otherNames) && $manga->otherNames != "")
                    <dt>{{ Lang::get('messages.front.manga.other-names') }}</dt>
                    <dd>{{ $manga->otherNames }}</dd>
                    @endif

                    @if(!is_null($manga->author) && $manga->author != "")
                    <dt>{{ Lang::get('messages.front.manga.author') }}</dt>
                    <dd>
                        <?php $authors=explode(',', $manga->author);?>
                        @foreach($authors as $index=>$author)
                        {{ link_to("/manga-list/author/".trim($author), trim($author)) }}
                        @if($index!=count($authors)-1)
                        ,&nbsp;
                        @endif
                        @endforeach
                    </dd>
                    @endif

                    @if(!is_null($manga->artist) && $manga->artist != "")
                    <dt>{{ Lang::get('messages.front.manga.artist') }}</dt>
                    <dd>{{ $manga->artist }}</dd>
                    @endif

                    @if(!is_null($manga->releaseDate) && $manga->releaseDate != "")
                    <dt>{{ Lang::get('messages.front.manga.released') }}</dt>
                    <dd>{{ $manga->releaseDate }}</dd>
                    @endif

                    @if (count($manga->categories)>0)
                    <dt>{{ Lang::get('messages.front.manga.categories') }}</dt>
                    <dd>
                        @foreach($manga->categories as $index=>$category)
                        {{ link_to("/manga-list/category/$category->slug", $category->name) }}
                        @if($index!=count($manga->categories)-1)
                        ,&nbsp;
                        @endif
                        @endforeach
                    </dd>
                    @endif

                    @if (count($manga->tags)>0)
                    <dt>{{ Lang::get('messages.front.manga.tags') }}</dt>
                    <dd class="tag-links">
                        @foreach($manga->tags as $index=>$tag)
                        {{ link_to("/manga-list/tag/$tag->id", $tag->name) }}
                        @endforeach
                    </dd>
                    @endif

                    <br/>

                    <dt>{{ Lang::get('messages.front.directory.views') }}</dt>
                    <dd>{{ $manga->views }}</dd>

                    <dt>{{ Lang::get('messages.front.manga.rating') }}</dt>

                        @php($item = App\ItemRating::get($manga->id, Request::ip()))
                        <dd class="rating" style="margin-left: 18px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star{{ $item != null && $i <= $item->score ? "":"-o" }} {{ $item != null && $item->score == $i ? "active":"" }}" aria-hidden="true" data-score="{{ $i }}" style="{{ $item != null ? "color: orange;":"" }}"></i>
                            @endfor
                        </dd>
                        <dd>{{ __("Moyenne de :avg/5 sur :vote vote(s)", ["avg"  => App\ItemRating::getVotesAvg($manga->id), 'vote' => App\ItemRating::getTotalVotes($manga->id)]) }}</dd>
                </dl>
                @if ($manga->caution == 1)
                    <div role="alert" class="alert alert-danger" style="position: absolute; bottom: 0px; right: 0px; left: 0px; margin: 10px 50px;">
                        {{ Lang::get('messages.front.manga.caution') }}
                    </div>
                @endif
</div>
</div>
</div>
@if(!is_null($manga->summary) && $manga->summary != "")
<div class="row">
<div class="col-lg-12">
<div class="widget-container boxed" style="padding:20px">
<h5>{{ Lang::get('messages.front.manga.summary') }}</h5>
<p>{{ $manga->summary }}</p>
</div>
</div>
</div>
@endif

@if (count($posts)>0)
<div class="row" style="margin-bottom: 20px">
<div class="col-lg-12">
<h2 class="widget-title">{{ Lang::get('messages.front.home.news') }}</h2>
<ul class="chapters">
@foreach ($posts as $post)
<li>
    <div class="pull-right">
        <span class="pull-left">
            <i class="glyphicon glyphicon-time"></i> {{ (new App\Http\Controllers\Utils\HelperController())->formateCreationDate($post->created_at) }}&nbsp;&middot;&nbsp;
        </span>
        <span class="pull-left"><i class="glyphicon glyphicon-user"></i> {{$post->user->username}}</span>
    </div>

    <h3 class="chapter-title-rtl">
        <a href="{{route('front.news', $post->slug)}}">{{$post->title}}</a>
    </h3>
</li>
@endforeach
</ul>
</div>
</div>
<br/>

@endif

<!-- ads -->
<div class="row">
<div class="col-xs-12" style="padding: 0">
<div class="ads-large" style="display: table; margin: 10px auto;">
{{isset($ads['TOP_LARGE'])?$ads['TOP_LARGE']:''}}
</div>
<div style="display: table; margin: 10px auto;">
<div class="pull-left ads-sqre1" style="margin-right: 50px;">
    {{isset($ads['TOP_SQRE_1'])?$ads['TOP_SQRE_1']:''}}
</div>
<div class="pull-right ads-sqre2">
    {{isset($ads['TOP_SQRE_2'])?$ads['TOP_SQRE_2']:''}}
</div>
</div>
</div>
</div>

<div class="row">
<div class="col-lg-12">
<h2 class="widget-title">{{ Lang::get('messages.front.manga.chapters', array('manganame' => $manga->name)) }}</h2>

<ul class="chapters">
@if (count($chapters)>0)
<?php $volume = 0; ?>

@foreach ($chapters as $chapter)
@if (isset($mangaOptions->show_chapters_volume) && $mangaOptions->show_chapters_volume == '1')
@if ($volume!=$chapter->volume)
<li class="volume btn btn-default btn-xs" data-volume="volume-{{$chapter->volume}}">
    <i class="glyphicon glyphicon-minus"></i> Volume {{$chapter->volume}}
</li>
@endif
<?php $volume = $chapter->volume; ?>
@endif
<li class="volume-{{$chapter->volume}}">
    <div class="action">
        <?php if (isset($mangaOptions->allow_download_chapter) && $mangaOptions->allow_download_chapter == '1') { ?>
            <a href="{{route('front.manga.download', array('mangaSlug' => $manga->slug, 'chapterId' => $chapter->id))}}"
               title="download" class="pull-right" style="margin-left: 10%">
                <i class="glyphicon glyphicon-download-alt"></i>
            </a>
        <?php } ?>
        <div style="float:right" class="date-chapter-title-rtl">
            <i class="glyphicon glyphicon-time"></i> {{ (new App\Http\Controllers\Utils\HelperController())->formateCreationDate($chapter->created_at) }}
        </div>
        <?php if (isset($mangaOptions->show_contributer_pseudo) && $mangaOptions->show_contributer_pseudo == '1') { ?>
            <div style="float:right; margin-right: 10%">
                <a href="{{route('user.profil.index', $chapter->user->username)}}">
                    <i class="glyphicon glyphicon-user"></i> {{ $chapter->user->username }}
                </a>
            </div>
        <?php } ?>
    </div>

    <h3 class="chapter-title-rtl">
        {{ link_to("/manga/$manga->slug/$chapter->slug", $manga->name.' '.$chapter->number) }}
        <em>{{ $chapter->name }}</em>
    </h3>
</li>
@endforeach
@else
<div class="center-block">
    <p>{{ Lang::get('messages.front.manga.no-chapter') }}</p>
</div>
@endif
</ul>
</div>
</div>

<!-- ads -->
<div class="row">
<div class="col-xs-12" style="padding: 0">
<div class="ads-large" style="display: table; margin: 10px auto;">
{{isset($ads['BOTTOM_LARGE'])?$ads['BOTTOM_LARGE']:''}}
</div>
<div style="display: table; margin: 10px auto;">
<div class="pull-left ads-sqre1" style="margin-right: 50px;">
    {{isset($ads['BOTTOM_SQRE_1'])?$ads['BOTTOM_SQRE_1']:''}}
</div>
<div class="pull-right ads-sqre2">
    {{isset($ads['BOTTOM_SQRE_2'])?$ads['BOTTOM_SQRE_2']:''}}
</div>
</div>
</div>
</div>

<!-- comment -->
<input type="hidden" id="post_id" name="post_id" value="{{$manga->id}}"/>
<input type="hidden" id="post_type" name="post_type" value="manga"/>

<?php $comment = json_decode($settings['site.comment']) ?>

@if(isset($comment->page->mangapage) && $comment->page->mangapage == '1')
@if(isset($comment->fb) && $comment->fb == '1')
<div id="fb-root"></div>
<script>
(function (d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id))
return;
js = d.createElement(s);
js.id = id;
js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
@endif

<div class="row widget-container boxed" style="margin:15px auto;">
<div class="col-xs-12" style="padding:15px;">
<ul class="nav nav-tabs" role="tablist">
@if(isset($comment->builtin) && $comment->builtin == '1')
<li role="presentation" class="active"><a href="#builtin" aria-controls="builtin" role="tab" data-toggle="tab">{{Lang::get('messages.front.home.comment.builtin-tab')}}</a></li>
@endif
@if(isset($comment->fb) && $comment->fb == '1')
<li role="presentation"><a href="#fb" aria-controls="fb" role="tab" data-toggle="tab">Facebook</a></li>
@endif
@if(isset($comment->disqus) && $comment->disqus == '1')
<li role="presentation"><a href="#disqus" aria-controls="disqus" role="tab" data-toggle="tab">Disqus</a></li>
@endif
</ul>

<!-- Tab panes -->
<div class="tab-content">
@if(isset($comment->builtin) && $comment->builtin == '1')
<div role="tabpanel" class="tab-pane active" id="builtin">
    @include('front.themes.default.blocs.comments')
</div>
@endif
@if(isset($comment->fb) && $comment->fb == '1')
<div role="tabpanel" class="tab-pane" id="fb">
    <div class="fb-comments" data-href="{{route('front.manga.show', $manga->slug)}}" data-width="100%" data-numposts="5">
    </div>
</div>
@endif
@if(isset($comment->disqus) && $comment->disqus == '1')
<div role="tabpanel" class="tab-pane <?php if (!isset($comment->fb)) echo 'active'; ?>" id="disqus">
    <div id="disqus_thread"></div>
    <script>
        var disqus_config = function () {
            this.page.url = "{{route('front.manga.show', $manga->slug)}}";
        };

        (function () {  // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');

            s.src = '//<?php echo isset($comment->disqusUrl) ? $comment->disqusUrl : '' ?>/embed.js';

            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
</div>
@endif
</div>
</div>
</div>
@endif

@if (Session::has('downloadError'))
<script>
alert('Sorry! thers is no pages on this chapter.');
</script>
@endif

<script>
    $(document).ready(function () {
        $(".volume").click(function () {
            volume = $(this).data('volume');
            $('li.' + volume).toggle();
            $(this).find('i').toggleClass('glyphicon glyphicon-minus').toggleClass('glyphicon glyphicon-plus');
        });

        $(".rating i").hover(function() {
            var $this = $(this);
            $this.nextAll().removeClass('fa-star').addClass( "fa-star-o" );
            $this.prevUntil("h1").removeClass( "fa-star-o" ).addClass('fa-star');
            $this.removeClass( "fa-star-o" ).addClass('fa-star');
        });

        //**on mouseOut** change back to fa-star-o OR the result of the click event
        $(".rating i").mouseout(function() {
            var select = $('.rating .active');
            if ($('.rating .active').length > 0) {
                select.nextAll().removeClass('fa-star').addClass('fa-star-o');
                select.prevUntil("h1").removeClass('fa-star-o').addClass('fa-star');
                select.removeClass('fa-star-o').addClass('fa-star');
            } else {
                $('.rating .fa').removeClass('fa-star').addClass('fa-star-o');
            }
        });

        //on Click change star to fa-star and change color to red (prevAll)
        $(".rating i").click(function () {
            $(this).addClass('active').siblings().removeClass('active');
            $(this).attr("style","color:orange");
            $(this).prevUntil("h1").css("color","orange");
            $(this).nextAll().css("color","orange");

            // store score in DB
            var score = $(this).data("score")
            $.ajax({
                headers: {'x-csrf-token': "{{ csrf_token() }}"},
                url: "{{ route("score.store") }}",
                type: "post",
                data: {
                    item_id: {{ $manga->id }},
                    score: score,
                    added_on: "{{ now() }}",
                    ip_address: "{{ Request::ip() }}"
                },
                success: function (response) {
                    console.log("saved successfully !");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });
    });
</script>
@stop
