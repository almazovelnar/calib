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
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        $('input[name=name]').on('change', function (e) {
            $('input[name=title]').val($(this).val())
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
        //     console.log('past');
        // })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        window.onbeforeunload = function() {
            var url = "{{route('news_unlock',['news' => $news->id])}}";
            $.ajax({ url: url, method: 'post' })
        };

        window.addEventListener('popstate', function(event) {
            var url = "{{route('news_unlock',['news' => $news->id])}}";
            $.ajax({ url: url, method: 'post' })
        }, false);
    </script>
    <script src="{{ asset('lib/dropzone.min.js') }}"></script>
    <script>
        const dropzone = $("div#dropzone").dropzone({
            url: "/news/upload-2",
            maxFilesize: 2,
            addRemoveLinks: true,
            init: function() {
                @foreach($news->gallery()->get() as $file)
                    var mockFile = { name: "{{ $file->filename }}", size: '-', type: 'image/jpeg' };
                    this.options.addedfile.call(this, mockFile);
                    this.options.thumbnail.call(this, mockFile, "{{ $file->url }}");
                    mockFile.previewElement.classList.add('dz-success');
                    mockFile.previewElement.classList.add('dz-complete')
                @endforeach

                this.on("success", function(file, responseText) {
                    $('#main-form').append('<input type=hidden name=gallery[] value='+responseText.fileName+'>')
                });
                this.on('removedfile', function(file) {
                    file.previewElement.remove();

                    let name = file.name

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
                    <li class="breadcrumb-item"><a href="{{ route('category.index') }}">новосты</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Редактировать новость</li>
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Редактировать новость: {{ $news->name }}</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary d-block" value="Сохранить">

                    <form action="{{ route('news.ready',['news'=>$news->id]) }}" style="display: block" class="mt-4" method="POST">
                        @csrf
                        @method('PATCH')

                        <input type="submit" class="btn btn-outline-secondary" value="{{ $news->ready ? 'Не отредактирован' : 'Отредактирован' }}">
                    </form>
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('news.update',['news'=>$news->id]) }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf
                @method('PUT')

                <div class="row row-sm mg-b-10">
                    <div class="form-group col-sm-12 mg-t-10 mg-sm-t-0">

                        <label for="name" class="d-block">Название ( <span id="left-count">{{ mb_strlen(html_entity_decode($news->name)) }}</span>  / 108)</label>
                        <input type="text" maxlength="108" class="form-control @error('name') is-invalid @enderror" value="{{ old('name')??html_entity_decode($news->name) }}" name="name" id="name" placeholder="Название новость" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="category" class="d-block">Категория</label>
                        <select name="category[]" multiple id="category" class="form-control @error('category') is-invalid @enderror">
                            @foreach($categories as $category)
                                <option {{ !in_array($category->id, (old('category')??$news->category->pluck('id')->toArray())) ?: 'selected' }} value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="published_at" class="d-block">Дата публикации</label>
                        <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at')??$news->published_at->format('Y-m-d\TH:i') }}" name="published_at" id="published_at"
                               placeholder="Publish date of news" required>
                        @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="slug" class="d-block">URL</label>
                        <input readonly disabled type="text" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug')??$news->slug }}" name="slug" id="slug" placeholder="URL of the category" required>
                        @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="title" class="d-block">SEO название</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" value="{{ old('title')??$news->title }}" name="title" id="title" placeholder="SEO название" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 mg-t-10 mg-sm-t-0">
                        <label for="description" class="d-block">SEO Описание</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" value="{{ old('description')??$news->description }}" name="description" id="description" placeholder="SEO Описание"
                               required>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-sm-6 ">
                        <label for="image" class="d-block">SEO Картинка <a target="_blank" href="{{ $news->image }}"><u>View current</u></a></label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input @error('image') is-invalid @enderror" id="image">
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

                                <div class="form-group d-flex flex-wrap">
                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input {{ (old('show') ?? $news->show) ? 'checked' : ''  }} type="checkbox" name="show" class="custom-control-input @error('show') is-invalid @enderror" id="show">
                                        <label class="custom-control-label" for="show">Опубликовать</label>
                                        @error('show')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input {{ (old('on_index') ?? $news->on_index) ? 'checked' : ''  }} type="checkbox" name="on_index" class="custom-control-input @error('on_index') is-invalid @enderror" id="on_index">
                                        <label class="custom-control-label" for="on_index">Опубликовать на главной странице</label>
                                        @error('on_index')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input {{ (old('top_left') ?? $news->top_left) ? 'checked' : ''  }} type="checkbox" name="top_left" class="custom-control-input @error('top-left') is-invalid @enderror" id="top-left">
                                        <label class="custom-control-label" for="top-left">Левая Топ Новость</label>
                                        @error('top-left')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-4">
                                        <input {{ (old('top_right') ?? $news->top_right) ? 'checked' : ''  }} type="checkbox" name="top_right" class="custom-control-input @error('top-right') is-invalid @enderror" id="top-right">
                                        <label class="custom-control-label" for="top-right">Правая Топ Новость</label>
                                        @error('top-right')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline">
                                        <input {{ (old('show_img') ?? $news->show_img) ? 'checked' : ''  }} type="checkbox" name="show_img" class="custom-control-input @error('show_img') is-invalid @enderror" id="show_img">
                                        <label class="custom-control-label" for="show_img">SEO Image</label>
                                        @error('show_img')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-switch d-inline mr-3 mt-3">
                                        <input {{ (old('show_user') ?? $news->show_user) ? 'checked' : ''  }}  type="checkbox" name="show_user" class="custom-control-input @error('show_user') is-invalid @enderror" id="show_user">
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
                            <textarea id="editor" name="content">{!! old('content')??$news->content !!}</textarea>
                        </div>
                    </div>

                    <h5>Галерея</h5>
                    <div id="dropzone" class="dropzone form-group col-sm-12" style="border: 2px dashed #0087F7;width: 100%;border-radius: 10px">
                        <div class="dz-message" data-dz-message><span>Перетащите сюда файлы для загрузки</span></div>
                    </div>

                    @if(auth()->user()->hasPermissionFor('news_logs'))
                        <hr>

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <td>#ID</td>
                                <td>User</td>
                                <td>Action at</td>
                                <td>Action</td>
                                <td>IP</td>
                                <td>Old</td>
                                <td>New</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($news->logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>{{ $log->causer->name ?? 'System' }}</td>
                                    <td>{{ $log->action_at }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->ip }}</td>
                                    <td><span data-toggle="tooltip" data-placement="top" title="{{ json_encode($log->properties['old']) }}">View</span></td>
                                    <td><span data-toggle="tooltip" data-placement="top" title="{{ json_encode($log->properties['new']) }}">View</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
