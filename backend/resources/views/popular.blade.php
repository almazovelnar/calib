@extends('layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('lib/datatables.net-dt/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/datatables.net-responsive-dt/css/responsive.dataTables.min.css') }}">
@endpush

@push('js')
    <script src="{{ asset('lib/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('lib/datatables.net-dt/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ asset('lib/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('lib/datatables.net-responsive-dt/js/responsive.dataTables.min.js') }}"></script>
    <script>
        $('#dt').DataTable({
            'ajax': {
                url: window.location.href,
            },
            "order": [[3, "desc"]],
            "columns": [
                {data: "id"},
                {data: "name"},
                {data: "date"},
                {data: "view"},
            ],
            language: {
                "paginate": {
                    "previous": "Предыдущий",
                    "next": "Следующий"
                },
                searchPlaceholder: 'Поиск...',
                sSearch: '',
                sInfo: 'Отображено с _START_ до _END_ записи (всего _TOTAL_ записей)',
                "infoEmpty":      'Отображено с 0 до 0 записи (всего 0 записей)',
                lengthMenu: '_MENU_  количество на странице',
            }
        });
    </script>
@endpush

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Популярный</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Популярный</h1></div>
                <div class="pd-y-10">
                    <div class="d-flex">
                    </div>
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <div>
                <table id="dt" class="table">
                    <thead>
                    <tr>
                        <th class="wd-10p">ID</th>
                        <th class="wd-50p">Название</th>
                        <th class="wd-20p">Дата новости</th>
                        <th class="wd-20p">Количество просмотров</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
