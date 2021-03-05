@extends('layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashforge.auth.css') }}">
@endpush

@push('js')
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('auth.recaptcha.key') }}&hl=en"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('{{ config('auth.recaptcha.key') }}', {action: '{{ csrf_token() }}'})
                .then(function (token) {
                    document.getElementById('recaptcha-token').value = token;
                    $('.wait-recaptcha').attr('disabled', false)
                });
        });

        setInterval(function () {
            $('.wait-recaptcha').attr('disabled', true)

            grecaptcha.execute('{{ config('auth.recaptcha.key') }}', {action: '{{ csrf_token() }}'})
                .then(function (token) {
                    document.getElementById('recaptcha-token').value = token;
                    $('.wait-recaptcha').attr('disabled', false)
                });
        }, 90 * 1000);
    </script>
@endpush

@section('content')
    <div class="content content-fixed content-auth">
        <div class="container">
            <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
                <div class="sign-wrapper" style="min-width: 280px">
                    <form class="wd-100p" action="{{ route('login') }}" method="POST">
                        @csrf

                        <input type="hidden" name="recaptcha-token" id="recaptcha-token">

                        <h3 class="tx-color-01 mg-b-5">Caliber Control Panel</h3>
                        <p class="tx-color-03 tx-16 mg-b-20">Please login to continue</p>

                        @include('errors')

                        <div class="form-group">
                            <label for="email">E-mail address</label>
                            <input required autofocus type="email" value="{{ old('email') }}" name="email" id="email" class="form-control" placeholder="user@exmaple.com">
                        </div>

                        <div class="form-group">
                            <div class="d-flex justify-content-between mg-b-5">
                                <label class="mg-b-0-f" for="password">Password</label>
                            </div>

                            <input required name="password" type="password" id="password" class="form-control" placeholder="********">
                        </div>

                        <button type="submit" class="btn btn-brand-02 btn-block btn-disable-if-valid wait-recaptcha" disabled>Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
