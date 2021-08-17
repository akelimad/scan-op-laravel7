@extends('front.layouts.colorful')

@section('title')
@if(isset($seo->news->title->global) && $seo->news->title->global == '1')
{{$settings['seo.title']}} | {{$post->title}}
@else
{{ App::make("HelperController")->advSeoNewsPage($seo->news->title->value, $post) }}
@endif
@stop

@section('description')
@if(isset($seo->news->description->global) && $seo->news->description->global == '1')
{{$settings['seo.description']}}
@else
{{ App::make("HelperController")->advSeoNewsPage($seo->news->description->value, $post) }}
@endif
@stop

@section('keywords')
@if(isset($seo->news->keywords->global) && $seo->news->keywords->global == '1')
{{$settings['seo.keywords']}}
@else
{{ App::make("HelperController")->advSeoNewsPage($seo->news->keywords->value, $post) }}
@endif
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<h1 class="widget-title">{{$post->title}}</h1>
<div class="pull-right" style="padding: 15px 0;">
    <span class="pull-left">
        <i class="glyphicon glyphicon-time"></i> {{ App::make("HelperController")->formateCreationDate($post->created_at) }}&nbsp;&middot;&nbsp;
    </span>
    <span class="pull-left"><i class="glyphicon glyphicon-user"></i> {{$post->user->username}}</span>
    @if(!is_null($post->manga))
    <span class="pull-left">&nbsp;&middot;&nbsp;<i class="glyphicon glyphicon-folder-open"></i> {{ link_to("/manga/{$post->manga->slug}", $post->manga->name) }}</span>
    @endif
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="widget-container boxed" style="padding:15px;">
            {{$post->content}}
        </div>

        <input type="hidden" id="post_id" name="post_id" value="{{$post->id}}"/>
        <input type="hidden" id="post_type" name="post_type" value="post"/>
    </div>
</div>

<!-- comment -->
<?php $comment = json_decode($settings['site.comment']) ?>

@if(isset($comment->page->news) && $comment->page->news == '1')
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
                <div class="fb-comments" data-href="{{route('front.news', $post->slug)}}" data-width="100%" data-numposts="5">
                </div>
            </div>
            @endif
            @if(isset($comment->disqus) && $comment->disqus == '1')
            <div role="tabpanel" class="tab-pane <?php if (!isset($comment->fb)) echo 'active'; ?>" id="disqus">
                <div id="disqus_thread"></div>
                <script>
                    var disqus_config = function () {
                        this.page.url = "{{route('front.news', $post->slug)}}";
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
@stop
