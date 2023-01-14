@extends('layouts.master')
@section('title')
    {{ trans('global.organization.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.organization.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">
            <form action="{{ route("organizations.updateAddress", [$organization->id]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                @include ('forms.input.text',
                            ["model" => "organization",
                            "field" => "lms_url",
                            "placeholder" => "https:\\\[your_lms_url]",
                            "value" => old('lms_url', isset($organization) ? $organization->lms_url : '')])

                <div>
                    <input
                        id="organization-save"
                        class="btn btn-info"
                        type="submit"
                        value="{{ trans('global.organization.edit') }}"
                    >
                </div>

            </form>
        </div>
    </div>

@endsection
