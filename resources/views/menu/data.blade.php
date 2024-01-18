@extends('theme.app')
@section('title', 'Data Tabel')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border" style="border-bottom: 1px solid #3C8DBC;">
                            <i class="fa fa-table" aria-hidden="true"></i>
                            <h3 class="box-title">ALL DATA</h3>
                        </div>

                        <div class="box-body" style="overflow: auto;white-space: nowrap;width: 100%;">
                            <table class="table table-bordered text-uppercase" style="font-size: 12px;">
                                <thead>
                                    <tr class="bg-blue">

                                        @forelse ($field as $item)
                                            <th class="text-center" width="3%">{{ $item }}</th>
                                        @empty
                                        @endforelse
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        <tr class="text-uppercase">
                                            @foreach ($item as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer clearfix">
                            <div class="pull-left hidden-xs">
                                <a href="{{ route('ubah.data') }}" class="btn btn-default btn-sm"
                                    style="background-color: #1f7bb0; color: white; font-weight: 500; border-radius: 5px;">
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
