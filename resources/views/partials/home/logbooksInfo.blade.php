<ul
    class="products-list product-list-in-card pl-2 pr-2"
    role="list"
>
    @foreach ((new \App\Http\Controllers\LogbookController)->getLogbooks(true)->get() as $logbook)
        <li
            class="item"
            role="listitem"
        >
            <div>
                <a href="/logbooks/{{$logbook->id}}">
                <span class="product-title">{{$logbook->title}}</span>
                </a>
            </div>
        </li>
    @endforeach
</ul>