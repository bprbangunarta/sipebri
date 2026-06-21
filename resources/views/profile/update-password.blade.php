@extends('theme.app')
@section('title', 'Ubah Password')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                @if(session('error'))
                <div class="callout callout-danger">
                    <p>{{ session('error') }}</p>
                </div>
                @endif

                @if(session('success'))
                <div class="callout callout-success">
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <i class="fa fa-key"></i>
                        <h3 class="box-title">UBAH PASSWORD</h3>
                    </div>

                    <div id="formpassword" class="box-body"
                        style="overflow: auto;white-space: nowrap;width: 100%;margin-top:-10px;">

                        <form role="form" action="{{ route('codex.profile.password') }}" method="POST">
                            @csrf
                            <div class="box-body">

                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" id="current_password"
                                        placeholder="********">
                                    <p id="current-password" style="color: red;"></p>
                                </div>

                                <div class="form-group" style="margin-top:-5px;">
                                    <label>New Password</label>
                                    <div class="input-group" style="display: flex;">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Password" aria-describedby="password-toggle">
                                        <div class="input-group-append" style="align-self: center;">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="password-toggle">
                                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p id="pesan-kesalahan" style="color: red;"></p>
                                    <p id="konfirmasi-password" style="color: red;"></p>
                                </div>

                                <div class="form-group" style="margin-top:-5px;">
                                    <label>Confirm New Password</label>
                                    <div class="input-group" style="display: flex;">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="Password"
                                            aria-describedby="password-toggle">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="confirm-password-toggle">
                                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p id="pesan-kesalahan" style="color: red;"></p>
                                    <p id="konfirmasi-password-error" style="color: red;"></p>
                                </div>

                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn bg-blue" style="width: 100%;">SIMPAN</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@push('myscript')
<script>
    $(document).ready(function() {
        $('#password-toggle').on('click', function() {
            var passwordField = $('#password');
            var eyeIcon = $(this).find('i');

            var fieldType = passwordField.attr('type');
            if (fieldType === 'password') {
                passwordField.attr('type', 'text');
                eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else if (fieldType === 'text') {
                passwordField.attr('type', 'password');
                eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        $('#confirm-password-toggle').on('click', function() {
            var passwordField = $('#password_confirmation');
            var eyeIcon = $(this).find('i');

            var fieldType = passwordField.attr('type');
            if (fieldType === 'password') {
                passwordField.attr('type', 'text');
                eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else if (fieldType === 'text') {
                passwordField.attr('type', 'password');
                eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        $('#formpassword').submit(function(e) {
            var current = $('#current_password').val();
            var password = $('#password').val();
            var password_confirmation = $('#password_confirmation').val();

            var passwordArray = password.split(' ');
            var password_confirmationArray = password_confirmation.split(' ');

            var lastPasswordWord = passwordArray.pop();
            var lastConfirmPasswordWord = password_confirmationArray.pop();


            if (current.trim() === '') {
                e.preventDefault();
                $('#current-password').text('Password Lama Harus Diisi').fadeIn();
                setTimeout(function() {
                    $('#current-password').fadeOut();
                }, 1000);
            }

            if (password.trim() === '') {
                e.preventDefault();
                $('#konfirmasi-password').text('Kolom password harus diisi').fadeIn();
                setTimeout(function() {
                    $('#konfirmasi-password').fadeOut();
                }, 1000);
            }
            if (password_confirmation.trim() === '') {
                e.preventDefault();
                $('#konfirmasi-password-error').text('Kolom confirm password harus diisi').fadeIn();
                setTimeout(function() {
                    $('#konfirmasi-password-error').fadeOut();
                }, 1000);
            }

            if (lastPasswordWord !== lastConfirmPasswordWord) {
                $('#pesan-kesalahan').text('Bro Passwordnya Ada Yang Tidak Sama Tuhh!!!').fadeIn();
                e.preventDefault();
                setTimeout(function() {
                    $('#pesan-kesalahan').fadeOut();
                }, 1000);
            }

        });
    });
</script>
@endpush