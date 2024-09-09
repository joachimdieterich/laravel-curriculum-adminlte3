@extends((Auth::user()->id == env('GUEST_USER')) ? 'layouts.contentonly' : 'layouts.master')

@section('title')
    {{ trans('global.logbook.title') }}
@endsection
@section('breadcrumb')
    <breadcrumbs
        :entries="{{json_encode([
            ['active'=> true, 'title'=> trans('global.logbook.title')]
        ])}}"
    ></breadcrumbs>
@endsection
@section('content')
    <logbooks create_label_field="create"></logbooks>
    {{--<medium-create-modal></medium-create-modal>--}}
@endsection
{{--
@section('scripts')
    @parent
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
            var table = $('#logbooks-datatable').DataTable({
                ajax: "{{ url('logbooks/list') }}",
                columns: [
                    { data: 'check'},
                    { data: 'title' },
                    {data: 'action'}
                ],
                columnDefs: [
                    {"visible": false, "targets": 0},
                    {
                        orderable: false,
                        searchable: false,
                        targets: -1
                    }
                ],
                select: false,
                bStateSave: true,
                fnStateSave: function (oSettings, oData) {
                    localStorage.setItem('DataTables', JSON.stringify(oData));
                },
                fnStateLoad: function (oSettings) {
                    return JSON.parse(localStorage.getItem('DataTables'));
                },
                buttons: dtButtons
            });
            table.on('click', 'tr', function () {
                window.location.href = "/logbooks/" + table.row(this).id()
            });
        })

    </script>
@endsection--}}
