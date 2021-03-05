@extends('layout')

@push('js')
    <script>
        $('#user_setter').on('click',function() {
            $("input#category_create").prop("checked", false);
            $("input#category_read").prop("checked", false);
            $("input#category_update").prop("checked", false);
            $("input#category_delete").prop("checked", false);

            $("input#user_create").prop("checked", false);
            $("input#user_read").prop("checked", false);
            $("input#user_update").prop("checked", false);
            $("input#user_delete").prop("checked", false)

            $("input#news_all").prop("checked", false);
            $("input#news_publish").prop("checked", false);
            $("input#news_logs").prop("checked", false);
            $("input#news_create").prop("checked", true);
            $("input#news_read").prop("checked", true);
            $("input#news_update").prop("checked", true);
            $("input#news_delete").prop("checked", false);

            $("input#settings_update").prop("checked", false);
        })

        $('#editor_setter').on('click',function() {
            $("input#category_create").prop("checked", false);
            $("input#category_read").prop("checked", false);
            $("input#category_update").prop("checked", false);
            $("input#category_delete").prop("checked", false);

            $("input#user_create").prop("checked", false);
            $("input#user_read").prop("checked", false);
            $("input#user_update").prop("checked", false);
            $("input#user_delete").prop("checked", false)

            $("input#news_all").prop("checked", false);
            $("input#news_publish").prop("checked", true);
            $("input#news_logs").prop("checked", false);
            $("input#news_create").prop("checked", true);
            $("input#news_read").prop("checked", true);
            $("input#news_update").prop("checked", true);
            $("input#news_delete").prop("checked", false);

            $("input#settings_update").prop("checked", true);

            $("input#notification_delete").prop("checked", true);
        })

        $('#director_setter').on('click',function() {
            $("input#category_create").prop("checked", true);
            $("input#category_read").prop("checked", true);
            $("input#category_update").prop("checked", true);
            $("input#category_delete").prop("checked", true);

            $("input#user_create").prop("checked", true);
            $("input#user_read").prop("checked", true);
            $("input#user_update").prop("checked", true);
            $("input#user_delete").prop("checked", true)

            $("input#news_all").prop("checked", true);
            $("input#news_publish").prop("checked", true);
            $("input#news_logs").prop("checked", true);
            $("input#news_create").prop("checked", true);
            $("input#news_read").prop("checked", true);
            $("input#news_update").prop("checked", true);
            $("input#news_delete").prop("checked", true);
            $("input#news_deleted").prop("checked", true);

            $("input#settings_update").prop("checked", true);

            $("input#notification_delete").prop("checked", true);
        })
    </script>
@endpush

@section('content')
    <div class="content content-components">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-0">
                </ol>
            </nav>

            <div class="d-flex mg-y-20">
                <div class="pd-y-10 flex-grow-1"><h1 class="df-title m-0">Update permissions</h1></div>
                <div class="pd-y-10">
                    <input type="submit" form="main-form" class="btn btn-outline-secondary mr-4" value="Сохранить">
                    <button class="btn btn-outline-secondary" id="user_setter" type="button">Set for User</button>
                    <button class="btn btn-outline-secondary" id="editor_setter" type="button">Set for Editor</button>
                    <button class="btn btn-outline-secondary" id="director_setter" type="button">Set for Director</button>
                </div>
            </div>

            <hr class="mg-y-20">

            @include('errors')

            <form method="POST" id="main-form" action="{{ route('user.permission-edit', ['user'=>$user->id]) }}" enctype="multipart/form-data" class="needs-validation {{ !count($errors->toArray()) ?: 'was-validated' }}">
                @csrf
                @method('PUT')

                @foreach(config('permissions') as $groupSlug=>$group)
                    <div class="row row-sm mg-b-10">
                        <div class="col-sm-6 d-flex justify-content-between">
                            <span style="min-width: 200px; cursor: pointer" class="mb-2 d-block permission_group">{{ $group['name'] }}</span>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex justify-content-end">
                                @foreach($group['actions'] as $action=>$isActive)
                                    <div class="{{ $loop->last ?: 'mr-3' }}">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   {{ !$isActive ? 'disabled' : '' }}
                                                   {{ old('permission') !== NULL
                                                                ? (old("permission.{$groupSlug}_$action") == 1 ? 'checked' : '')
                                                                : ($user->hasPermissionFor("{$groupSlug}_$action")
                                                                                            ? 'checked'
                                                                                            : '') }}
                                                   name="permission[{{ $groupSlug.'_'.$action }}]"
                                                   value="1"
                                                   class="custom-control-input {{ !$isActive ? 'disabled' : '' }}"
                                                   id="{{ $groupSlug.'_'.$action }}">
                                            <label class="custom-control-label" for="{{ $groupSlug.'_'.$action }}">
                                                @switch($action)
                                                    @case('create')
                                                    создание
                                                    @break
                                                    @case('read')
                                                    чтение
                                                    @break
                                                    @case('update')
                                                    изменение
                                                    @break
                                                    @case('deleted')
                                                    Удаленные
                                                    @break
                                                    @case('delete')
                                                    удаление
                                                    @break
                                                    @case('publish')
                                                    публикация
                                                    @break
                                                    @case('all')
                                                    все
                                                    @break
                                                    @case('logs')
                                                    логи
                                                    @break
                                                @endswitch
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
@endsection
