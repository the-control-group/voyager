@extends('voyager::master')

@section('css')
    <style>
        .user-email {
            font-size: .85rem;
            margin-bottom: 1.5em;
        }
    </style>
@stop

@section('content')
    <div style="background-size:cover; background-image: url({{ Voyager::image( Voyager::setting('admin.bg_image'), voyager_asset('/images/bg.jpg')) }}); background-position: center center;position:absolute; top:0; left:0; width:100%; height:300px;"></div>
    <div style="height:160px; display:block; width:100%"></div>
    <div style="position:relative; z-index:9; text-align:center;">
        <img src="@if( !filter_var(auth()->user()->avatar, FILTER_VALIDATE_URL)){{ Voyager::image( auth()->user()->avatar ) }}@else{{ auth()->user()->avatar }}@endif"
             class="avatar"
             style="border-radius:50%; width:150px; height:150px; border:5px solid #fff;"
             alt="{{ auth()->user()->name }} avatar">
        <h4>{{ ucwords(auth()->user()->name) }}</h4>
        <div class="user-email text-muted">{{ ucwords(auth()->user()->email) }}</div>
        <p>{{ auth()->user()->bio }}</p>
        @if ($route != '')
            <a href="{{ $route }}" class="btn btn-primary">{{ __('voyager::profile.edit') }}</a>
        @endif
    </div>
@stop
