<tr> 
    @if ($terminal_label)
    <th style="width:30%; border-top:0.5pt solid black;">
        <small>{!! strip_tags($reference->referenceable->terminalObjective->title, '<br>') !!}</small>                                     
    </th>
    @else
    <th style="width:30%; border-bottom:0.0pt;"></th>
    @endif
    <th style="width:30%; border-bottom:0.5pt solid black;">
        <small>{!! strip_tags($reference->referenceable->title, '<br>') !!}</small>
    </th>
    <th style="width:40%; border-bottom:0.5pt solid black;">
        <small>{!!  strip_tags($reference->reference->description, '<br>') !!}</small>
    </th>
</tr>