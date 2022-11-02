<style>
    th {
        border-bottom: 3px solid;
        font-size: large;
    }
    td {
        text-align: center;
        border-bottom: 1px solid;
    }
</style>
<table>
    <tr>
        <th>title</th>
        <th width="40%">description</th>
        <th>status</th>
        <th>created at</th>
        <th>owner</th>
    </tr>
    @foreach($kanban->statuses as $status)
        @foreach($status->items as $k)
            <tr>
                <td>{{$k->title}}</td>
                <td width="40%">{{$k->description}}</td>
                <td>{{$status->title}}</td>
                <td>{{$k->created_at}}</td>
                <td>{{$k->owner->username}}</td>
            </tr>
        @endforeach
    @endforeach
</table>
