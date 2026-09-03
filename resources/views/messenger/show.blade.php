@extends('layouts.master')
@section('title')
    {{ trans('global.message.title_singular') }}
@endsection
@section('content')
    @include('messenger.partials.flash')
    <div class="row">
        @include('messenger.partials.menu')
        <div class="col-md-9">
            @each('messenger.partials.messages', $thread->messages, 'message')
            @include('messenger.partials.form-message')
        </div>
    </div>
@endsection