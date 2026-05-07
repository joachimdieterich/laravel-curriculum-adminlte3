@extends((Auth::user()->id == config('app.guest_user_id')) ? 'layouts.contentonly' : 'layouts.master')

@section('breadcrumb')
    @if (Auth::user()->id == config('app.guest_user_id'))
        <breadcrumbs
            :entries="{{json_encode([
            ['active'=> true, 'title'=> Str::limit($objective->curriculum->title, 10) ]
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

@section('title')
    <title-component
        :show-back-button="true"
        back-button-title="global.back_to_curriculum"
        :back-button-url="{{ json_encode('/curricula/' . $objective->curriculum_id) }}"
    ></title-component>
@endsection

@section('content')
    <objective
        ref="curriculumView"
        :repository="{{ $repository }}"
        :objective="{{ $objective }}"
        :editable="{{ json_encode($editable) }}"
    ></objective>
@endsection
