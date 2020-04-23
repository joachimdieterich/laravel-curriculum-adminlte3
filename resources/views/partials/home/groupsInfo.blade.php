<ul class="products-list product-list-in-card pl-2 pr-2">
    @foreach(auth()->user()->groups as $groups)
<li class="item">
    <div >
        <a href="/groups/{{$groups->id}}" class="product-title">{{$groups->title}}
        <span class="product-description">
            {{$groups->grade->title}}
        </span>
        </a>
    </div>
</li>
<!-- /.item -->
@endforeach
</ul>
