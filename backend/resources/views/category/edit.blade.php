@extends('layout')

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Главная</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Категории</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Обновить категорию</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Обновить категорию: {{ $category->name }}</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary" value="Сохранить">
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('category.update',['category'=>$category->id]) }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf
                @method('PUT')

                <div class="row row-sm mg-b-10">
                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="name" class="d-block">Название</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name')??$category->name }}" name="name" id="name" placeholder="Название категории" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="order" class="d-block">Order</label>
                        <input type="number" step="1" min="1" max="20" class="form-control @error('order') is-invalid @enderror" value="{{ old('order')??$category->order }}" name="order" id="order" placeholder="Order" required>
                        @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="slug" class="d-block">URL</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug')??$category->slug }}" name="slug" id="slug" placeholder="URL для категории" required>
                        @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="title" class="d-block">SEO Название</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title')??$category->title }}" name="title" id="title" placeholder="SEO Название категории" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="description" class="d-block">SEO Описание</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" value="{{ old('description')??$category->description }}" name="description" id="description" placeholder="SEO Описание категории" required>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

{{--                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">--}}
{{--                        <label for="image" class="d-block"> SEO Картинка <a target="_blank" href="{{ $category->image }}"><u>Обзор</u></a></label>--}}
{{--                        <div class="custom-file">--}}
{{--                            <input  type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="image">--}}
{{--                            <label class="custom-file-label" for="image">Файл не выбран</label>--}}
{{--                            @error('image')--}}
{{--                            <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="form-group col-md-12">
                        <fieldset class="form-fieldset">
                            <legend>Настройки</legend>

                            <div class="form-group">
                                <div class="custom-control custom-switch d-inline">
                                    <input {{ (old('show') ?? $category->show) ? 'checked' : ''  }} type="checkbox" name="show" class="custom-control-input @error('show') is-invalid @enderror" id="show">
                                    <label class="custom-control-label" for="show">Опубликовать</label>
                                    @error('show')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
