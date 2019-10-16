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
            <label for="imports" class="col-md-4 col-form-label text-md-right">
                {{ __('Import (optional)') }}
            </label>

            <div class="col-md-6">
                <input id="imports" type="file" name="imports[]" class="form-control" multiple>
            </div>
            
        </div>
        <div>
            <input class="btn btn-info" type="submit" value="importieren...">
        </div>
        </form>
    </div>
</div>
@endsection