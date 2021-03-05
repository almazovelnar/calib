<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Caliber Control Panel</title>
    <link href="{{ asset('lib/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/select2/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('lib/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/quill/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/quill/quill.bubble.css') }}">
    <link rel="stylesheet" href="{{ asset('lib/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
    @stack('css')
    <link rel="stylesheet" href="{{ asset('css/dashforge.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashforge.profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin.charcoal.css') }}">

    <style>
        .custom-checkbox .custom-control-label::before, .custom-checkbox .custom-control-label::after,
        .custom-radio .custom-control-label::before,
        .custom-radio .custom-control-label::after,
        .custom-switch .custom-control-label::before,
        .custom-switch .custom-control-label::after {
            top: 49%;
        }

        .form-fieldset legend {
            top: -4px
        }

        .ui-tooltip {
            z-index: 999999;
        }

        .select2-container--default .select2-results__option[aria-disabled=true] {
            display: none;
        }

        .select2-search__field {
            outline: none;
        }

        #scrolling-container {
            padding-top: 60px;
        }

        .custom-file-label {
            text-overflow: ellipsis;
            overflow: hidden;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            white-space: nowrap;
        }

        .custom-file-label span {
            text-overflow: ellipsis;
            overflow: hidden;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            white-space: nowrap;
            width: 70%;
        }

        .timeline-body.red::before {
            border-color: red
        }

        #dt_wrapper{
            overflow: auto;
            width: 100%;
        }

        @media(max-width: 660px) {
            nav[aria-label=breadcrumb] {
                padding-left: 50px;
            }
        }
    </style>
</head>
<body>

@php($sidebar = !Route::is('login'))

@includeWhen($sidebar, 'aside')

@yield('content')

<script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('lib/jqueryui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('lib/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('lib/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('lib/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('lib/quill/quill.min.js') }}"></script>
<script src="{{ asset('lib/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('js/dashforge.js') }}"></script>
@if($sidebar)
    <script src="{{ asset('js/dashforge.aside.js') }}"></script>
@endif
<script>
    function routines() {
        $('.custom-file-input').change((e) => {
            if (!e.target.files.length) {
                $(e.target).siblings('label').html("<span>No files chosen</span>")
            } else {
                $(e.target).siblings('label').html("<span>" + e.target.files[0].name + "</span>")
            }
        })

        const inputLabels = $('input[required]:not([type=radio])').parents('.form-group').find('label:not(.custom-file-label):not(.custom-control-label)');

        if (inputLabels.find('span.text-danger').length)
            inputLabels.find('span.text-danger').remove()

        inputLabels.append('<span class=text-danger>&nbsp;*</span>')

        const textareaLabels = $('textarea:not(#editor)').parents('.form-group').find('label');

        if (textareaLabels.find('span.text-danger').length)
            textareaLabels.find('span.text-danger').remove()

        textareaLabels.append('<span class=text-danger>&nbsp;*</span>')

        const selectLabels = $('select[required]').parents('.form-group').find('label:not(.custom-file-label)');

        if (selectLabels.find('span.text-danger').length)
            selectLabels.find('span.text-danger').remove()

        selectLabels.append('<span class=text-danger>&nbsp;*</span>')
    }

    $(function () {
        'use strict'

        $('.btn-disable-if-valid').on('click', (e) => {
            let form = $(e.target).parents('form')[0];

            let attr = $(e.target).attr('form');

            if (typeof attr !== typeof undefined && attr !== false)
                form = $('form#' + $(e.target).attr('form'))[0];

            if (!form.checkValidity()) return;

            e.stopPropagation();
            e.preventDefault();

            $(e.target).attr('disabled', true);
            form.submit();
        });

        $('document').ready(() => {
            if ($('select').length)
                $('select').select2({
                    placeholder: 'Choose',
                    searchInputPlaceholder: 'Search fields',
                    allowClear: true
                });

            if ($('.dataTables_length select').length)
                $('.dataTables_length select').select2({
                    minimumResultsForSearch: Infinity
                });

            if ($('#dt').length)
                $('#dt').on('draw.dt', () => {
                    if ($('[data-toggle="tooltip"]').length) {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                });

            $('[data-toggle="tooltip"]').tooltip();
        })

        routines()

        $('#delete-*').on('click',function() {

        })
    });
</script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
        CKEDITOR.tools.enableHtml5Elements(document)
    // The trick to keep the editor in the sample quite small
    // unless user specified own height.
    CKEDITOR.config.height = 478
    CKEDITOR.config.width = 'auto'
    CKEDITOR.config.language = '{{ app()->getLocale() }}'
    CKEDITOR.config.filebrowserImageUploadUrl = '/news/upload?type=Images&_token={{csrf_token()}}'
    var wysiwygareaAvailable = isWysiwygareaAvailable(),
        isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode')
    var editorElement = CKEDITOR.document.getById('editor')
    CKEDITOR.config.extraPlugins = 'inserthtml4x';
    CKEDITOR.config.allowedContent= true;
    CKEDITOR.config.contentsCss = 'blockquote {padding-left: 30px;border-left: 2px solid gray;}p{max-width:1100px} iframe { max-width: 1100px !important; } img { margin-bottom: 10px; max-width: 1100px !important; } @media (max-width: 992px) { img { width: 100% !important; height: auto !important; display: block !important; } } img + small { margin-bottom: 30px; font-size: 14px; font-weight: 500; display: block; margin-top: 17px; color: rgba(57, 57, 57, .5); } b, strong { font-weight: 700; } p { font-size: 18px; font-weight: 300; line-height: 26px; } + div { display: flex; justify-content: space-between; align-items: flex-start; } + div > span { color: rgba(57, 57, 57, .5); } + div > span b { color: rgba(10, 10, 10, .5); font-weight: 700; } + div > ul li { display: inline-block; list-style: none; } + div > ul li:not(:last-of-type) { margin-right: 12px; } + div > ul li a img { height: 20px; width: auto; cursor: pointer; } '
    CKEDITOR.config.removeButtons = 'Save,NewPage,ExportPdf,Print,Templates,Cut,Copy,Paste,Find,Replace,Scayt,Form,Checkbox,Radio,TextField,Textarea,Flash,Table,Smiley,Anchor,Select,Button,ImageButton,HiddenField,CreateDiv';
    CKEDITOR.config.removePlugins = 'contextmenu,liststyle,tabletools,tableselection,pastetext,pastefromword,pastecode';

    CKEDITOR.config.allowedContent=  {
        script: true,
        div: true,
        $1: {
            // This will set the default set of elements
            elements: CKEDITOR.dtd,
            attributes: true,
            styles: true,
            classes: true
        }
    }

    // :(((

    if (isBBCodeBuiltIn) {
        editorElement.setHtml(
            'Hello world!\n\n' +
            'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
        )
    }
    // Depending on the wysiwygarea plugin availability initialize classic or inline editor.
    if (wysiwygareaAvailable) {
        CKEDITOR.replace('editor')
    } else {
        editorElement.setAttribute('contenteditable', 'true')
        CKEDITOR.inline('editor')
    }

    function isWysiwygareaAvailable() {
        // If in development mode, then the wysiwygarea must be available.
        // Split REV into two strings so builder does not replace it :D.
        if (CKEDITOR.revision == ('%RE' + 'V%')) {
            return true
        }
        return !!CKEDITOR.plugins.get('wysiwygarea')
    }

    CKEDITOR.config.disableNativeSpellChecker = false;

    CKEDITOR.instances.editor.on('change', function () {
        $('textarea[name=content]').val(this.getData())
    })
</script>
@stack('js')
</body>
</html>
