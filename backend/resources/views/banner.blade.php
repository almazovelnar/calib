@extends('layout')

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Banner</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Изменение “Banner”</h1></div>
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
                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <label for="banner_height" class="d-block">Banner height</label>
                        <input type="text" class="form-control @error('banner_height') is-invalid @enderror" value="{{ old('banner_height')??$settings->where('key','banner_height')->first()->value }}" name="banner_height" id="banner_height" placeholder="" required>
                        @error('banner_height')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <div class=" form-group">
                            <label for="editor">
                                Текст banner: <span class="tx-danger">*</span></label>
                            <textarea id="editor" name="banner_text">{!! old('content')??$settings->where('key','banner_text')->first()->value !!}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
