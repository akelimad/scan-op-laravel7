@extends('front.layouts.colorful')

@section('title')
{{$settings['seo.title']}} | {{ Lang::get('messages.front.myprofil.my-profil')}} {{$user->username}}
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<div class="row" style="background-color: #fff; padding: 20px 0; margin: 0;">
    <div class="col-xs-12">
        <div class="page-header">
            <h3>
                {{ Lang::get('messages.front.myprofil.my-profil')}} <small>{{$user->username}}</small>
                @if(Confide::user() && Confide::user()->id == $user->id)
                <a class="btn-sm btn-default pull-left" href="{{route('user.profil.edit', $user->id)}}">
                    <i class="glyphicon glyphicon-edit"></i> {{ Lang::get('messages.front.myprofil.edit') }}
                </a>
                @endif
            </h3>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-3">
                @if($user->avatar == 1)
                <img class="thumbnail" src='{{asset("uploads/users/{$user->id}/avatar.jpg")}}' alt='{{$user->avatar}}'>
                @else
                <img width="200" height="200" class="placeholder" src="{{asset('uploads/users/placeholder.png')}}" alt="avatar placeholder"/>
                @endif
            </div>
            <div class="col-xs-12 col-sm-9">
                <dl class="dl-horizontal">
                    <dt>{{ Lang::get('messages.front.myprofil.member-since') }}</dt>
                    <dd>{{ App::make("HelperController")->formateCreationDate($user->created_at) }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@stop
