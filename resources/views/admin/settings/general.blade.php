@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.settings.general'))

@section('head')
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-sliders fa-fw"></i> {{ Lang::get('messages.admin.settings.general.header') }}
            </div>

            <br/>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" style="padding:0 10px;">
                <li role="presentation" class="active">
                    <a href="#info" aria-controls="info" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.info')}}</a>
                </li>
                <li role="presentation">
                    <a href="#pagination" aria-controls="pagination" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.pagination')}}</a>
                </li>
                <li role="presentation">
                    <a href="#menu" aria-controls="menu" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.menu')}}</a>
                </li>
                <li role="presentation">
                    <a href="#comment" aria-controls="comment" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.comment')}}</a>
                </li>
                <li role="presentation">
                    <a href="#reader" aria-controls="reader" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.reader')}}</a>
                </li>
                <li role="presentation">
                    <a href="#storage" aria-controls="storage" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.storage')}}</a>
                </li>
            </ul>

            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        @if (Session::has('updateSuccess'))
                        <div class="alert text-center alert-info ">
                            {{ Session::get('updateSuccess') }}
                        </div>
                        @endif

                        {{ Form::open(array('route' => 'admin.settings.general.save', 'role' => 'form')) }}
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
                                <div class="form-group">
                                    {{Form::label('site.lang', Lang::get('messages.admin.settings.general.select-lang'))}}
                                    <br/>
                                    {{Form::select('site.lang', $languages, $options['site.lang'], array('class' => 'selectpicker', 'data-width' => 'auto', 'data-size' => 'false'))}}
                                </div>
                                <div class="form-group">
                                    <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.settings.general.site-orientation') }}</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="site.orientation" value="ltr" <?php if ($options['site.orientation'] === 'ltr'): ?>
                                               checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.orientation-ltr') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="site.orientation" value="rtl" <?php if ($options['site.orientation'] === 'rtl'): ?>
                                               checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.orientation-rtl') }}
                                    </label>
                                </div>
                                <div class="form-group">
                                    {{ Form::label('site.name', Lang::get('messages.admin.settings.general.site-name')) }}
                                    {{ Form::text('site.name', $options['site.name'], ['class' => 'form-control']) }}
                                    {{ $errors->first('site.name', '<label class="error" for="site.name">:message</label>') }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('site.slogan', Lang::get('messages.admin.settings.general.slogan')) }}
                                    {{ Form::text('site.slogan', $options['site.slogan'], ['class' => 'form-control']) }}
                                    {{ $errors->first('site.slogan', '<label class="error" for="site.slogan">:message</label>') }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('site.description', Lang::get('messages.admin.settings.general.description')) }}
                                    {{ Form::textarea('site.description', $options['site.description'], ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pagination">
                                <div class="form-group">
                                    {{Form::label('pagination_homepage', Lang::get('messages.admin.settings.general.pagination-homepage'))}}
                                    {{Form::number('site.pagination[homepage]', $pagination->homepage, ['min' => 5])}}
                                </div>
                                <div class="form-group">
                                    {{Form::label('pagination_mangalist', Lang::get('messages.admin.settings.general.pagination-mangalist'))}}
                                    {{Form::number('site.pagination[mangalist]', $pagination->mangalist, ['min' => 10])}}
                                </div>
                                <div class="form-group">
                                    {{Form::label('pagination_latest_release', Lang::get('messages.admin.settings.general.pagination-latest-release'))}}
                                    {{Form::number('site.pagination[latest_release]', $pagination->latest_release, ['min' => 10])}}
                                </div>

                                <hr/>
                                <div class="form-group">
                                    {{Form::label('pagination_news_homepage', Lang::get('messages.admin.settings.general.pagination-news-homepage'))}}
                                    {{Form::number('site.pagination[news_homepage]', isset($pagination->news_homepage)?$pagination->news_homepage:5, ['min' => 5])}}
                                </div>
                                <div class="form-group">
                                    {{Form::label('pagination_newslist', Lang::get('messages.admin.settings.general.pagination-newslist'))}}
                                    {{Form::number('site.pagination[newslist]', isset($pagination->newslist)?$pagination->newslist:10, ['min' => 10])}}
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="menu">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.menu[home]" value="1" 
                                               <?php if (isset($menus->home) && $menus->home == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.menu.show-home-menu')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.menu[mangalist]" value="1" 
                                               <?php if (isset($menus->mangalist) && $menus->mangalist == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.menu.show-mangalist-menu')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.menu[latest_release]" value="1" 
                                               <?php if (isset($menus->latest_release) && $menus->latest_release == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.menu.show-latest-release-menu')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.menu[news]" value="1" 
                                               <?php if (isset($menus->news) && $menus->news == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.menu.show-news-menu')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.menu[random]" value="1" 
                                               <?php if (isset($menus->random) && $menus->random == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.menu.show-random-menu')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.menu[adv_search]" value="1" 
                                               <?php if (isset($menus->adv_search) && $menus->adv_search == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.menu.show-adv-search-menu')}}
                                    </label>
                                </div>
                                <hr/>

                                <button class="btn add-bloc"><i class="fa fa-plus-square"></i> {{Lang::get('messages.admin.settings.menu.add-custom-url')}}</button>

                                <div class="blocs clearfix" style="margin: 20px 0">
                                    @if(isset($menus->label) && count($menus->label)>0)
                                    @foreach($menus->label as $index => $menu)
                                    <div class="col-xs-12 bloc">
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <label>Label&nbsp;</label>
                                                <input name="site.menu[label][]" required="" class="form-control" type="text" value="{{$menu}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>&nbsp;URL&nbsp;</label>
                                                <input name="site.menu[url][]" required="" class="form-control" type="text" placeholder="start with http://" value="{{$menus->url[$index]}}" size="50"/>
                                            </div>
                                            <div class="form-group">&nbsp;
                                                <a href="#" class="pull-right remove-bloc" title="remove bloc"><i class="fa fa-minus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="comment">
                                <b>{{Lang::get('messages.admin.settings.comment.system')}}</b>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.comment[builtin]" value="1" 
                                               <?php if (isset($comment->builtin) && $comment->builtin == '1') { ?> checked="checked" <?php } ?> >
                                        Built-in comments
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.comment[fb]" value="1" 
                                               <?php if (isset($comment->fb) && $comment->fb == '1') { ?> checked="checked" <?php } ?> >
                                        Facebook
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input id="disqusOption" type="checkbox" name="site.comment[disqus]" value="1"
                                               <?php if (isset($comment->disqus) && $comment->disqus == '1') { ?> checked="checked" <?php } ?> >
                                        Disqus
                                    </label>
                                    <input id="disqusUrl" type="text" name="site.comment[disqusUrl]" value="<?php echo isset($comment->disqusUrl) ? $comment->disqusUrl : '' ?>" 
                                           placeholder="your Disqus URL, ex: myblog.disqus.com" size="50" />
                                </div>

                                <hr/>

                                <b>{{Lang::get('messages.admin.settings.comment.show-on-page')}}</b>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.comment[page][news]" value="1" 
                                               <?php if (isset($comment->page->news) && $comment->page->news == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.comment.news-page')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.comment[page][mangapage]" value="1" 
                                               <?php if (isset($comment->page->mangapage) && $comment->page->mangapage == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.comment.manga-page')}}
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="site.comment[page][reader]" value="1" 
                                               <?php if (isset($comment->page->reader) && $comment->page->reader == '1') { ?> checked="checked" <?php } ?> >
                                        {{Lang::get('messages.admin.settings.comment.reader-page')}}
                                    </label>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="reader">
                                <div class="form-group">
                                    <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.settings.general.reader-type') }}</label>
                                    <label class="radio-inline">
                                        <input type="radio" name="reader.type" value="all" <?php if ($options['reader.type'] === 'all'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.reader-type-all') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="reader.type" value="ppp" <?php if ($options['reader.type'] === 'ppp'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.reader-type-ppp') }}
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.settings.general.reader-mode') }}</label>
                                    <label class="radio">
                                        <input type="radio" name="reader.mode" value="noreload" <?php if ($options['reader.mode'] === 'noreload'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.reader-mode-noreload') }}
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="reader.mode" value="reload" <?php if ($options['reader.mode'] === 'reload'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.reader-mode-reload') }}
                                    </label>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="storage">
                                <div class="form-group">
                                    <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.settings.general.storage-type') }}</label>
                                    <label class="radio">
                                        <input type="radio" name="storage.type" value="server" <?php if ($options['storage.type'] === 'server'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.storage-type-server') }}
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="storage.type" value="gdrive" <?php if ($options['storage.type'] === 'gdrive'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.storage-type-gdrive') }}
                                        {{ link_to_route('admin.settings.gdrive', '(Configuration)') }}
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="storage.type" value="mirror" <?php if ($options['storage.type'] === 'mirror'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.settings.general.storage-type-mirror') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.admin.settings.save'), ['class' => 'btn btn-primary']) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>

<script>
    var bloc_template = '<div class="col-xs-12 bloc">' +
            '<div class="form-inline">' +
            '<div class="form-group">' +
            '<label>Label&nbsp;</label>' +
            '<input name="site.menu[label][]" required="" class="form-control" type="text" value=""/>' +
            '</div>' +
            '<div class="form-group">' +
            '<label>&nbsp;URL&nbsp;</label>' +
            '<input name="site.menu[url][]" required="" class="form-control" type="text" placeholder="start with http://" value="" size="50"/>' +
            '</div>' +
            '<div class="form-group">&nbsp;' +
            '<a href="#" class="pull-right remove-bloc" title="remove bloc"><i class="fa fa-minus"></i></a>' +
            '</div>' +
            '</div>' +
            '</div>';

    $(document).ready(function () {
        $('#disqusOption').click(function () {
            disqus();
        });

        function disqus() {
            if ($('#disqusOption').is(':checked')) {
                $('#disqusUrl').show();
            } else {
                $('#disqusUrl').hide().val('');
            }
        }
        disqus();

        $('.add-bloc').click(function (e) {
            e.preventDefault();

            $('.blocs').append(bloc_template);
        });

        $('.blocs').on('click', '.remove-bloc', function (e) {
            e.preventDefault();

            parent = $(this).parents('.bloc');
            if (confirm('are you sure to delete this bloc?')) {
                parent.remove();
            }
        });
    });
</script>
@stop