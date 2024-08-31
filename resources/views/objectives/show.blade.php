@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.details') }}
@endsection
@section('breadcrumb')
    @if (Auth::user()->id == env('GUEST_USER'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> Str::limit($curriculum->title, 10) ]
        ])}}"
        ></breadcrumbs>
    @else
        <breadcrumbs
            :entries="{{json_encode([
                ['active'=> false, 'title'=> trans('global.curriculum.title_singular'), 'url' => "/curricula"],
                ['active'=> true, 'title'=> Str::limit($objective->curriculum->title, 10), 'url' => "/curricula/" . $objective->curriculum->id],
                ['active'=> true, 'title'=> trans('global.details') ]
            ])}}"
        ></breadcrumbs>
    @endif
@endsection
@section('content')
    <objective
        ref="curriculumView"
        :repository="{{ $repository }}"
        :objective="{{ $objective }}"
    ></objective>

    {{--<terminal-objective-modal></terminal-objective-modal>
    <enabling-objective-modal></enabling-objective-modal>
    <content-modal></content-modal>
    <!--<objective-medium-modal></objective-medium-modal>-->
    <medium-modal></medium-modal>
    <medium-create-modal></medium-create-modal>
    <reference-objective-modal></reference-objective-modal>
    <prerequisite-modal></prerequisite-modal>--}}
@endsection
