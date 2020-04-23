<ul class="products-list product-list-in-card pl-2 pr-2">
    @foreach(auth()->user()->organizations as $organization)
    <li class="item">
        <div >
            <a href="/organizations/{{$organization->id}}" 
               class="product-title">
                {{$organization->title}}
            </a>
            <span class="product-description">
                {!! $organization->description !!}
            </span>
        </div>
    </li>
    <!-- /.item -->
    @endforeach
</ul>