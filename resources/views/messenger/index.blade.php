@extends('layouts.master')

@section('title')
    {{ trans('global.message.inbox') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.message.inbox') }}</li>
    <li class="breadcrumb-item ">
        <a href="{{ config('app.documentation_url') }}" class="text-black-50" aria-label="{{ trans('global.documentation') }}">
            <i class="fas fa-question-circle"></i>
        </a>
    </li>
@endsection
@section('content')
    @include('messenger.partials.flash')
    <div class="row">
        @include('messenger.partials.menu')
        <div class="col-md-9">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('global.message.inbox') }}</h3>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped">
                            <tbody>
                                @each('messenger.partials.message', $threads, 'thread', 'messenger.partials.no-messages')
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer p-0"></div>
            </div>
        </div>
    </div>
@endsection