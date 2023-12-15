@extends('theme.app')
@section('title', 'Ubah Password')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <i class="fa fa-key"></i>
                            <h3 class="box-title">UBAH PASSWORD</h3>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;margin-top:-10px;">
                            <form role="form">
                                <div class="box-body">

                                    <div class="form-group">
                                        <label>Current Password</label>
                                        <input type="password" class="form-control" name="password" placeholder="********">
                                    </div>

                                    <div class="form-group" style="margin-top:-5px;">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" name="new_password" placeholder="********">
                                    </div>

                                    <div class="form-group" style="margin-top:-5px;">
                                        <label>Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" placeholder="********">
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

