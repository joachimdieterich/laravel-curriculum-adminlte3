<div class="{{ $item->css_class }} px-0 my-1">
    <div class="box bottom-buffer-20">
        <div class="box-header with-border">
            <h3 class="box-title" data-toggle="collapse" data-target="#card_{{ $item->id }}"> {{ $item->title }}</h3>
            <div class="box-tools pull-right">
<!--                <a href="{{route('navigatorItems.edit', ['navigatorItem' => $item->id, 'navigator_id'])}}"
                        class="btn btn-box-tool">
                    <i class="fa fa-edit"></i>
                </a>-->
                <form class="float-right" action="{{route('navigatorItems.destroy', ['navigatorItem' => $item->id])}}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      onclick="event.stopPropagation();">
                    @csrf
                    @method('DELETE')   
                    <button type="submit" class="btn btn-box-tool">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
                <button class="btn btn-box-tool" >
                    <i class="fa fa-print"></i>
                </button>
                <button class="btn btn-box-tool" data-toggle="collapse" data-target="#card_{{ $item->id }}">
                    <i class="fa fa-expand"></i>
                </button>
                
                
            </div>
        </div><!-- /.box-header -->
        <div id="card_{{ $item->id }}" class="box-body collapse">
            {!!html_entity_decode($item->referenceable->content)!!}
            
        </div>
            
    </div>
</div>