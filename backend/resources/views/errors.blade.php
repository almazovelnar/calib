@if(count($errors->toArray()))
    <div class="alert-c" role="alert">
        <div class="alert alert-danger">
        <span>
            @php $__index = 0; @endphp
            @foreach($errors->toArray() as $index=>$error)
                {{ ++$__index }}) {{ $error[array_key_first($error)] }} <br>
            @endforeach
        </span>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert-c" role="alert">
        <div class="alert alert-danger">
        <span>
            {{ session('error') }}
        </span>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="alert-c" role="alert">
        <div class="alert alert-success">
        <span>
            {{ session('success') }}
        </span>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="alert-c" role="alert">
        <div class="alert alert-warning">
        <span>
            {{ session('warning') }}
        </span>
        </div>
    </div>
@endif
