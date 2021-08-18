@extends('front.layouts.colorful')

@section('title')
{{ Lang::get('messages.front.home.title', array('sitename' => isset($settings['seo.title']) ?: "sitename")) }}
@stop

@section('description')
{{ isset($settings['seo.description']) ?: "description" }}
@stop

@section('keywords')
{{ isset($settings['seo.keywords']) ?: "keywords" }}
@stop

@section('header')
<?php
// echo Jraty::js();

// echo Jraty::js_init(array(
//     'score' => 'function() { return $(this).attr(\'data-score\'); }',
//     'number' => 5,
//     'click' => 'function(score, evt) {
//                 $.post(\'save/item_rating\',{
//                     item_id: $(this).attr(\'data-item\'),
//                     score: score
//                 });
//               }',
//     'path' => '\'packages/escapeboy/jraty/raty/lib/img\''
// ));
?>
@stop

@include('front.themes.'.$theme.'.blocs.menu')
@include('front.themes.'.$theme.'.blocs.hot_manga')
@include('front.themes.'.$theme.'.blocs.content')
@include('front.themes.'.$theme.'.blocs.sidebar')