@extends('layouts.master')
@section('title')
    {{ trans('global.organization.edit') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.organization.edit') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
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
                        "field" => "street",
                        "placeholder" => "Curriculumstreet 1",
                        "value" => old('street', isset($organization) ? $organization->street : '')])

            @include ('forms.input.text',
                        ["model" => "organization",
                        "field" => "postcode",
                        "placeholder" => "76831",
                        "value" => old('postcode', isset($organization) ? $organization->postcode : '')])

            @include ('forms.input.text',
                        ["model" => "organization",
                        "field" => "city",
                        "placeholder" => "Ilbesheim",
                        "value" => old('city', isset($organization) ? $organization->city : '')])

            @include ('forms.input.text',
                        ["model" => "organization",
                        "field" => "phone",
                        "placeholder" => "123/4567890",
                        "value" => old('phone', isset($organization) ? $organization->phone : '')])

            @include ('forms.input.text',
                        ["model" => "organization",
                        "field" => "email",
                        "placeholder" => "mail@curriculumonline.de",
                        "value" => old('email', isset($organization) ? $organization->email : '')])

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
