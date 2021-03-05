@extends('layout')

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Пользователи</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Новый пользователь</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Новый пользователь</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary" value="Сохранить">
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('user.store') }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf

                <div class="row row-sm mg-b-10">
                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">
                        <label for="name" class="d-block">Имя</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" name="name" id="name" placeholder="Имя" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">
                        <label for="email" class="d-block">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" id="email" placeholder="E-mail address" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">
                        <label for="position" class="d-block">Позиция</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" name="position" id="position" placeholder="Позиция" required>
                        @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="password" class="d-block">Пароль</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" value="" name="password" id="password" placeholder="Пароль" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="password_confirmation" class="d-block">Подтверждение пароля</label>
                        <input type="text" class="form-control @error('password_confirmation') is-invalid @enderror" value="" name="password_confirmation" id="password_confirmation" placeholder="Подтверждение пароля" required>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
