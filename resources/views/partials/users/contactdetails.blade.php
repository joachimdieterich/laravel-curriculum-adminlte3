
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <h5 class="m-0">
                <i class="far fa-id-card mr-1"></i> 
                 {{ $contactdetail->owner->fullname() }}
            </h5>
        </div>
        @can('contactdetail_edit')
        <div class="card-tools pr-2 no-print">   
            <a href="{{route('contactdetails.edit', $contactdetail->id) }}" class="link-muted" >
                <i class="far fa-edit"></i>
            </a>  
        </div>
        @endcan
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <strong><i class="fas fa-envelope mr-1"></i> {{ trans('global.contactdetail.fields.email')}} </strong>
        <p class="text-muted">{{ $contactdetail->email }}</p>
        
        <hr>
        
        <strong><i class="fas fa-phone mr-1"></i> {{ trans('global.contactdetail.fields.phone')}} </strong>
        <p class="text-muted">{{ $contactdetail->phone }}</p>
        
        <hr>
        
        <strong><i class="fa fa-mobile mr-1"></i> {{ trans('global.contactdetail.fields.mobile')}} </strong>
        <p class="text-muted">{{ $contactdetail->mobile }}</p>
        <hr>
        <strong><i class="fa fa-clipboard mr-1"></i> {{ trans('global.contactdetail.fields.notes')}} </strong>
        <p class="text-muted">{!! $contactdetail->notes !!}</p>
        
    </div>
    @if(isset($organization))
    <div class="card-footer">
        <h5>{{ $organization->title }}</h5>
        
        <hr>
        
        <strong><i class="fa fa-map-marker mr-1"></i> {{ trans('global.place') }}</strong>
        <p class="text-muted">
            {{ $organization->street }}<br>
            {{ $organization->postcode }} {{ $organization->city }}<br>
            {{ $organization->state->lang_de }}, {{ $organization->country->lang_de }}
        </p>
        <hr>

        <strong><i class="fa fa-phone mr-1"></i> {{ trans('global.contactdetail.title_singular') }}</strong>
        <p class="text-muted">
            {{ trans('global.organization.fields.phone') }}: {{ $organization->phone }}<br>
            {{ trans('global.organization.fields.email') }}: {{ $organization->email }}
        </p>
    </div>
    @endif   
</div>
