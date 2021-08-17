@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.manga.chapter.create', $manga))

@section('head')
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-magnet fa-fw"></i> {{ Lang::get('messages.admin.chapter.scraper.title') }}
                <div class="pull-right">
                    {{ link_to_route('admin.manga.show', Lang::get('messages.admin.manga.back'), array('manga' => $manga->id), array('class' => 'btn btn-default btn-xs pull-right')); }}
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="form-group">
                @if ($settings['storage.type'] == 'gdrive')
                <div class="alert alert-info" role="alert">{{ Lang::get('messages.admin.chapter.scraper.storage-mode.gdrive') }}</div>
                @elseif ($settings['storage.type'] == 'mirror')
                <div class="alert alert-info" role="alert">{{ Lang::get('messages.admin.chapter.scraper.storage-mode.mirror') }}</div>
                @else
                <div class="alert alert-info" role="alert">{{ Lang::get('messages.admin.chapter.scraper.storage-mode.server') }}</div>
                @endif
                </div>
                
                <div class="form-group">
                    <label for="scan-source">{{ Lang::get('messages.admin.chapter.scraper.select-source') }}</label>
                    <select id="scan-source" class="selectpicker" data-width="100%">
                        @foreach($websites as $key=>$site)
                        <option value="{{ $key }}"> {{$site['name']}} </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group scraping-mode">
                    <label for="scraping-mode">Scraping mode:</label>
                    <select id="scraping-mode" class="selectpicker" data-width="100%">
                        <option value="">-- Select How to scrape chapters --</option>
                        <option value="one-or-many">{{ Lang::get('messages.admin.chapter.scraper.one-many-urls') }}</option>
                        <option value="all-chapters">Scrape all chapters from Manga page</option>
                    </select>
                </div>
                <div class="form-group one-or-many">
                    <label for="chaptersUrl">{{ Lang::get('messages.admin.chapter.scraper.one-many-urls') }}</label>
                    <textarea id="chaptersUrl" class="form-control" rows="7" aria-describedby="helpBlock"></textarea>
                    <span id="helpBlock" class="help-block"></span>
                    @if(Entrust::can('add_chapter'))
                    <button id="startScrapingBtn" type="button" class="btn btn-default" onclick="startScraping()">
                        {{ Lang::get('messages.admin.chapter.scraper.start') }}
                    </button>
                    <a href="" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" title="After finishing scraping click here to notify users">
                        {{ Lang::get('messages.admin.chapter.scraper.notif-users') }}
                    </a>
                    @endif
                </div>
                <div class="form-group all-chapters">
                    <label for="mangaPageUrl">Enter the Manga Page URL</label>
                    <input id="mangaPageUrl" class="form-control" aria-describedby="mangaHelpBlock" />
                    <span id="mangaHelpBlock" class="help-block">
                        <span></span>
                        <br/>
                        /!\ Depending on the number of chapters, it may takes a long time to get them all, you can stop and resume later (recommended).
                    </span>

                    @if(Entrust::can('add_chapter'))
                    <button id="startAllChaptersScrapingBtn" type="button" class="btn btn-default" onclick="startAllChaptersScraping()">
                        {{ Lang::get('messages.admin.chapter.scraper.start') }}
                    </button>
                    <button id="stopAllChaptersScrapingBtn" type="button" class="btn btn-danger disabled" onclick="abort()">
                        Stop
                    </button>
                    <button id="resumeAllChaptersScrapingBtn" type="button" class="btn btn-primary" onclick="resume()">
                        Check previous status & Resume
                    </button>
                    <a href="" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" title="After finishing scraping click here to notify users">
                        {{ Lang::get('messages.admin.chapter.scraper.notif-users') }}
                    </a>
                    @endif
                </div>

                <br/>
                <div id="waiting" style="display: none;"><center><img src="{{ asset('images/ajax-loader.gif') }}" /></center></div>
                <br/>

                <div id="startScrapingProgressTotal" class="panel panel-default" style="display:none;">
                    <div class="panel-heading">{{ Lang::get('messages.admin.chapter.scraper.total-progress') }}</div>
                    <div class="panel-body">
                        <div class="progress progress-total">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"></div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>

    <script>
        var mangaSlug = '{{$manga->slug}}';
        var mangaId = '{{$manga->id}}';
        var scanList = Array();
        var urls = Array();
        var selectedWebsite;

        var template = "<td>index.</td>" +
                "<td><img class='img-responsive' width='150' src='imageholder' /></td>" +
                "<td>filename</td>" +
                "<td>url</td>";

        var errorTemplate = "<tr id='chapterId-index'>" +
                "<td>index.</td>" +
                "<td colspan='3'>please retry downloading this page by clicking on the 'Retry' button. " +
                "<button type='button' class='btn btn-primary btn-xs pull-right' onclick='retryDownloading(index, chapterId)'>Retry</button></td>" +
                "</tr>";

        var collapseHeader = '<div class="panel-heading" role="tab" id="HEADING-ID">' +
                '<h4 class="panel-title" style="margin-bottom: 5px">' +
                '<a role="button" data-toggle="collapse" data-parent="#accordion" href="#COLLAPSE-ID" aria-expanded="true" aria-controls="COLLAPSE-ID"></a>' +
                '</h4>' +
                '<div class="progress COLLAPSE-ID" style="display:none;">' +
                '<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>' +
                '</div>' +
                '</div>';

        var collapseBody = '<div id="COLLAPSE-ID" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="HEADING-ID">' +
                '<div class="panel-body">' +
                '<table id="table-COLLAPSE-ID" class="table" style="display:none">' +
                '<thead>' +
                '<tr>' +
                "<th>{{ Lang::get('messages.admin.chapter.scraper.header-number') }}</th>" +
                "<th>{{ Lang::get('messages.admin.chapter.scraper.header-scan') }}</th>" +
                "<th>{{ Lang::get('messages.admin.chapter.scraper.header-name') }}</th>" +
                "<th>{{ Lang::get('messages.admin.chapter.scraper.header-url') }}</th>" +
                '</tr>' +
                '</thead>' +
                '<tbody></tbody>' +
                '</table>' +
                '</div>' +
                '</div>';

        $(document).ready(function () {
            selectSource();
            $('#scan-source').change(function () {
                selectSource();
            });

            $('#scraping-mode').change(function () {
                selectSourceBulkMode();
            });
        });

        function selectSourceBulkMode() {
            switch ($('#scraping-mode').val()) {
                case "all-chapters":
                    $('.one-or-many').hide();
                    $('.all-chapters').show();
                    switch ($('#scan-source').val()) {
                        case "mangapanda":
                            $('#mangaHelpBlock span').html("For example, Naruto page in MangaPanda is: <b>http://www.mangapanda.com/naruto</b>");
                            break;
                        case "comicvn":
                            $('#mangaHelpBlock span').html("For example, One Piece page in Comicvn is: <b>http://comicvn.net/truyen-tranh/one-piece-dao-hai-tac/14</b>");
                            break;
                        case "l-e-l":
                            $('#mangaHelpBlock span').html("For example, One Piece page in L-e-L is: <b>http://lecture-en-ligne.com/manga/onepiece/</b>");
                            break;
                        case "pecinta":
                            $('#mangaHelpBlock span').html("For example, One Piece page in PecintaKomik is: <b>http://www.pecintakomik.com/One_Piece/</b>");
                            break;
                        case "3asq":
                            $('#mangaHelpBlock span').html("For example, Naruto page in 3asq is: <b>http://www.3asq.info/one_piece/</b>");
                            break;
                        case "9manga_es":
                            $('#mangaHelpBlock span').html("For example, Tokyo Ghoul page is: <b>http://es.ninemanga.com/manga/Tokyo Ghoul.html</b>");
                            break;
                    }
                    break;
                case "one-or-many":
                    $('.one-or-many').show();
                    $('.all-chapters').hide();
                    switch ($('#scan-source').val()) {
                        case "mangapanda":
                            $('#helpBlock').html("For example, the url of the chapter 700 of Naruto is: <b>http://www.mangapanda.com/naruto/700</b> or <b>http://www.mangareader.net/naruto/700</b>.");
                            break;
                        case "comicvn":
                            $('#helpBlock').html("For example, the url of the chapter 806 of One Piece is: <b>http://comicvn.net/truyen-tranh/one-piece-dao-hai-tac/chapter-806/255041</b>.");
                            break;
                        case "l-e-l":
                            $('#helpBlock').html("For example, chapter 814 of One Piece page in L-e-L is: <b>http://lecture-en-ligne.com/onepiece/814/0/0/1.html</b>");
                            break;
                        case "pecinta":
                            $('#helpBlock').html("For example, chapter 814 of One Piece page in PecintaKomik is: <b>http://www.pecintakomik.com/manga/One_Piece/814</b>");
                            break;
                        case "3asq":
                            $('#helpBlock').html("For example, the url of the chapter 809 of One Piece is: <b>http://www.3asq.info/one_piece/809/</b>.");
                            break;
                        case "9manga_es":
                            $('#helpBlock').html("For example, Tokyo Ghoul chapter 180 is: <b>http://es.ninemanga.com/chapter/Tokyo Ghoul/450605-3.html</b>");
                            break;
                    }
                    break;
                default:
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
            }
        }

        function selectSource() {
            switch ($('#scan-source').val()) {
                case "mangapanda":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['mangapanda']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                case "mangareader":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['mangareader']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                case "comicvn":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['comicvn']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                case "l-e-l":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['l-e-l']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                case "pecinta":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['pecinta']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                case "3asq":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['3asq']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                case "9manga_es":
                    $('.one-or-many').hide();
                    $('.all-chapters').hide();
                    $('.scraping-mode').show();
                    selectedWebsite = "{{$websites['9manga_es']['url']}}";
                    $('#scraping-mode').selectpicker('val', '');
                    break;
                default:
                    $('#scan-source').selectpicker('val', 'mangapanda');
                    selectedWebsite = "{{$websites['mangapanda']['url']}}";
                    $('#helpBlock').html("For example, the url of the chapter 700 of Naruto in MangaPanda is: <b>http://www.mangapanda.com/naruto/700</b>.");
            }
        }

        var bulkObj = {};
        $.xhrPool = [];
        $.xhrPool.abortAll = function () {
            $(this).each(function (idx, jqXHR) {
                jqXHR.abort();
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            });
        };

        $.ajaxSetup({
            beforeSend: function (jqXHR) {
                $.xhrPool.push(jqXHR);
            },
            complete: function (jqXHR) {
                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            }
        });

        // case 1: all

        function startAllChaptersScraping() {
            if ($('.all-chapters').is(':visible') && !$('#mangaPageUrl').val().length) {
                alert('Please enter a chapter URL!');
                return;
            }

            var patt = new RegExp("^" + selectedWebsite);
            if (!patt.test($('#mangaPageUrl').val())) {
                alert('Some of your URLs are invalid!');
                return;
            }

            $('#startScrapingProgressTotal').hide();
            $('#startAllChaptersScrapingBtn').addClass('disabled');
            $('#resumeAllChaptersScrapingBtn').addClass('disabled');
            $('#stopAllChaptersScrapingBtn').removeClass('disabled');
            $('#waiting').show();

            $.ajax({
                type: 'POST',
                url: "{{ action('WebScraperController@getTotalChapters') }}",
                data: {'mangaPageUrl': $('#mangaPageUrl').val()},
                success: function (response) {
                    $('#startScrapingProgressTotal').show();
                    $('#startScrapingProgressTotal .panel-heading').append('<span class="total-page" style="float:right">Total downloaded chapters: <span>0</span> / ' + response.total + '</span>');

                    bulkObj = response;

                    urls = Array();
                    for (i = 0; i < response.content.length; i++) {
                        urls[i] = response.content[i];
                    }

                    getChapters(0);
                }
            });
        }

        function getChapters(k) {
            if (k < urls.length) {
                if (typeof urls[k].scanList != "undefined") {
                    // chapter exist -> show heading
                    if ($('#heading-' + urls[k].chapterId).length == 0) {
                        $('#accordion').append('<div id="heading-' + urls[k].chapterId + '"><h4 class="panel-title" style="display:inline">Chapter #' + urls[k].chapterNumber + ': </h4></div>');
                    }

                    // go to the next chapter 
                    getChapters(k + 1);

                    // continue processing the current chapter
                    download = false;
                    for (j = 0; j < urls[k].scanList.length; j++) {
                        if (typeof urls[k].scanList[j].status == "undefined" || urls[k].scanList[j].status != "ok") {
                            download = true;
                            if ($('#heading-' + urls[k].chapterId + ' h4 span').length == 0) {
                                $('#heading-' + urls[k].chapterId + ' h4').append('<span>' + j + '</span> / ' + urls[k].scanList.length + ' downloaded scan(s)...');
                            }

                            resumeDownloadingImage(k, urls[k].scanList[j].index, urls[k].scanList[j].url, urls[k].chapterId, urls[k].scanList.length);
                        }
                    }

                    // already downloaded?
                    if (!download) {
                        if($('#accordion #heading-' + urls[k].chapterId + ' .download-success').length > 0) {
                            $('#accordion #heading-' + urls[k].chapterId + ' .download-success').remove();
                        }
                        $('#accordion #heading-' + urls[k].chapterId).append('<span class="download-success" style="float: right">Already downloaded...</span>');
                        
                        // progress total
                        var count = parseInt($('#startScrapingProgressTotal .total-page span').text());
                        $('#startScrapingProgressTotal .total-page span').text(count + 1);
                            
                        var progressTotal = Number($('#startScrapingProgressTotal .progress-bar').attr('aria-valuenow')) + (100 / parseInt(urls.length));
                        if (progressTotal > 100) {
                            progressTotal = 100;
                        }

                        $('#startScrapingProgressTotal .progress-bar').css('width', progressTotal + '%').attr('aria-valuenow', progressTotal);
                    }
                } else {
                    $.ajax({
                        type: 'POST',
                        url: "{{ action('WebScraperController@getChapter') }}",
                        data: {'chapterUrl': urls[k].url, 'mangaId': mangaId, 'selectedWebsite': $('#scan-source').val(), 'chapterTitle': urls[k].title},
                        success: function (response) {
                            $('#waiting').hide();
                            $('#startScrapingProgressTotal').show();

                            var chapterId = response.chapterId;
                            var chapterNumber = response.chapterNumber;

                            if (response.contents.length) {
                                scanList[chapterId] = response.contents;

                                $('#accordion').append('<div id="heading-' + chapterId + '"><h4 class="panel-title" style="display:inline">Chapter #' + chapterNumber + ': <span>0</span> / ' + response.contents.length + ' downloaded scan(s)...</h4></div>');

                                bulkObj.content[k].chapterId = chapterId;
                                bulkObj.content[k].chapterNumber = chapterNumber;
                                bulkObj.content[k].scanList = response.contents;

                                getChapterImage(0, chapterId, k);
                            } else {
                                $('#startScrapingProgress').find('.panel-heading').html('<p>There is no scans in this chapter, please check the URL you enter.</p>');
                                $('#startAllChaptersScrapingBtn').addClass('disabled');
                            }
                        },
                        error: function (xhr, status, error) {
                            if (status != "abort") {
                                $('#waiting').hide();
                                $('#startScrapingProgressTotal').show();
                                $('#startScrapingProgressTotal').find('.panel-heading').html('<p>There are errors when processing your request, please retry.</p>');

                                var err = xhr.responseJSON;
                                if (typeof xhr.responseJSON !== "undefined") {
                                    $('#startScrapingProgressTotal .panel-heading').append("Error: " + err.error.message);
                                }
                            }
                        },
                        complete: function (xhr, status) {
                            if (status != "abort")
                                getChapters(k + 1);
                        }
                    });
                }
            }
        }

        function getChapterImage(i, chapterId, k) {
            if (i < scanList[chapterId].length) {
                $.ajax({
                    method: "POST",
                    url: "{{ action('PageController@downloadImageFromUrl') }}",
                    data: {scanURL: scanList[chapterId][i].url, index: scanList[chapterId][i].index, 'mangaSlug': mangaSlug, 'chapterId': chapterId},
                    success: function (response) {
                        var progress = parseInt($('#heading-' + chapterId + ' span').text());
                        $('#heading-' + chapterId + ' h4 span').text(progress + 1);

                        bulkObj.content[k].scanList[i].status = 'ok';
                        if (i === (scanList[chapterId].length - 1)) {
                            bulkObj.content[k].status = 'complete';
                            
                            var count = parseInt($('#startScrapingProgressTotal .total-page span').text());
                            $('#startScrapingProgressTotal .total-page span').text(count + 1);
                        
                            $('#accordion').find('#heading-' + chapterId).append('<span class="download-success" style="float: right">Complete!</span>');
                            var progressTotal = Number($('#startScrapingProgressTotal .progress-bar').attr('aria-valuenow')) + (100 / parseInt(urls.length));
                            if (progressTotal > 100) {
                                progressTotal = 100;
                            }

                            $('#startScrapingProgressTotal .progress-bar').css('width', progressTotal + '%').attr('aria-valuenow', progressTotal);

                            if (urls.length === $('.download-success').length) {
                                alert('Download Complete !');

                                $('#waiting').hide();
                                $('#startAllChaptersScrapingBtn').addClass('disabled');
                                $('#resumeAllChaptersScrapingBtn').addClass('disabled');
                                $('#stopAllChaptersScrapingBtn').addClass('disabled');
                            }

                            saveDownloadStat();
                        }
                    },
                    error: function (xhr, status, error) {
                        var err = xhr.responseJSON;
                        if (typeof xhr.responseJSON !== "undefined") {
                            $('#collapse-' + chapterId + ' table > tbody').append(errorTemplate.replace(/index/g, err.index).replace(/chapterId/g, chapterId));
                        }

                        bulkObj.content[k].scanList[i].status = 'nok';
                        if ($('#accordion').find('#heading-' + chapterId + ' .download-errors').length > 0) {
                            $('#accordion').find('#heading-' + chapterId + ' .download-errors').remove();
                        }

                        if (status == "abort")
                            $('#accordion').find('#heading-' + chapterId).append('<span class="download-errors" style="float: right">/!\\ Aborted</span>');
                        else {
                            $('#accordion').find('#heading-' + chapterId).append('<span class="download-errors" style="float: right">/!\\ Errors</span>');
                        }
                    },
                    complete: function (xhr, status) {
                        if (status != "abort")
                            getChapterImage(i + 1, chapterId, k);
                    }
                });
            }
        }

        // abort scraping
        function abort() {
            $.xhrPool.abortAll();

            saveDownloadStat();

            $('#waiting').hide();
            alert('Download progress has been saved, you can resume later!');

            $('#resumeAllChaptersScrapingBtn').removeClass('disabled');
            $('#stopAllChaptersScrapingBtn').addClass('disabled');
        }

        // save bulk download stat
        function saveDownloadStat() {
            $.ajax({
                method: "POST",
                url: "{{ action('WebScraperController@abort') }}",
                data: {'mangaId': mangaId, 'bulkStatus': JSON.stringify(bulkObj)}
            });
        }

        // resume scraping
        function resume() {
            $('#waiting').show();

            $.ajax({
                method: "POST",
                url: "{{ action('WebScraperController@resume') }}",
                data: {'mangaId': mangaId},
                success: function (response) {
                    noStat = true;
                    if (response.bulkStatus == null || response.bulkStatus.length == 0) {
                        $('#waiting').hide();
                        alert('There is no saved status for this Manga! Click on "Start Scraping" button to start downloading chapters.');
                        return;
                    } else {
                        bulkObj = JSON.parse(response.bulkStatus);
                        for (i = 0; i < bulkObj.content.length; i++) {
                            if (typeof bulkObj.content[i].status == 'undefined' || bulkObj.content[i].status != 'complete') {
                                noStat = false;
                            }
                        }

                        if (noStat) {
                            $('#waiting').hide();
                            alert('All chapters has been downloaded!');
                            return;
                        }
                    }

                    urls = Array();
                    for (i = 0; i < bulkObj.content.length; i++) {
                        urls[i] = bulkObj.content[i];
                    }

                    $('#startScrapingProgressTotal').show();
                    if ($('#startScrapingProgressTotal .total-page').length > 0) {
                            $('#startScrapingProgressTotal .total-page').remove();
                        }
                        
                    $('#startScrapingProgressTotal .panel-heading').append('<span class="total-page" style="float:right">Total downloaded chapters: <span>0</span> / ' + urls.length + '</span>')
                    $('#accordion .download-errors').remove();

                    $('#resumeAllChaptersScrapingBtn').addClass('disabled');
                    $('#stopAllChaptersScrapingBtn').removeClass('disabled');

                    getChapters(0);
                }
            });
        }

        function resumeDownloadingImage(k, i, url, chapterId, totalPage) {
            $.ajax({
                method: "POST",
                url: "{{ action('PageController@downloadImageFromUrl') }}",
                data: {'scanURL': url, 'index': i, 'mangaSlug': mangaSlug, 'chapterId': chapterId},
                success: function (response) {
                    var progress = parseInt($('#heading-' + chapterId + ' span').text()) + 1;
                    $('#heading-' + chapterId + ' h4 span').text(progress);

                    bulkObj.content[k].scanList[parseInt(i) - 1].status = 'ok';
                    if (progress === totalPage) {
                        bulkObj.content[k].status = 'complete';
                        
                        var count = parseInt($('#startScrapingProgressTotal .total-page span').text());
                        $('#startScrapingProgressTotal .total-page span').text(count + 1);
                            
                        $('#accordion').find('#heading-' + chapterId).append('<span class="download-success" style="float: right">Complete!</span>');
                        var progressTotal = Number($('#startScrapingProgressTotal .progress-bar').attr('aria-valuenow')) + (100 / parseInt(urls.length));
                        if (progressTotal > 100) {
                            progressTotal = 100;
                        }

                        $('#startScrapingProgressTotal .progress-bar').css('width', progressTotal + '%').attr('aria-valuenow', progressTotal);

                        if (urls.length === $('.download-success').length) {
                            alert('Download Complete !');

                            $('#waiting').hide();
                            $('#startAllChaptersScrapingBtn').addClass('disabled');
                            $('#resumeAllChaptersScrapingBtn').addClass('disabled');
                            $('#stopAllChaptersScrapingBtn').addClass('disabled');
                        }

                        saveDownloadStat();
                    }
                }
            });
        }


        // case 2: one or many

        function startScraping() {
            if ($('.one-or-many').is(':visible') && !$('#chaptersUrl').val().length) {
                alert('Please enter a chapter URL!');
                return;
            }

            urls = $.trim($('#chaptersUrl').val()).split('\n');
            var patt = new RegExp("^" + selectedWebsite);
            for (i = 0; i < urls.length; i++) {
                if (!patt.test(urls[i])) {
                    alert('Some of your URLs are invalid!');
                    return;
                }
            }

            $('#startScrapingProgressTotal').hide();
            $('#startScrapingBtn').addClass('disabled');
            $('#waiting').show();

            startScarpingChapters(0);
        }

        function startScarpingChapters(k) {
            if (k < urls.length) {
                $.ajax({
                    type: 'POST',
                    url: "{{ action('WebScraperController@startScraper') }}",
                    data: {'chapterUrl': urls[k], 'mangaId': mangaId, 'selectedWebsite': $('#scan-source').val()},
                    success: function (response) {
                        $('#waiting').hide();
                        $('#startScrapingProgressTotal').show();

                        if (response.chapterId === 'exist') {
                            var collapseHeaderLocal = collapseHeader.replace('HEADING-ID', 'heading-' + response.chapterNumber + '-' + k).replace(/COLLAPSE-ID/g, 'collapse-' + response.chapterNumber + '-' + k);
                            var collapseBodyLocal = collapseBody.replace('HEADING-ID', 'heading-' + response.chapterNumber + '-' + k).replace(/COLLAPSE-ID/g, 'collapse-' + response.chapterNumber + '-' + k);
                            $('#accordion').append('<div class="panel panel-default">' + collapseHeaderLocal + collapseBodyLocal + '</div>');
                            $('#accordion').find('#heading-' + response.chapterNumber + '-' + k + ' h4 a').text('Chapter #' + response.chapterNumber + ': Chapter with the same number or slug already exist');

                            $('#collapse-' + response.chapterNumber + '-' + k).collapse('hide');
                            //$('#startScrapingBtn').removeClass('disabled');
                            var progressTotal = Number($('#startScrapingProgressTotal .progress-bar').attr('aria-valuenow')) + (100 / parseInt(urls.length));
                            if (progressTotal > 100) {
                                progressTotal = 100;
                            }

                            $('#startScrapingProgressTotal .progress-bar').css('width', progressTotal + '%').attr('aria-valuenow', progressTotal);
                        } else {
                            var chapterId = response.chapterId;
                            var chapterNumber = response.chapterNumber;

                            if (response.contents.length) {
                                scanList[chapterId] = response.contents;

                                var collapseHeaderLocal = collapseHeader.replace('HEADING-ID', 'heading-' + chapterId).replace(/COLLAPSE-ID/g, 'collapse-' + chapterId);
                                var collapseBodyLocal = collapseBody.replace('HEADING-ID', 'heading-' + chapterId).replace(/COLLAPSE-ID/g, 'collapse-' + chapterId);
                                $('#accordion').append('<div class="panel panel-default">' + collapseHeaderLocal + collapseBodyLocal + '</div>');
                                $('#accordion').find('#heading-' + chapterId + ' h4 a').text('Chapter #' + chapterNumber + ': ' + response.contents.length + ' scans to download...');

                                $('.progress.collapse-' + chapterId).show();
                                $('#collapse-' + chapterId + ' table').show();
                                startDownloading(0, chapterId);
                            } else {
                                $('#startScrapingProgress').find('.panel-heading').html('<p>There is no scans in this chapter, please check the URL you enter.</p>');
                                $('#startScrapingBtn').removeClass('disabled');
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#waiting').hide();
                        $('#startScrapingProgressTotal').show();
                        $('#startScrapingBtn').removeClass('disabled');
                        $('#startScrapingProgressTotal').find('.panel-heading').html('<p>There are errors when processing your request, please retry.</p>');

                        var err = xhr.responseJSON;
                        if (typeof xhr.responseJSON !== "undefined") {
                            $('#startScrapingProgressTotal .panel-heading').append("Error: " + err.error.message);
                        }
                    },
                    complete: function () {
                        startScarpingChapters(k + 1);
                    }
                });
            }
        }

        function startDownloading(i, chapterId) {
            if (i < scanList[chapterId].length) {
                $.ajax({
                    method: "POST",
                    url: "{{ action('PageController@downloadImageFromUrl') }}",
                    data: {scanURL: scanList[chapterId][i].url, index: scanList[chapterId][i].index, 'mangaSlug': mangaSlug, 'chapterId': chapterId},
                    timeout: 40000,
                    success: function (response) {
                        $('#collapse-' + chapterId + ' table > tbody').append("<tr id='" + chapterId + '-' + response.index + "'>" + template.replace(/index/g, response.index).replace('imageholder', response.path).replace('url', response.url).replace('filename', response.filename) + "</tr>");
                        var progress = Number($('.collapse-' + chapterId + ' .progress-bar').attr('aria-valuenow')) + (100 / parseInt(scanList[chapterId].length));
                        if (progress > 100) {
                            progress = 100;
                        }

                        $('.collapse-' + chapterId + ' .progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
                        if (i === (scanList[chapterId].length - 1)) {
                            $('#collapse-' + chapterId).collapse('hide');

                            var progressTotal = Number($('#startScrapingProgressTotal .progress-bar').attr('aria-valuenow')) + (100 / parseInt(urls.length));
                            if (progressTotal > 100) {
                                progressTotal = 100;
                            }

                            $('#startScrapingProgressTotal .progress-bar').css('width', progressTotal + '%').attr('aria-valuenow', progressTotal);
                        }
                    },
                    error: function (xhr, status, error) {
                        if (status === "timeout") {
                            $('#collapse-' + chapterId + ' table > tbody').append(errorTemplate.replace(/index/g, scanList[chapterId][i].index).replace(/chapterId/g, chapterId));
                        } else {
                            var err = xhr.responseJSON;
                            if (typeof xhr.responseJSON !== "undefined") {
                                $('#collapse-' + chapterId + ' table > tbody').append(errorTemplate.replace(/index/g, err.index).replace(/chapterId/g, chapterId));
                            }
                        }

                        if ($('#accordion').find('#heading-' + chapterId + ' h4 a .download-errors').length > 0) {
                            $('#accordion').find('#heading-' + chapterId + ' h4 a .download-errors').remove();
                        }

                        $('#accordion').find('#heading-' + chapterId + ' h4 a').append('<span class="download-errors" style="float: right">/!\\ Errors</span>');
                    },
                    complete: function () {
                        startDownloading(i + 1, chapterId);
                    }
                });
            }
        }

        function retryDownloading(i, chapterId) {
            $.ajax({
                method: "POST",
                url: "{{ action('PageController@downloadImageFromUrl', array('mangaSlug' => $manga->slug)) }}",
                data: {scanURL: scanList[chapterId][parseInt(i) - 1].url, index: i, 'mangaSlug': mangaSlug, 'chapterId': chapterId},
                timeout: 40000,
                success: function (response) {
                    $('#collapse-' + chapterId + ' table tr[id="' + chapterId + '-' + response.index + '"]').html('').append(template.replace(/index/g, response.index).replace('imageholder', response.path).replace('url', response.url).replace('filename', response.filename));
                    var progress = Number($('.collapse-' + chapterId + ' .progress-bar').attr('aria-valuenow')) + (Math.round(1000 / parseInt(scanList[chapterId].length)) / 10);
                    if (progress > 100) {
                        progress = 100;
                    }

                    $('.collapse-' + chapterId + ' .progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
                }
            });
        }
        
        function notifyUsers() {
            $.ajax({
                method: "POST",
                url: "{{ action('ChapterController@notifyUsers') }}",
                data: {mangaId: mangaId, 'mangaSlug': mangaSlug},
                success: function (response) {
                    if(response.status == "ok") {
                        $('#myModal').modal('hide');
                        $('.modal-success').modal('show');
                    }
                }
            });
        }
    </script>
    
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="z-index: 9999">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ Lang::get('messages.admin.chapter.scraper.notif-users') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row control-group">
                        <div class="form-group col-xs-12">
                            <p>{{ Lang::get('messages.admin.chapter.scraper.notif-users-message') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('messages.front.reader.close') }}</button>
                    <button type="button" class="btn btn-primary" onclick="notifyUsers()">{{ Lang::get('messages.admin.chapter.scraper.notif-users.notify') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-success" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" style="z-index: 9999">
            <div class="modal-content">
                <div class="alert text-center alert-info ">
                    {{ Lang::get('messages.front.reader.sentSuccess') }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
