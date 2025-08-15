@extends('layouts.app')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12 p4">
            <h2>{{ $terms->title }}</h2>
            <div>{{ $terms->content }}</div>

        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent

@endsection


