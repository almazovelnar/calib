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
            "order": [[1, "desc"]],
            "columns": [
                {data: "name"},
                {data: "article_count"},
                {data: "hits"},
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
                    <li class="breadcrumb-item active" aria-current="page">Статистика</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Статистика</h1></div>
                <div class="pd-y-10">
                    <div class="d-flex">
                        <form action="#" >

                            <label class="mb-0">
                                From
                                <input value="{{ Request::has('from') ? \Carbon\Carbon::parse(Request::get('from'))->format('Y-m-d') :'' }}" required type="date" name="from" class="form-control">
                            </label>
                            <label class="mb-0">
                                To
                                <input value="{{ Request::has('to') ? \Carbon\Carbon::parse(Request::get('to'))->format('Y-m-d') :'' }}" required type="date" name="to" class="form-control">
                            </label>
                            <button type="submit"  class="btn btn-outline-secondary">Filter</button>
                        </form>
                    </div>
                </div>
            </div>

            <hr class="mg-y-20">



            @include('errors')

            <div>
                <table id="dt" class="table">
                    <thead>
                    <tr>
                        <th class="wd-60p">Имя</th>
                        <th class="wd-20p">Количество статей</th>
                        <th class="wd-20p">Количество просмотров</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
