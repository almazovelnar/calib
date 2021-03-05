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
            pageLength: 20,
            paging: false,
            "bFilter": false,
            "info":     false,
            "order": [[ 5, "desc" ]],
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
                    <li class="breadcrumb-item active" aria-current="page">{{ Request::query('published') ==='true' ? 'Опубликованные' : (Request::query('deleted') ==='true' ? 'Удаленные' :  'Неопубликованных ') }} новости</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Список {{ Request::query('published') ==='true' ? 'Опубликованные' : (Request::query('deleted') ==='true' ? 'удаленных' :  'Неопубликованных ') }} новостей</h1></div>
                <div class="pd-y-10">
                    @if(Request::query('published') !== 'true' && Request::query('deleted') !== 'true')
                        <a href="{{ route('news.create') }}" class="btn btn-outline-secondary">Создайте</a>
                    @endif
                </div>
            </div>

            <form action="#">
                <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                    <label for="name" class="d-block">Название</label>
                    <input type="text" maxlength="108" class="form-control " value="{{ old('name') }}" name="name" id="name" placeholder="Название новость" required>
                </div>

                @if(Request::has('published'))
                <input type="hidden" name="published" value="{{ Request::get('published') }}">
                @endif

                @if(Request::has('deleted'))
                    <input type="hidden" name="deleted" value="{{ Request::get('deleted') }}">
                @endif

                <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                    <button class="btn btn-outline-secondary">
                        Поиск
                    </button>
                </div>
            </form>

            @include('errors')

            <div>
                <table id="dt" class="table">
                    <thead>
                    <tr>
                        <th class="wd-10p">ID</th>
                        <th class="wd-15p">Название</th>
                        <th class="wd-15p">Идентификатор</th>
                        <th class="wd-10p">Категория</th>
                        <th class="wd-10p">Автор</th>
                        <th class="wd-10p">Дата новости</th>
                        <th class="wd-10p">Количество просмотров</th>
                        @if(Request::query('published') === 'true')
                            <th class="wd-10p">Было опубликовано?</th>
                        @endif
                        <th class="wd-10p">Отредактирован?</th>
                        <th class="wd-10p">Действия</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($newsItems as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->slug }}</td>
                            <td>{{ $row->category_name ?? '-' }}</td>
                            <td>{{ $row->getRelation('user')->name }}</td>
                            <td>{{ $row->published }}</td>
                            <td>{{ $row->view }}</td>
                                @if(Request::query('published') === 'true')
                                <td>
                                @if($row->show)
                                        @if($row->published > now()->format('Y-m-d H:i:s'))
                                            <span class="badge badge-warning">Позже</span>
                                        @else
                                            <span class="badge badge-success">Да</span>
                                        @endif
                                    @else
                                        <span class="badge badge-danger">Нет</span>
                                    @endif
                                </td>
                            @endif
                            <td>
                                @if($row->ready)
                                    <span class="badge badge-success">Отредактирован</span>
                                @else
                                    <span class="badge badge-danger">Не отредактирован</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="/news/{{ $row->id }}/edit" class="btn btn-xs btn-primary btn-icon" data-toggle="tooltip" data-placement="top" title="Редактировать новость">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                                            <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path>
                                            <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                                        </svg>
                                    </a>
                                    @if(Request::query('deleted') !=='true')
                                        <button type="submit" form='delete-{{ $row->id }}' class="btn btn-xs btn-danger  btn-icon" data-toggle="tooltip" data-placement="top" title="Удалить новость">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 class="feather feather-trash-2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    @endif
                                    {{--                                    @if(Request::query('deleted') ==='true')--}}
                                    {{--                                        <button type="submit" form='restore-{{ $row->id }}' class="btn btn-xs btn-info  btn-icon" data-toggle="tooltip" data-placement="top" title="Восстановить новость">--}}
                                    {{--                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-rotate-ccw"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>--}}
                                    {{--                                        </button>--}}
                                    {{--                                    @endif--}}
                                </div>
                                <form onsubmit="return confirm('Вы уверены, что хотите удалить данную новость?');" action="/news/{{ $row->id }}" id='delete-{{ $row->id }}' method='POST'>@csrf @method('DELETE')</form>

                                @if(Request::query('deleted') ==='true')
                                    <form onsubmit="return confirm('Вы уверены, что хотите восстановить данную новость?');" action="/news/{{ $row->id }}/restore" id='restore-{{ $row->id }}' method='POST'>@csrf @method('PATCH')</form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $news->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@endsection
