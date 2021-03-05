@extends('layout')

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Настройки</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Изменение “Горячая Новость”</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary" value="Сохранить">
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('settings') }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf
                @method('PUT')

                <div class="row row-sm mg-b-10">
{{--                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="name" class="d-block">USD</label>--}}
{{--                        <input type="text" class="form-control @error('usd') is-invalid @enderror" value="{{ old('usd')??$settings->where('key','usd')->first()->value }}" name="usd" id="usd" placeholder="" required>--}}
{{--                        @error('usd')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}


{{--                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="euro" class="d-block">EURO</label>--}}
{{--                        <input type="text" class="form-control @error('euro') is-invalid @enderror" value="{{ old('euro')??$settings->where('key','euro')->first()->value }}" name="euro" id="euro" placeholder="" required>--}}
{{--                        @error('euro')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

{{--                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="oil" class="d-block">Нефть</label>--}}
{{--                        <input type="text" class="form-control @error('oil') is-invalid @enderror" value="{{ old('oil')??$settings->where('key','oil')->first()->value }}" name="oil" id="oil" placeholder="" required>--}}
{{--                        @error('oil')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

{{--                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="covid_total" class="d-block"> COVID Всего</label>--}}
{{--                        <input type="text" class="form-control @error('covid_total') is-invalid @enderror" value="{{ old('covid_total')??$settings->where('key','covid_total')->first()->value }}" name="covid_total" id="covid_total" placeholder="" required>--}}
{{--                        @error('covid_total')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

{{--                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="covid_baku" class="d-block">COVID Баку</label>--}}
{{--                        <input type="text" class="form-control @error('covid_baku') is-invalid @enderror" value="{{ old('covid_baku')??$settings->where('key','covid_baku')->first()->value }}" name="covid_baku" id="covid_baku" placeholder="" required>--}}
{{--                        @error('covid_baku')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

{{--                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="weather_baku" class="d-block"> Погода Баку</label>--}}
{{--                        <input type="text" class="form-control @error('weather_baku') is-invalid @enderror" value="{{ old('weather_baku')??$settings->where('key','weather_baku')->first()->value }}" name="weather_baku" id="weather_baku" placeholder="" required>--}}
{{--                        @error('weather_baku')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

{{--                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="weather_ganca" class="d-block"> Погода Гянджа</label>--}}
{{--                        <input type="text" class="form-control @error('weather_ganca') is-invalid @enderror" value="{{ old('weather_ganca')??$settings->where('key','weather_ganca')->first()->value }}" name="weather_ganca" id="weather_ganca" placeholder="" required>--}}
{{--                        @error('weather_ganca')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <label for="red_title" class="d-block">Важный (Красный) Текст</label>
                        <input type="text" class="form-control @error('red_title') is-invalid @enderror" value="{{ old('red_title')??$settings->where('key','red_title')->first()->value }}" name="red_title" id="red_title" placeholder="" >
                        @error('red_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

{{--                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="red_tag" class="d-block">Важный (Красный) Заголовок</label>--}}
{{--                        <input type="text" class="form-control @error('red_tag') is-invalid @enderror" value="{{ old('red_tag')??$settings->where('key','red_tag')->first()->value }}" name="red_tag" id="red_tag" placeholder="" >--}}
{{--                        @error('red_tag')--}}
{{--                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                        @enderror--}}
{{--                    </div>--}}

                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <label for="red_date" class="d-block"> Важный (Красный) Дата</label>
                        <input type="datetime-local" class="form-control @error('red_date') is-invalid @enderror" value="{{ now()->format('Y-m-d\TH:i') }}" name="red_date" id="red_date" placeholder="" >
                        @error('red_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <td>Дата сохранения</td>
                            <td>Название новости</td>
                            <td>Дата в новости</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(array_reverse($settings->where('key','red_title')->first()->changes??[]) as $index=>$log)
                            <tr>
                                <td>{{ $log['date'] }}</td>
                                <td>{{ $log['new']??'-' }}</td>
                                <td>{{ array_reverse($settings->where('key','red_date')->first()->changes??[])[$index]['new'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
@endsection
