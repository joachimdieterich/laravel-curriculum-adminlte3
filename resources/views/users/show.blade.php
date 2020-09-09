@extends('layouts.master')
@section('title')
    {{ trans('global.myProfile') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ trans('global.myProfile') }}</li>
    <li class="breadcrumb-item "><a href="/documentation" class="text-black-50"><i class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-3 col-sm-12">
        <div class="card card-primary card-outline">
        <div class="card-body box-profile">
                <div id="lfm" data-input="thumbnail" data-preview="holder" class="text-center">
                    <img id="holder"
                         class="profile-user-img img-fluid img-circle"
                         style="height:100px;"
                         src="{{ ($user->medium_id !== null) ? '/media/'.$user->medium_id  : Avatar::create($user->fullName())->toBase64() }}"
                         alt="User profile picture">
                </div>
                <input id="thumbnail"
                       name="filepath"
                       class="invisible"
                       type="text"
                       onchange="setAvatar();"
                       >

                <h3 class="profile-username text-center">{{ $user->firstname }} {{ $user->lastname }}</h3>

                <p class="text-muted text-center">{{ $user->username }} ({{ (null !== $user->currentRole()->first()) ? $user->currentRole()->first()->title : '' }})</p>
            @can('user_edit')
                <a class="float-right link-muted" href="{{ route('users.edit', $user->id) }}" >
                    <i class="far fa-edit"></i>
                </a>
            @endcan
              {{--  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Accomplished</b> <a class="float-right">1,322</a>
                    </li>
                </ul>--}}
            </div>
            <!-- /.card-body -->
        </div>

        <div class="card card-primary">

            <!-- /.card-header -->
            <div class="card-body">
                <strong><i class="fa fa-university mr-1"></i>{{ trans('global.organization.title_singular') }}</strong>
                    <ul class="pl-4">
                    @foreach($user->organizations as $id => $organizations)
                        <li class="small">{{$organizations->title}} @ {{ $user->roles()->where('organization_id', $organizations->id)->first()->title }}</li>
                    @endforeach
                    </ul>
                <hr>

                <strong><i class="fa fa-users mr-1"></i>{{ trans('global.group.title_singular') }}</strong>
                    <ul class="pl-4">
                    @foreach($user->groups as $id => $groups)
                        <li class="small">{{$groups->title}} @ {{ $groups->organization->title }}</li>
                    @endforeach
                    </ul>
                <hr>
                <strong><i class="fa fa-key mr-1"></i>{{ trans('global.roles') }}</strong>
                    <ul class="pl-4">
                    @foreach($user->roles as $id => $roles)
                        <li class="small">{{$roles->title}} @ {{ $user->organizations()->where('role_id', $roles->id)->first()->title }}</li>
                    @endforeach
                    </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                {{--<div class="float-left">
                    <button type="button" class="btn-xs btn-block btn-{{$status_definitions[$user->status_id]->color_css_class}} pull-right">{{$status_definitions[$user->status_id]->lang_de}}</button>
                </div>--}}
                <small class="float-right">
                    {{ $user->updated_at }}
                </small>
            </div>
        </div>
    </div>

    <div class="col-lg-9 col-sm-12">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active show" href="#contact" data-toggle="tab">{{ trans('global.contactdetail.title_singular') }}</a></li>
                </ul>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="contact">
                        @if(auth()->user()->contactDetail != null)
                            @include('partials.users.contactdetails', [
                                'contactdetail' => auth()->user()->contactDetail,
                                'organization'  => \App\Organization::find(auth()->user()->current_organization_id)
                                ])
                        @else
                            <a
                                id="add-plan"
                                class="btn btn-success"
                                href="{{ route("contactdetails.create") }}">
                                {{ trans('global.contactdetail.create') }}
                            </a>
                        @endif
                    </div><!-- /.tab-pane -->

                </div><!-- /.tab-content -->
            </div><!-- /.card-body -->
        </div><!-- /.card -->

        @if (auth()->user()->role()->id == 1)
        <div class="card">
            <div class="card-header p-2">
                Debug
            </div><!-- /.card-header -->
            <div class="card-body">
                User
                {{ App\User::find($user->id) }}<br/><br/>
                currentCurriculaEnrolments:
                {{ App\User::find($user->id)->currentCurriculaEnrolments()->pluck('id') }}<br/><br/>
                currentGroupEnrolments:
                {{ App\User::find($user->id)->currentGroupEnrolments }}<br/><br/>
                Groups:
                {{ App\User::find($user->id)->groups }}<br/><br/>
                organizations:
                {{ App\User::find($user->id)->organizations->pluck('id','title')}}
            </div><!-- /.card-body -->
        </div><!-- /.card -->
            @endif
    </div>


</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready( function () {
    $('#lfm').filemanager('files');
});

function setAvatar()
{
    $.ajax({
        headers: {'x-csrf-token': _token},
            method: 'POST',
            url: "{{ route('users.setAvatar') }}",
            data: {
                filepath: $('#thumbnail').val(),
                _method: 'PATCH',
            }
    })
    .done(function () { location.reload() })
}
</script>

@endsection
