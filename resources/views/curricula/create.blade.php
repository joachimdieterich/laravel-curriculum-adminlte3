@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('global.curriculum.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("curricula.store") }}" method="POST" enctype="multipart/form-data">
            <form
            method="POST"
            action="/curricula"
        >
            @include ('curricula.form', [
                'organization' => new App\Curriculum,
                'buttonText' =>  trans('global.create'). ' ' .trans('global.curriculum.title_singular')
            ]) 
        </form>
    </div>
</div>
@endsection