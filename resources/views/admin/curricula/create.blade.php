@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.curriculum.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.curricula.store") }}" method="POST" enctype="multipart/form-data">
            <form
            method="POST"
            action="/curricula"
        >
            @include ('admin.curricula.form', [
                'organization' => new App\Curriculum,
                'buttonText' =>  trans('global.create'). ' ' .trans('global.curriculum.title_singular')
            ])
        </form>
        </form>
    </div>
</div>
@endsection