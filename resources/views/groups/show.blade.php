@extends('layouts.master')
@section('title')
    {{ $group->title }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ $group->title }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"                                    aria-label="{{ trans('global.documentation') }}"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <group-view
                :group="{{ $group }}"
                :courses="{{ $courses }}"
            >
            </group-view>
            {{--
                    @foreach($group->curricula as $id => $curriculum)
                        @include ('navigators.views.items.item', [ 'item' => $curriculum, 'onclick' => "location.href='/curricula/{$curriculum->id}';" , 'readonly' => true])
                    @endforeach--}}
    </div>
</div>
@endsection
