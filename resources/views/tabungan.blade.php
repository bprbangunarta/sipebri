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
        <div class="row justify-content-left">
            <div class="col-md-12">
                <br />
                <div class="card">
                    <div class="card-body">
                        <div class=" card-content table-responsive">
                            <table id="example" class="table table-striped table-bordered" style="width:100%">

                                <thead>
                                
                                    <th>No CIF</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIK</th>
                                    <th>TGL Lahir</th>
                                </thead>

                                <tbody>
                                    @foreach($tabungan as $row)
                                    <tr>
                                        <td>{{ $row->nocif }}</td>
                                        <td>{{ $row->fname }}</td>
                                        <td>{{ $row->noid }}</td>
                                        <td>{{ $row->jttempoid }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ $tabungan->onEachSide(0)->links('vendor.pagination.bootstrap-5') }}

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