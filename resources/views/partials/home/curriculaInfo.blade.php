<ul class="products-list product-list-in-card pl-2 pr-2"
    role="list">
    @foreach(auth()->user()->currentCurriculaEnrolments() as $curriculum)
        <li class="item"
            role="listitem">
            <div>
                <a href="/courses/{{$curriculum->course_id}}"
                >
                <span class="product-title">
                    {{$curriculum->title}}
                </span>
                </a>
            </div>
        </li>
    @endforeach
</ul>
