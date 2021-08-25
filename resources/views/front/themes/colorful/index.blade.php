@extends('front.layouts.colorful')

@section('title')
{{ Lang::get('messages.front.home.title', array('sitename' => isset($settings['seo.title']) ? $settings['seo.title'] : "sitename")) }}
@stop

@section('description')
{{ isset($settings['seo.description']) ? $settings['seo.description'] : "description" }}
@stop

@section('keywords')
{{ isset($settings['seo.keywords']) ? $settings['seo.keywords'] : "keywords" }}
@stop

@include('front.themes.'.$theme.'.blocs.menu')
@include('front.themes.'.$theme.'.blocs.hot_manga')
@include('front.themes.'.$theme.'.blocs.content')
@include('front.themes.'.$theme.'.blocs.sidebar')