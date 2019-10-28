@extends('layouts.master')
@section('title')
    admin home
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">admin home</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
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