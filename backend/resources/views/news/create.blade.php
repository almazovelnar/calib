@extends('layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('lib/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/basic.min.css') }}">
    <style>
        .dz-message span{
            font-size: 25px;
        }
    </style>
@endpush

@push('js')
    <script>
        $('input[name=name]').on('change', function (e) {
            $('input[name=title]').val($(this).val())
            $('input[name=description]').val($(this).val())
            $('input[name=slug]').val($(this).val())
        })

        $('input[name=name]').on('keyup', function (e) {
            $('span#left-count').text($(this).val().length)
        })

        $('input[name=top_right]').on('change', function (e) {
            $("input[name=top_left]").prop("checked", false);
        })

        $('input[name=top_left]').on('change', function (e) {
            $("input[name=top_right]").prop("checked", false);
        })

        $('input[name=show]').on('change', function (e) {
            if (!$(this).is(':checked')) {
                $("input[name=top_right]").prop("checked", false);
                $("input[name=top_left]").prop("checked", false);
            }
        })

        // $('input[name=published_at]').on('change', function () {
        //     const today = new Date();
        //     const selected = new Date($(this).val())
        //
        //     if (selected > today) {
        //         $("input[name=show]").prop("checked", false);
        //         return;
        //     }
        //
        // })
    </script>
<script src="{{ asset('lib/dropzone.min.js') }}"></script>
    <script>
        $("div#dropzone").dropzone({
            url: "/news/upload-2",
            maxFilesize: 2,
            addRemoveLinks: true,
            init: function() {
                this.on("success", function(file, responseText) {
                    $('#main-form').append('<input type=hidden name=gallery[] value='+responseText.fileName+'>')
                });
                this.on('removedfile', function(file) {
                    file.previewElement.remove();

                    let name = null

                    if(file.xhr) {
                        name = JSON.parse(file.xhr.responseText).fileName
                    }

                    $('#main-form').append('<input type=hidden name=gallery_removed[] value='+name+'>')
                })
                this.on('sending',function(file, xhr, formData) {
                    formData.append("_token", "{{ csrf_token() }}");
                })
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
                    <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Новости</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Новая новость</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Новая новость</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary" value="Сохранить">
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('news.store') }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf

                <div class="row row-sm mg-b-10">
                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <label for="name" class="d-block">Название ( <span id="left-count">0</span>  / 108)</label>
                        <input type="text" maxlength="108" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" name="name" id="name" placeholder="Название новость" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="category" class="d-block">Категория</label>
                        <select required name="category[]" multiple="multiple" id="category" class="form-control @error('category') is-invalid @enderror">
                            @foreach($categories as $category)
                                <option {{ (in_array($category->id, old('category')??[]) || Request::input('category') == $category->slug) ? 'selected' : '' }} value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="published_at" class="d-block">Дата публикации</label>
                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at')??now()->format('Y-m-d\TH:i') }}" name="published_at" id="published_at"
                               placeholder="Publish date of news" required>
                        @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="title" class="d-block">SEO название</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" name="title" id="title" placeholder="SEO Название новость" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="description" class="d-block">SEO Описание категории</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" name="description" id="description" placeholder="SEO Описание новость" required>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 ">
                        <label for="image" class="d-block">SEO Картинка</label>
                        <div class="custom-file">
                            <input required type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="image">
                            <label class="custom-file-label" for="image">Файл не выбран</label>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if(auth()->user()->hasPermissionFor('news_publish'))
                        <div class="form-group col-md-12">
                            <fieldset class="form-fieldset">
                                <legend>Настройки</legend>

                                <div class="form-group d-flex flex-wrap" id="settingsform">
                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input type="checkbox" name="show" class="custom-control-input @error('show') is-invalid @enderror" id="show">
                                        <label class="custom-control-label" for="show">Опубликовать</label>
                                        @error('show')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input type="checkbox" name="on_index" checked class="custom-control-input @error('on_index') is-invalid @enderror" id="on_index">
                                        <label class="custom-control-label" for="on_index">Опубликовать на главной странице</label>
                                        @error('on_index')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input type="checkbox" name="top_left" class="custom-control-input @error('top-left') is-invalid @enderror" id="top-left">
                                        <label class="custom-control-label" for="top-left">Левая Топ Новость</label>
                                        @error('top-left')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input type="checkbox" name="top_right" class="custom-control-input @error('top-right') is-invalid @enderror" id="top-right">
                                        <label class="custom-control-label" for="top-right">Правая Топ Новость</label>
                                        @error('top-right')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input checked type="checkbox" name="show_img" class="custom-control-input @error('show_img') is-invalid @enderror" id="show_img">
                                        <label class="custom-control-label" for="show_img">SEO Картинка</label>
                                        @error('show_img')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-3 mt-3">
                                        <input  type="checkbox" name="show_user" class="custom-control-input @error('show_user') is-invalid @enderror" id="show_user">
                                        <label class="custom-control-label" for="show_user">Показать пользователя</label>
                                        @error('show_user')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    @endif

                    <div class="alert-c w-100" role="alert">
                        <div class="alert alert-danger">
                            <span>
                                Для загрузки изображений c водяным знаком, пожалуйста используйте в названиях файлов префикс cbr. Пример cbr_imagename.jpg
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">
                        <div class=" form-group">
                            <label for="editor">
                                Текст новости: <span class="tx-danger">*</span></label>
                            <textarea id="editor" name="content">{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <h5>Галерея</h5>
                    <div id="dropzone" class="dropzone form-group col-sm-12" style="border: 2px dashed #0087F7;width: 100%;border-radius: 10px">
                        <div class="dz-message" data-dz-message><span>Перетащите сюда файлы для загрузки</span></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
