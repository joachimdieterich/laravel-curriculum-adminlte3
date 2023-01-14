@extends('layouts.master')

@section('title')
    {{ trans('global.message.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.message.title_singular') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    @include('messenger.partials.flash')
    <div class="row">
        @include('messenger.partials.menu')
        <div class="col-md-9">
            @each('messenger.partials.messages', $thread->messages, 'message')

            @include('messenger.partials.form-message')


        </div>
    <!-- /.col -->
</div>
@endsection
