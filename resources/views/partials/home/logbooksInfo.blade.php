<ul class="products-list product-list-in-card pl-2 pr-2"
    role="list">
    @foreach((auth()->user()->role()->id == 1) ? App\Logbook::with('subscriptions')->get() : App\Logbook::with('subscriptions')
            ->whereHas('subscriptions', function($query)  {
                 $query->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id'))
                    ->orWhere('subscribable_type', "App\Course")->whereIn('subscribable_id', auth()->user()->currentGroupEnrolments->pluck('course_id'));
            })
            ->orWhere('owner_id',  auth()->user()->id )
            ->get() as $logbook)
        <li class="item"
            role="listitem">
            <div>
                <a href="/logbooks/{{$logbook->id}}">
                <span class="product-title">
                    {{$logbook->title}}
                </span>
                    <span class="product-description">
<!--                    {!! $logbook->description !!}-->
                </span>
                </a>
            </div>
    </li>
@endforeach
</ul>
