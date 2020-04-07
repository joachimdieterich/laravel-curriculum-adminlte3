<div class="col-md-3">
    @if (!request()->is('messages/create') )
        <a href="{{ route('messages.create') }}" class="btn btn-primary btn-block mb-3">{{ trans('global.message.create') }}</a>
    @endif
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ trans('global.message.folder') }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item active">
                    <a href="{{ route('messages') }}"  
                       class="nav-link">
                        <i class="fas fa-inbox"></i> {{ trans('global.message.inbox') }}
                        @if ($inbox > 0) 
                        <span class="badge bg-primary float-right">{{$inbox}}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-envelope"></i> {{ trans('global.message.sendbox') }}
                    </a>
                </li>
                <!--                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="far fa-file-alt"></i> {{ trans('global.message.create') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="fas fa-filter"></i> {{ trans('global.message.junk') }}
                                            <span class="badge bg-warning float-right">65</span>
                                        </a>
                                    </li>-->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-trash-alt"></i> {{ trans('global.message.trash') }}
                    </a>
                </li>
            </ul>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    <!--        <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Labels</h3>
    
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                 /.card-header 
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="far fa-circle text-danger"></i> Important</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="far fa-circle text-warning"></i> Promotions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="far fa-circle text-primary"></i> Social</a>
                        </li>
                    </ul>
                </div>
                 /.card-body 
            </div>-->
    <!-- /.card -->
</div>