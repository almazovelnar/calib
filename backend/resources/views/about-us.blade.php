@extends('layout')

@push('js')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
@endpush

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
                    <li class="breadcrumb-item " aria-current="page">Настройки</li>
                    <li class="breadcrumb-item active" aria-current="page">О нас</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">О нас</h1></div>
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
                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="long" class="d-block">Longitude</label>
                        <input type="text" class="form-control @error('long') is-invalid @enderror" value="{{ old('long')??$settings->where('key','long')->first()->value }}" name="long" id="long" placeholder="" required>
                        @error('long')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="lat" class="d-block">Lattitude</label>
                        <input type="text" class="form-control @error('lat') is-invalid @enderror" value="{{ old('lat')??$settings->where('key','lat')->first()->value }}" name="lat" id="lat" placeholder="" required>
                        @error('lat')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <div class=" form-group">
                            <label for="editor">
                                Текст новости: <span class="tx-danger">*</span></label>
                            <textarea id="editor" name="about_us_text">{!! old('content')??$settings->where('key','about_us_text')->first()->value !!}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
