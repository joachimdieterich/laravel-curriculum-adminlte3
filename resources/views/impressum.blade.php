@extends('layouts.app')
@section('content')

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ $impressum->title }}</h2>
            <div>{{ $impressum->content }}</div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent

@endsection