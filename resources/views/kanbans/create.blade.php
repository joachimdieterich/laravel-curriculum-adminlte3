@extends('layouts.master')
@section('title')
    {{ trans('global.kanban.create') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.kanban.create') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("kanbans.store") }}"
                  method="POST"
                  enctype="multipart/form-data">
                @include ('kanbans.form', [
                    'kanban' => new App\Kanban,
                    'buttonText' => trans('global.kanban.create')
                ])
                @if (isset($_GET['subscribable_id']))
                    <input style="display:none" name="subscribable_id" value="{{ $_GET['subscribable_id']}}">
                    <input style="display:none" name="subscribable_type" value="{{ $_GET['subscribable_type']}}">
                @endif
            </form>
    </div>
</div>
@endsection
