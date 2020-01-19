<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="{{ public_path('css/app.css') }}" rel="stylesheet" />
        <link href="{{ public_path('css/custom.css') }}" rel="stylesheet" />
    </head>

    <body >
        <h1>{{ $curriculum->title }}</h1>
        <?php $p_ter = '' ?>
        {{-- ****************** Terminal Objective ****************** --}}            
        @foreach($curriculum->terminalObjectives as $terminalObjective)
            
            @foreach ($curriculum->terminalObjectives
                ->where('id', $terminalObjective->id)
                ->pluck('referenceSubscriptions.*.siblings.*.referenceable.curriculum')
                ->flatten()
                ->values()
                ->where('id', '!=', $curriculum->id)
                ->unique() as $cur)
                <h4>{{ $cur->title }}</h4>
                
                <div style="margin-top: 10px;padding:2px;border:1px solid {{ $terminalObjective->color }};background:{{ $terminalObjective->color }}; color:#fff;">
                    <small>{{ strip_tags($terminalObjective->title) }}</small>
                </div>     
                <?php $p_ter = $terminalObjective->title ?>
                <table  frame="box" style="width:100%; overflow: wrap; vertical-align: top; autosize:1;">
                @include('print.partials.references_table_head')
                
            <?php $t = '' ?>      
            @foreach($curriculum->terminalObjectives
                ->where('id', $terminalObjective->id)
                ->pluck('referenceSubscriptions.*.siblings')
                ->flatten()
                ->values()
                ->where('referenceable.curriculum_id', $cur->id) as $reference)
                @switch($reference->referenceable_type)
                    @case('App\\TerminalObjective') 
                        @include('print.partials.references_terminal_row', ['reference' => $reference])
                    @break
                    @case('App\\EnablingObjective')
                        @include('print.partials.references_enabling_row', ['reference' => $reference, 'terminal_label' => ($t != $reference->referenceable->terminalObjective->title) ])
                        @if ($t != $reference->referenceable->terminalObjective->title)
                           <?php $t = $reference->referenceable->terminalObjective->title ?>
                        @endif
                    @break
                @endswitch
             @endforeach
        @endforeach
        {{-- ****************** Enabling Objective ****************** --}} 
        <?php $e = '' ?>     
        @foreach($terminalObjective->enablingObjectives as $enablingObjective)
            @foreach($terminalObjective->enablingObjectives
                ->where('id', $enablingObjective->id)
                ->pluck('referenceSubscriptions.*.siblings')
                ->flatten()
                ->values()
                    ->where('referenceable.curriculum_id', $cur->id) as $objective_reference)
                @if ($e !=  $enablingObjective->title)   
                </table>  

                @if ($p_ter != $terminalObjective->title)
                    <div style="margin-top: 10px;padding:2px;border:1px solid {{ $terminalObjective->color }};background:{{ $terminalObjective->color }}; color:#fff;">
                        <small>{{ strip_tags($terminalObjective->title) }}</small>
                    </div>   
                <?php $p_ter = $terminalObjective->title ?>
                @endif

                <div style="margin-top: 10px;padding:2px;border:1px solid {{ $terminalObjective->color }};">
                    <small>{{ strip_tags($enablingObjective->title) }}</small>
                </div>
                <table  frame="box" style="width:100%; overflow: wrap; vertical-align: top; autosize:1;">
                @include('print.partials.references_table_head')
                @endif  
           
                @switch($objective_reference->referenceable_type)
                @case('App\\TerminalObjective') 
                    @include('print.partials.references_terminal_row', ['reference' => $objective_reference])
                @break
                @case('App\\EnablingObjective')
                    @include('print.partials.references_enabling_row', ['reference' => $reference, 'terminal_label' => ($e !=  $enablingObjective->title) ])
                @break
                @endswitch
            {{-- set $e --}}
            <?php $e = $enablingObjective->title; ?> 
            @endforeach  
        </table>
    @endforeach
@endforeach
</body>

</html>