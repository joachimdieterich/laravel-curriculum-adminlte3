<div class="{{ $item->css_class }} px-0 my-1">
    <div class="card collapsed-card pointer">
        <div class="card-header">
            <h1 class="h3 card-title"
                data-target="#card_{{ $item->id }}"
                data-card-widget="collapse">{{ $item->referenceable->title }}
            </h1>
            <div class="card-tools pull-right">

                @can('navigator_create')

                    <form class="float-right"
                          action="{{route('navigatorItems.destroy', ['navigatorItem' => $item->id])}}"
                          method="POST"
                          enctype="multipart/form-data"
                          onclick="event.stopPropagation();">
                        @csrf
                        @method('DELETE')
                        <button
                            id="delete-navigator-content-{{ $item->id }}"
                            type="submit"
                            class="btn btn-tool"
                            aria-label="{{ trans('global.delte') }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @endcan
                <a class="btn btn-tool" href="{{ route("print.content",  $item->referenceable_id) }}"
                   target="_blank"
                   aria-label="{{ trans('global.print') }}">
                    <i class="fa fa-print"></i>
                </a>
                <button
                    id="navigator-item-content-{{ $item->id }}"
                    class="btn btn-tool"
                    aria-label="{{ trans('global.toggle') }}"
                    data-card-widget="collapse"
                    data-expand-icon="fa-expand-alt"
                    data-collapse-icon="fa-compress-alt">
                    <i class="fas fa-expand-alt"></i>
                </button>
                @can('navigator_create')
                    <a onclick="app.__vue__.$modal.show('content-create-modal',  { 'id': {{ $item->referenceable_id }}, 'method': 'patch' });"
                       class="btn btn-tool"
                       title="{{ trans('global.edit') }}">
                        <i class="fa fa-pencil-alt"></i>
                    </a>
                @endcan
            </div>
        </div><!-- /.box-header -->
        <div id="card_{{ $item->id }}" class="card-body collapse">
            {!!html_entity_decode($item->referenceable->content)!!}
        </div>

    </div>
</div>
