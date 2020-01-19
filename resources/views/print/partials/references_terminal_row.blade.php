<tr>
    <th style="width:30%; border-top:0.5pt solid black;">
        <small>{!! strip_tags($reference->referenceable->title, '<br>') !!}</small>
    </th>
    <th style="width:30%; border-bottom:0.5pt solid black;"></th>
    <th style="width:40%; border-bottom:0.5pt solid black;">
        <small>{!! strip_tags($reference->reference->description, '<br>') !!}</small>
    </th>
</tr>