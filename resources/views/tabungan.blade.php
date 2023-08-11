<!DOCTYPE html>
<html>

<head>

    <title>Tabungan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
</head>

<body>
    <div class="container">
        <div class="card-header bg-secondary dark bgsize-darken-4 white card-header">
            <h4 class="text-white">Handling Excel Data using PHPSpreadsheet in Laravel</h4>
        </div>

        <div class="row justify-content-left">
            <div class="col-md-12">
                <br />
                <div class="card">
                    <div class="card-header bgsize-primary-4 white card-header">
                        <h4 class="card-title">Data Tabungan</h4>
                    </div>
                    <div class="card-body">
                        <div class=" card-content table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">

                                <thead>
                                    <th>No Tabungan</th>
                                    <th>No CIF</th>
                                    <th>Nama Lengkap</th>
                                </thead>

                                <tbody>
                                    @foreach($tabungan as $row)
                                    <tr>
                                        <td>{{ $row->noacc }}</td>
                                        <td>{{ $row->nocif }}</td>
                                        <td>{{ $row->fnama }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- {{ $tabungan->links('vendor.pagination.bootstrap-5') }} --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script>
            $(document).ready(function() {

           $('#example').DataTable();

       } );
        </script>
</body>

</html>