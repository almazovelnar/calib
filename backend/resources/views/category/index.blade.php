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
            "order": [[ 0, "desc" ]],
            "columns": [
                {data: "id"},
                {data: "order"},
                {data: "name"},
                {data: "slug"},
                {data: "title"},
                {
                    data: "show",
                    render: (data) => {
                        return data ? '<span class="badge badge-success">Да</span>' : '<span class="badge badge-danger">Нет</span>'
                    }
                },
                {
                    data: {},
                    render: (data) => {
                        return `<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="/category/` + data.id + `/edit" class="btn btn-xs btn-primary btn-icon" data-toggle="tooltip" data-placement="top" title="Редактировать категорию">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon></svg>
                                    </a>
                                    <button type="submit" form='delete-` + data.id + `' class="btn btn-xs btn-danger  btn-icon" data-toggle="tooltip" data-placement="top" title="Удалить категорию">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                    </button>
                                </div>
                                <form action="/category/` + data.id + `" onsubmit="return confirm('Вы уверены, что хотите удалить данную категорию?');" id='delete-` + data.id + `' method='POST'>@csrf @method('DELETE')</form>`
                    }
                },
            ],
            language: {
                "paginate": {
                    "previous": "Предыдущий",
                    "next": "Следующий"
                },
                searchPlaceholder: 'Поиск...',
                sSearch: '',
                lengthMenu: '_MENU_  количество на странице',
                "infoEmpty":      'Отображено с 0 до 0 записи (всего 0 записей)',
                sInfo: 'Отображено с _START_ до _END_ записи (всего _TOTAL_ записей)',
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
                    <li class="breadcrumb-item active" aria-current="page">Категории</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Список категорий</h1></div>
                <div class="pd-y-10">
                    <a href="{{ route('category.create') }}" class="btn btn-outline-secondary">Создайте</a>
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <div>
                <table id="dt" class="table">
                    <thead>
                    <tr>
                        <th class="wd-10p">ID</th>
                        <th class="wd-10p">Порядок</th>
                        <th class="wd-25p">Название</th>
                        <th class="wd-10p">Идентификатор</th>
                        <th class="wd-30p">SEO Название</th>
                        <th class="wd-15p">Показывать на главной</th>
                        <th class="wd-10p">Действия</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
