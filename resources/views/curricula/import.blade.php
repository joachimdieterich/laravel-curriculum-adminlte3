@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.curriculum.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("curricula.import.store") }}" method="POST" enctype="multipart/form-data">
            <form
            method="POST"
            action="/curricula"
        >
        @csrf   
        <div class="form-group row">
            <label for="import" class="col-md-4 col-form-label text-md-right">
                {{ __('Import (optional)') }}
            </label>

            <div class="col-md-6">
                <input id="import" type="file" class="form-control" name="import">
            </div>
            
        </div>
        <div>
            <input class="btn btn-info" type="submit" value="importieren...">
        </div>
        </form>
    </div>
</div>
@endsection