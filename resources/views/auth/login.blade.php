@extends('theme.auth')
@section('title', 'Sistem Pemberian Kredit')

@section('content')

<div class="login-box-body" style="border-radius: 5px;">

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group @error('username') has-error @enderror @error('email') has-error @enderror">
            <label>Username</label>
            <input type="text" class="form-control" placeholder="Username"
                name="username" value="{{ old('username') }}">

            @error('username')
                <span class="help-block">{{ $message }}</span>
            @enderror
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group @error('password') has-error @enderror">
            <label>Password</label>
            <input type="password" class="form-control" placeholder="********"
                name="password" value="{{ old('password') }}">

            @error('password')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            <div class="col-xs-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">MASUK</button>
            </div>
        </div>
    </form>

</div>
@endsection
