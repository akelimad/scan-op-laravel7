@extends('front.layouts.default')

@section('title')
{{ Lang::get('messages.front.home.title', array('sitename' => isset($settings['seo.title']) ? $settings['seo.title'] : "Seo Title" )) }}
@stop

@section('description')
{{ isset($settings['seo.description']) ? $settings['seo.description'] : "Description" }}
@stop

@section('keywords')
{{ isset($settings['seo.keywords']) ? $settings['seo.keywords'] : "keyword" }}
@stop

@include('front.themes.'.$theme.'.blocs.menu')
@include('front.themes.'.$theme.'.blocs.hot_manga')
@include('front.themes.'.$theme.'.blocs.content')
@include('front.themes.'.$theme.'.blocs.sidebar')