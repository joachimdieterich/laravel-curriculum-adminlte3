@extends('layouts.master')
@section('title')
    <div>
        <h4>Tool: {{  isset($exam) ? $exam->tool : 'tool' }}</h4>
        <h1 style="text-align: center;">{{  isset($exam) ? $exam->test_name : 'exam' }}</h1>
    </div>
    <div>{{ trans('global.exam.add_remove_users.students_exam_title') }}</div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="/"><i class="fa fa-home"></i></a></li>
    <li class="breadcrumb-item active">{{ trans('global.user.title') }}</li>
    <li class="breadcrumb-item "><a href="{{ env('DOCUMENTATION', '/documentation') }}" class="text-black-50"><i
                class="fas fa-question-circle"></i></a></li>
@endsection
@section('content')
    <table id="exam-users-datatable"
           class="table table-hover datatable">
        <thead>
        <tr class="tr_selectAll_students">
            <th class="selectAll-students" width="10"></th>
            <th>{{ trans('global.user.fields.firstname') }}</th>
            <th>{{ trans('global.user.fields.lastname') }}</th>
            <th>{{ trans('global.exam.fields.status') }}</th>
            <th>{{ trans('global.exam.fields.completed_at') }}</th>
        </tr>
        </thead>
    </table>
    <div class="row ">
        <div class="col-sm-12">
            <div class="btn-group pull-right" role="group" aria-label="...">
                @include ('forms.input.button', ["onclick" => "expelFromExam()", "field" => "expelFromExam", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-minus", "label" => "Aus Testung ausschreiben"])
            </div>
        </div><!-- ./col-xs-12 -->
    </div>

    <h3> {{ trans('global.exam.add_remove_users.users_group_title') }} </h3>
    <table id="users-datatable"
           class="table table-hover datatable">
        <thead>
        <tr class="tr_selectAll_users">
            <th class="selectAll-users" width="10"></th>
            <th>{{ trans('global.user.fields.username') }}</th>
            <th>{{ trans('global.user.fields.firstname') }}</th>
            <th>{{ trans('global.user.fields.lastname') }}</th>
            <th>{{ trans('global.user.fields.email') }}</th>
        </tr>
        </thead>
    </table>
    <div class="row ">
        <div class="col-sm-12">
            <div class="btn-group pull-right" role="group" aria-label="...">
                @include ('forms.input.button', ["onclick" => "enroleIntoExam()", "field" => "enroleIntoExam", "type" => "button", "class" => "btn btn-default pull-right mt-3", "icon" => "fa fa-plus", "label" => "In Testung einschreiben"])
            </div>
        </div><!-- ./col-xs-12 -->
    </div>
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

        function sendRequest(method, url, ids, data) {
            if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
                return
            }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                    headers: {'x-csrf-token': _token},
                    method: method,
                    url: url,
                    data: data
                })
                    .done(function () {
                        location.reload()
                    })
            }
        }

        function enroleIntoExam() {
            var ids = getDatatablesIds('#users-datatable');
            sendRequest('POST', '/exam/' + {{ $exam->exam_id }} + '/users/enrol', ids, {
                'tool': '{{ $exam->tool }}',
                "enrollment_list": ids,
                _method: 'POST'
            });
        }

        function expelFromExam() {
            var ids = getDatatablesIds('#exam-users-datatable');
            sendRequest('POST', '/exam/' + {{ $exam->exam_id }} + '/users/expel', ids, {
                'tool': '{{ $exam->tool }}',
                'expel_list': ids,
                _method: 'DELETE'
            });
        }

        $(function () {

            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let exam_users_table = $('#exam-users-datatable').DataTable({
                ajax: "{{ url('exam/' . $exam->exam_id . '/list') }}",
                columns: [
                    {data: 'check'},
                    {data: 'firstname'},
                    {data: 'lastname'},
                    {
                        data: 'pivot.exam_started',
                        'render': function (data, type, row) {
                            if (type === 'display') {
                                return row.pivot.exam_started && row.pivot.exam_completed_at ?
                                    '<i class="fa-solid fa-circle" style="color: limegreen"></i>' :
                                    row.pivot.exam_started ?
                                        '<i class="fa-solid fa-circle" style="color: orange"></i>' :
                                        '<i class="fa-solid fa-circle" style="color: red"></i>'
                            }
                            return data
                        }
                    },
                    {data: 'pivot.exam_completed_at',
                        'render': function (data, type, row) {
                            if (type === 'display') {
                                if(row.pivot.exam_completed_at) {
                                    var myDate = new Date(row.pivot.exam_completed_at)
                                    return myDate.toLocaleDateString('de');
                                }
                            }
                            return data
                        }},
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

            $(".selectAll-students").on("click", function (e) {
                let selectAllUsers = $('.tr_selectAll_students:first')
                selectAll(exam_users_table, selectAllUsers)
            });

            let users_table = $('#users-datatable').DataTable({
                ajax: "{{ url('exam/' . $exam->exam_id . '/users/list') }}",
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





