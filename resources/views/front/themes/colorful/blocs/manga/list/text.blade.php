<div class="type-content">
    <script>
        $(document).ready(function() {
            $('ul.tab-header').each(function() {
                letterSelector = $.trim($(this).text());
                if (letterSelector === '#')
                    letterSelector = 'unknown';

                $('span.alphabetic[class*="' + letterSelector + '"]').removeClass('disabled');
            });

            $('span.alphabetic').off().on('click',
                    function(e) {
                        letterSelector = $(e.currentTarget).text();
                        if (letterSelector === '#')
                            letterSelector = 'unknown';

                        if ($('ul.' + letterSelector).length)
                            $('html, body').animate({scrollTop: $('ul.' + letterSelector).offset().top});
                    });
        });
    </script>

    @if (count($mangaList)>0)
    @foreach ($mangaList as $key=>$group)
    <div class="tabs-framed tabs-framed-left boxed widget-container">
        <ul class="tabs clearfix tab-header {{ $key=='#'?'unknown':$key }}">
            <li><a><b>{{ $key }}</b></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active">
                <ul class="price-list style3">
                    @foreach ($group as $manga)
                    <li style="float: left; min-width: 50%;" class="list-rtl">
                        <a href="{{route('front.manga.show',$manga->slug)}}">
                            <h6>{{ $manga->name }}</h6>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <div style="clear: both;"/></div>
        </div>
    </div>
</div>
@endforeach
@else
<div class="center-block">
    <p>{{ Lang::get('messages.front.directory.no-manga') }}</p>
</div>
@endif
</div>
