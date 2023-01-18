@extends('layouts.master')

@section('title')
    {{ trans('global.message.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.message.create') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
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
                <!-- /.card-header -->
                <div class="card-body">
                @include ('forms.input.select',
                ["model" => "user",
                "show_label" => true,
                "class_left" => "col-12 px-0",
                "placeholder" => trans('global.message.to') ,
                "field" => "recipients",
                "options"=> $users,
                "multiple" => true,
                "option_id" => "id",
                "option_label"=> "username",
                "value" => old('id', isset($recipients->id) ? $recipients->id : '') ])
                <!--                     @if($users->count() > 0)
                    <div class="checkbox">
@foreach($users as $user)
                        <label title="{{ $user->username }}"><input type="checkbox" name="recipients[]"
                                                                    value="{{ $user->id }}">{!!$user->username!!}</label>
                        @endforeach
                        </div>
                        @endif-->
                        <div class="form-group">
                            <input
                                class="form-control"
                                name="subject"
                                placeholder="{{ trans('global.message.fields.subject') }}"
                                value="{{ old('subject') }}">
                        </div>
                        <div class="form-group">
                        <textarea
                            id="message"
                            name="message"
                            placeholder="{{ trans('global.message.fields.message') }}"
                            class="form-control"
                            style="height: 300px">
                        {{ old('message') }}
                        </textarea>
                    </div>
<!--                    <div class="form-group">
                        <div class="btn btn-default btn-file">
                            <i class="fas fa-paperclip"></i> Attachment
                            <input type="file" name="attachment">
                        </div>
                        <p class="help-block">Max. 32MB</p>
                    </div>-->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="float-right">
                        <!--<button type="button" class="btn btn-default"><i class="fas fa-pencil-alt"></i> Draft</button>-->
                        <button type="submit" class="btn btn-primary"><i class="far fa-envelope"></i> {{ trans('global.message.send') }}</button>
                    </div>
                    <!--<button type="reset" class="btn btn-default"><i class="fas fa-times"></i> Discard</button>-->
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </form>
    </div>
    <!-- /.col -->
</div>
@endsection
