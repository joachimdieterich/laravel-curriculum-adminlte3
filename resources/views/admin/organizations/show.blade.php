@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-4">
        <div class="card card-primary">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <h3>
                            About
                        </h3>
                    </div>
                    <div class="col-2">
                        @can('organization_edit')
                             <a href="{{ route('admin.organizations.edit', $organization->id) }}" >
                                <i class="far fa-edit p-2"></i>
                             </a> 
                        @endcan 
                    </div>
                </div>
            </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fa fa-university mr-1"></i> {{ $organization->title }}</strong>

                <p class="text-muted">
                  {{ $organization->type->title }}
                </p>

                <hr>

                <strong><i class="fa fa-map-marker mr-1"></i> Location</strong>

                <p class="text-muted">
                    {{ $organization->street }}<br>
                    {{ $organization->postcode }} {{ $organization->city }}<br>
                    {{ $organization->state->lang_de }}, {{ $organization->country->lang_de }}
                </p>

                <hr>

                <strong><i class="fa fa-phone mr-1"></i> Contact</strong>

                <p class="text-muted">
                  {{ trans('global.organization.fields.phone') }}: {{ $organization->phone }}<br>
                  {{ trans('global.organization.fields.email') }}: {{ $organization->email }}
                </p>

                <hr>

                <strong><i class="fa fa-file-alt mr-1"></i> {{ trans('global.organization.fields.description') }}</strong>

                <p class="text-muted">{{ strip_tags( $organization->description) }}</p>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <div class="float-left">
                    <button type="button" 
                            class="btn-xs btn-block btn-{{ $organization->status()->first()->color_css_class }} pull-right">
                        {{ $organization->status()->first()->lang_de }}
                    </button>            
                </div>
                <small class="float-right">
                    {{ $organization->updated_at }}
                </small> 
              </div>
        </div>
    </div>
    
    <div class="col-md-8">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active show" href="#activity" data-toggle="tab">Activity</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active show" id="activity">
                    
                    Activity Tab

                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                    timeline
                  </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
                    Organisational Settings
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
    
    
</div>
@endsection
