<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 >{{ $message->thread->subject }}</h5>
        <h6>{{ trans('global.message.from') }}: {{ $message->user->username }}
                <span class="mailbox-read-time float-right">{{ $message->created_at->diffForHumans() }}</span></h6>
       
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <!-- /.mailbox-read-info -->
        <div class="mailbox-controls with-border">
            <div class="btn-group">
                
                <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Reply">
                    <i class="fas fa-reply"></i></button>
                <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="Forward">
                    <i class="fas fa-share"></i></button>
            </div>
            <!-- /.btn-group -->
            <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" title="Print">
                <i class="fas fa-print"></i></button>
            <button type="button" class="btn btn-default btn-sm pull-right" data-toggle="tooltip" data-container="body" title="Delete">
                <i class="far fa-trash-alt"></i></button>
        </div>
        <!-- /.mailbox-controls -->
        <div class="mailbox-read-message">
            {{ $message->body }}
        </div>
        <!-- /.mailbox-read-message -->
    </div>
    <!-- /.card-body -->
<!--    <div class="card-footer bg-white">

    </div>-->
    <!-- /.card-footer -->
<!--    <div class="card-footer">
        <div class="float-right">
            <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button>
            <button type="button" class="btn btn-default"><i class="fas fa-share"></i> Forward</button>
        </div>
        <button type="button" class="btn btn-default"><i class="far fa-trash-alt"></i> Delete</button>
        <button type="button" class="btn btn-default"><i class="fas fa-print"></i> Print</button>
    </div>-->
    <!-- /.card-footer -->
</div>
<!-- /.card -->