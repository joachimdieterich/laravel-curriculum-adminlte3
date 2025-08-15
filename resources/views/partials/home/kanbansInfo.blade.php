<ul class="products-list product-list-in-card pl-2 pr-2"
    role="list">
    @foreach((new \App\Http\Controllers\KanbanController)->userKanbans() as $kanban)
        <li class="item"
            role="listitem">
            <div>
                <a href="/kanbans/{{$kanban->id}}">
                <span class="product-title">
                  <i class="fa fa-circle"
                     style="color: {{$kanban->color}} "
                     ></i>
                    {{$kanban->title}}
                </span>
                    <span class="product-description">
<!--                    {!! $kanban->description !!}-->
                </span>
                </a>
            </div>
    </li>
@endforeach
</ul>
