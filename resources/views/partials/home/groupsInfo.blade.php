<ul class="products-list product-list-in-card pl-2 pr-2"
    role="list">
    @foreach(auth()->user()->groups as $groups)
        <li class="item"
            role="listitem">
            <div>
                <a href="/groups/{{$groups->id}}"
                >
                <span class="product-title">
                    {{$groups->title}}
                </span>
                    <span class="product-description">
                    {{$groups->grade->title}}
                </span>
                </a>
            </div>
        </li>
    @endforeach
</ul>
