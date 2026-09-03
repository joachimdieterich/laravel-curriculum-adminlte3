@extends('layouts.master')
@section('title')
    <div>
        <h4>Tool: {{  isset($exam) ? $exam->tool : 'tool' }}</h4>
    </div>
@endsection
@section('content')
    <exam :exam="{{ $exam }}"></exam>
@endsection
@section('scripts')
    @parent
    <script>
        function getDatatablesIds(selector) {
            if (selector === '#exam-users-datatable') {
                let users = $(selector).DataTable().rows({selected: true}).data().toArray();
                var newArray = [];

                users.forEach(function (user) {
                    if (user.pivot.exam_started) {
                        alert('{{ trans('global.exam.error_messages.remove_users') }}')
                        throw new Error('Students selected cannot be removed');
                    }
                    newArray.push(user.id)
                })
                return newArray
            }

            return $(selector).DataTable().rows({selected: true}).ids().toArray();
        }

        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let exam_users_table = $('#exam-users-datatable').DataTable({
                ajax: "{{ url('exams/' . $exam->exam_id . '/list') }}",
                bStateSave: true,
                fnStateSave: function (oSettings, oData) {
                    localStorage.setItem('DataTables', JSON.stringify(oData));
                },
                fnStateLoad: function (oSettings) {
                    return JSON.parse(localStorage.getItem('DataTables'));
                },
                buttons: dtButtons
            });

            $(".selectAll-students").on("click", function (e) {
                let selectAllUsers = $('.tr_selectAll_students:first')
                selectAll(exam_users_table, selectAllUsers)
            });

            let users_table = $('#users-datatable').DataTable({
                ajax: "{{ url('exams/' . $exam->exam_id . '/users/list') }}",
                columns: [
                    {data: 'check'},
                    {data: 'username'},
                    {data: 'firstname'},
                    {data: 'lastname'},
                    {data: 'email'}
                ],
                bStateSave: true,
                fnStateSave: function (oSettings, oData) {
                    localStorage.setItem('DataTables', JSON.stringify(oData));
                },
                fnStateLoad: function (oSettings) {
                    return JSON.parse(localStorage.getItem('DataTables'));
                },
                buttons: dtButtons
            });

            $(".selectAll-users").on("click", function (e) {
                let selectAllUsers = $('.tr_selectAll_users:first')
                selectAll(users_table, selectAllUsers)
            });

        });

        function selectAll(users_table, selectAllUsers) {
            if (!selectAllUsers.hasClass('selected')) {
                users_table.rows().select();
                selectAllUsers.addClass('selected')
            } else {
                users_table.rows().deselect();
                selectAllUsers.removeClass('selected')
            }
        }
    </script>
@endsection