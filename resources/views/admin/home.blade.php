@extends('layouts.master')
@section('title')
    admin home
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">admin home</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                Admin Home
            </div>
        </div>
    </div>

@endsection
@section('scripts')
@parent

@endsection
