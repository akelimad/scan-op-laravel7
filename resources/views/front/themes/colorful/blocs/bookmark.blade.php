@extends('front.layouts.colorful')

@section('title')
{{$settings['seo.title']}} | 
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<div class="alert alert-info" role="alert">
    <input id="bookmarks-notify" type="checkbox" value="1" 
    	<?php if (Auth::user()->notify == 1): ?> checked="checked" <?php endif ?> /> {{Lang::get('messages.front.myprofil.bookmarks-notification')}}
</div>

<h2 class="widget-title">{{ Lang::get('messages.front.bookmarks.title') }}</h2>

<div class="row" style="background-color: #fff; padding: 20px 0">
    <div class="col-xs-12">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#currently-reading" aria-controls="currently-reading" role="tab" data-toggle="tab">{{Lang::get('messages.front.bookmarks.currently-reading')}}</a>
            </li>
            <li role="presentation">
                <a href="#completed" aria-controls="completed" role="tab" data-toggle="tab">{{Lang::get('messages.front.bookmarks.completed')}}</a>
            </li>
            <li role="presentation">
                <a href="#on-hold" aria-controls="on-hold" role="tab" data-toggle="tab">{{Lang::get('messages.front.bookmarks.on-hold')}}</a>
            </li>
            <li role="presentation">
                <a href="#plan-to-read" aria-controls="plan-to-read" role="tab" data-toggle="tab">{{Lang::get('messages.front.bookmarks.plan-to-read')}}</a>
            </li>
            <li role="presentation">
                <a href="#all" aria-controls="all" role="tab" data-toggle="tab">{{Lang::get('messages.front.bookmarks.all')}}</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="currently-reading">
                @include('front.themes.'.$theme.'.blocs.bookmark_frag')
            </div>
            <div role="tabpanel" class="tab-pane" id="completed">
            </div>
            <div role="tabpanel" class="tab-pane" id="on-hold">
            </div>
            <div role="tabpanel" class="tab-pane" id="plan-to-read">
            </div>
            <div role="tabpanel" class="tab-pane" id="all">
                
            </div>
        </div>
    </div>
</div>
<script>
    checked = Array();
    
    $(document).ready(function() {
        $('#bookmarks-notify').click(function () {
            $.ajax({
	            url: '{{action("BookmarkController@saveNotificationOption")}}',
	            method: 'POST',
	            data: {
	                'bookmarks-notify': $('#bookmarks-notify').is(':checked')
	            }
	        });
        });
        
        $('.nav-tabs a').click(function (e) {
            e.preventDefault();
            
            loadTabData($(this).attr("aria-controls"));
        });
        
        $('.tab-content').on('click', 'input.move', function () {
            status = $('.tab-content select').val();
            activeTab = $('.nav-tabs li.active a').attr('aria-controls');
            
            if(checked.length == 0){
               alert("{{Lang::get('messages.front.bookmarks.select-manga')}}");
            } else {
                if(status != activeTab) {
                    changeStatus(status);
                } else {
                    alert("{{Lang::get('messages.front.bookmarks.different-status')}}");
                }
            }
        });
        
        $('.tab-content').on('click', 'input.delete', function () {            
            if(checked.length == 0){
               alert('Select at least one manga!');
            } else {
                if(confirm("{{Lang::get('messages.front.bookmarks.confirm-delete')}}")) {
                    deleteChecked();
                }
            }
        });
        
        $('.tab-content').on('click', 'input[type="checkbox"]', function () {
            if($(this).prop('checked') == true){
                if($(this).val() == 'all') {
                    checked = Array();
                    $('table input[type="checkbox"]').each(function(){
                        $(this).prop('checked', 'checked');
                        checked.push($(this).val());
                    });
                } else {
                    checked.push($(this).val());
                    allChecked = true;
                    $('table input[type="checkbox"]').each(function(){
                        if($(this).prop('checked') != true) {
                            allChecked = false;
                        }
                    });
                    
                    $('.tab-content input.all').prop('checked', allChecked);
                }
            } else {
                if($(this).val() == 'all') {
                    checked = Array();
                    $('table input[type="checkbox"]').each(function(){
                        $(this).prop('checked', '');
                    });
                } else if(checked.indexOf($(this).val()) != -1) {
                    checked.splice(checked.indexOf($(this).val()), 1);
                    $('.tab-content input.all').prop('checked', '');
                }
            }
        });
    });
    
    waiting = '<div style="padding: 10px 0;" class="text-center">' +
                    '<img src="{{ asset('images/ajax-loader.gif') }}" />'+
                '</div>';
                
    function loadTabData(status) {
        $('#' + status).html(waiting);
        
        status_name = status != 'all' ? status : '';
        $.ajax({
            url: 'loadTabData',
            data: {'status': status_name}
        }).done(function(data) {
            $('.tab-pane').html('');
            $('#' + status).html(data);
            checked = Array();
        });
    }
    
    function changeStatus(status) {
        $.ajax({
            url: "changeStatus",
            method: 'POST',
            data: {
                'ids': checked.join(),
                'status': status
            },
            success: function(response){
                if(response.status == 'ok') {
                    loadTabData($('.nav-tabs li.active a').attr('aria-controls'));
                    alert("{{Lang::get('messages.front.bookmarks.status-changed')}}");
                }
            },
            error: function(response){
                alert("{{Lang::get('messages.front.bookmarks.error')}}");
            }
        });
    }
    
    function deleteChecked() {
        $.ajax({
            url: "deleteChecked",
            method: 'POST',
            data: {
                'ids': checked.join(),
            },
            success: function(response){
                if(response.status == 'ok') {
                    loadTabData($('.nav-tabs li.active a').attr('aria-controls'));
                }
            },
            error: function(response){
                alert("{{Lang::get('messages.front.bookmarks.error')}}");
            }
        });
    }
</script>
@stop
