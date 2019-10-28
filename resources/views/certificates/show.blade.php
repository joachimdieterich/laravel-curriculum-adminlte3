@extends('layouts.master')
@section('title')
    {{ trans('global.certificate.title_singular') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.certificate.title_singular') }}</li>
    <li class="breadcrumb-item "> <i class="fas fa-question-circle"></i></li>
@endsection
@section('content')
<div class="row">
   Todo Preview for 
   {{ $certificate }}
</div>

@endsection
