@extends('master.user.menu', [$data])
@section('title', 'Give Permission')

@section('content')
    <div class="tab-content">
        <div class="tab-pane active">
            <table class="table table-striped table-hover table-bordered table-condensed">
                {{-- <thead>
                    <tr>
                        <th class="text-center">Permission Name</th>
                        <th class="text-center">Pilih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permission as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td><input type="checkbox" name="item1" value="value1"></td>
                        </tr>
                    @empty
                    @endforelse
                </tbody> --}}

                <thead>
                    <tr>
                        <th class="text-center">Permission Name</th>
                        <th class="text-center">Pilih</th>
                        <th class="text-center">Permission Name</th>
                        <th class="text-center">Pilih</th>
                        {{-- <th class="text-center">Permission Name</th>
                        <th class="text-center">Pilih</th>
                        <th class="text-center">Permission Name</th>
                        <th class="text-center">Pilih</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permission->chunk(2) as $chunk)
                        <tr>
                            @foreach ($chunk as $items)
                                <td>{{ $items->name }}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="give-permission" name="give-permission" id="give-permission"
                                        value="{{ $items->id }}" data-id1="{{ $items->id }}"
                                        data-id2="{{ $data->role_id }}"
                                        @foreach ($role as $item)
                                            @if ($item->permission_id == $items->id && $item->role_id == $data->role_id) 
                                                checked 
                                            @break 
                                            @endif @endforeach>
                                </td>
                            @endforeach

                            {{-- Menambahkan kolom kosong jika kurang dari 4 --}}
                            @for ($i = 0; $i < 2 - $chunk->count(); $i++)
                                <td></td>
                                <td></td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No permissions available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('myscript')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '.give-permission', function() {
                if ($(this).is(':checked')) {
                    const value1 = $(this).data("id1");
                    const value2 = $(this).data("id2");

                    $.ajax({
                        url: "{{ route('permission.postpermission') }}",
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        data: {
                            id1: value1,
                            id2: value2,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Panggil SweetAlert setelah permintaan AJAX berhasil
                            var da = JSON.stringify(response);
                            var data = JSON.parse(da);
                            var convert = data.split(' ').map(function(word) {
                                return word.charAt(0).toUpperCase() + word.slice(1);
                            }).join(' ');
                            var pesan = '<span style="color: blue;">' + convert +
                                '</span> berhasil diterapkan.';

                            Swal.fire({
                                position: 'center',
                                title: 'Success!',
                                html: pesan,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1100,
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error if needed
                        }
                    });
                } else {
                    const value1 = $(this).data("id1");
                    const value2 = $(this).data("id2");

                    $.ajax({
                        url: "{{ route('permission.destroypermission') }}",
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        data: {
                            id1: value1,
                            id2: value2,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            // Panggil SweetAlert setelah permintaan AJAX berhasil
                            var da = JSON.stringify(response);
                            var data = JSON.parse(da);

                            var convert = data.split(' ').map(function(word) {
                                return word.charAt(0).toUpperCase() + word.slice(1);
                            }).join(' ');
                            var pesan = '<span style="color: blue;">' + convert +
                                '</span> berhasil dihapus.';

                            Swal.fire({
                                position: 'center',
                                title: 'Success!',
                                html: pesan,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1100,
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error if needed
                        }
                    });
                }
            });
        })
    </script>
@endpush
