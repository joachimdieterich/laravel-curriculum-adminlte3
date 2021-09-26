<ul class="products-list product-list-in-card pl-2 pr-2">
    @foreach(auth()->user()->organizations as $organization)
    <li class="item">
        <div >
            <a href="/organizations/{{$organization->id}}">
                <span class="product-title ">
                    {{$organization->title}}
                </span>
                <span class="product-description text-muted">
                    {!! $organization->description !!}
                </span>
            </a>
        </div>
    </li>
    <!-- /.item -->
    @endforeach
</ul>
