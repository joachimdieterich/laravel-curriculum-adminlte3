<div class="{{ $item->css_class }} px-0 my-1">
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title" data-target="#card_{{ $item->id }}" data-card-widget="collapse">{{ $item->title }}</h3>
            <div class="card-tools pull-right">
<!--                <a href="{{route('navigatorItems.edit', ['navigatorItem' => $item->id, 'navigator_id'])}}"
                        class="btn btn-box-tool">
                    <i class="fa fa-edit"></i>
                </a>-->
                @can('navigator_create')
                <form class="float-right" action="{{route('navigatorItems.destroy', ['navigatorItem' => $item->id])}}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      onclick="event.stopPropagation();">
                    @csrf
                    @method('DELETE')   
                    <button 
                        id="delete-navigator-content-{{ $item->id }}"
                        type="submit" class="btn btn-tool">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
                @endcan
                <a class="btn btn-tool" href="{{ route("print.content",  $item->referenceable_id) }}" target="_blank">
                    <i class="fa fa-print"></i>
                </a>
                <button 
                    id="navigator-item-content-{{ $item->id }}"
                    class="btn btn-tool" 
                    data-target="#card_{{ $item->id }}" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
                
                
            </div>
        </div><!-- /.box-header -->
        <div id="card_{{ $item->id }}" class="card-body collapse">
            {!!html_entity_decode($item->referenceable->content)!!}
        </div>
            
    </div>
</div>