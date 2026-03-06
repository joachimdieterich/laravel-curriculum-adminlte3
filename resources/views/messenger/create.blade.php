@extends('layouts.master')

@section('title')
    {{ trans('global.message.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.message.create') }}</li>
    <li class="breadcrumb-item">
        <a href="{{ config('app.documentation_url') }}" class="text-black-50" aria-label="{{ trans('global.documentation') }}">
            <i class="fas fa-question-circle"></i>
        </a>
    </li>
@endsection
@section('content')
    <div class="row">
        @include('messenger.partials.menu')
        <div class="col-md-9">
            <form action="{{ route('messages.store') }}" method="post">
                {{ csrf_field() }}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('global.message.compose') }}</h3>
                    </div>

                    <div class="card-body">
                        @include (
                            'forms.input.select',
                            [
                                "model" => "user",
                                "show_label" => true,
                                "class_left" => "col-12 px-0",
                                "placeholder" => trans('global.message.to') ,
                                "field" => "recipients",
                                "options"=> $users,
                                "multiple" => true,
                                "option_id" => "id",
                                "option_label"=> "username",
                                "value" => old('id', isset($recipients->id) ? $recipients->id : '')
                            ]
                        )

                        <div class="form-group">
                            <input
                                class="form-control"
                                name="subject"
                                placeholder="{{ trans('global.message.fields.subject') }}"
                                value="{{ old('subject') }}"
                            />
                        </div>
                        <div class="form-group">
                            <textarea
                                id="message"
                                name="message"
                                class="form-control"
                                style="height: 300px"
                                placeholder="{{ trans('global.message.fields.message') }}"
                            >
                                {{ old('message') }}
                            </textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="float-right">
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="far fa-envelope"></i>
                                {{ trans('global.message.send') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection