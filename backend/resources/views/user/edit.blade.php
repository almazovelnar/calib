@extends('layout')

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Редактировать профиль</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary" value="Сохранить">
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('user.update') }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf
                @method('PUT')

                <div class="row row-sm mg-b-10">
                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="name" class="d-block">Имя</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name')??auth()->user()->name }}" name="name" id="Имя" placeholder="Name of the category" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="position" class="d-block">Позиция</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" value="{{ old('position')??auth()->user()->position }}" name="position" id="position" placeholder="Позиция" required>
                        @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">
                        <label for="old_password" class="d-block">Пароль</label>
                        <input type="password" class="form-control @error('old_password') is-invalid @enderror" name="old_password" id="old_password" placeholder="Пароль" >
                        @error('old_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">
                        <label for="password" class="d-block">Новая Пароль</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"  name="password" id="password" placeholder="Новая Пароль" >
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group col-sm-4 mg-t-10 mg-sm-t-0">
                        <label for="password_confirmation" class="d-block">Подтверждение пароля</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" placeholder="Подтверждение пароля" >
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
