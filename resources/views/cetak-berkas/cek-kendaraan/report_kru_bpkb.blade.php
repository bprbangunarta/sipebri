<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report KRU BPKB</title>
    <style>
        @page {
            size: A4;
            margin-top: 1.0cm;
            margin-bottom: 1.0cm;
            margin-left: -1.2cm;
            margin-right: 0cm;
        }

        body {
            margin: 0;
            /* font-family: 'Calibri', serif; */
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 106%;
            border-collapse: collapse;
            /* border: 1px solid #000000; */
            /* Menambahkan border ke seluruh tabel */
        }

        th,
        td {
            padding: 1px;
            text-align: left;
            /* Menambahkan border pada sisi kanan sel */
        }

        .br-1 {
            border-bottom: 1px solid #000000;
            border-right: 1px solid #000000;
        }

        th:last-child,
        td:last-child {
            border-right: none;
            /* Menghapus border kanan pada sel terakhir setiap baris */
        }

        th {
            background-color: #f2f2f2;
        }

        input {
            border: none;
        }

        .text-center {
            text-align: center;
        }

        .content {
            width: 100%;
            max-width: 100%;
            padding: 20px;
            box-sizing: border-box;
            text-align: justify;
        }

        @media print {
            body {
                font-size: 10pt;
            }

            .content {
                padding-top: 1.5cm;
                padding-bottom: 1.5cm;
                padding-left: 2cm;
                padding-right: 2cm;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <div class="content" style="margin-top: -57px;">
        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <h4 style="text-align: center; font-size: 12pt;">PT BPR BANGUNARTA <br>
            CHECK LIST KELENGKAPAN DOKUMEN SURAT PERNJANJIAN KREDIT</h4>
        <table style="border:1px solid black;">
            <thead>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="5%" rowspan="2" style="border:1px solid black;">No</th>
                    <th class="text-center" width="48%" rowspan="2" style="border:1px solid black;">Daftar Dokumen
                        Persyaratan Kredit
                    </th>
                    <th class="text-center" colspan="5" width="30%" style="border:1px solid black;">QC Petugas
                    </th>
                    <th class="text-center" rowspan="2" width="17%" style="border:1px solid black;">Catatan &
                        Kelengkapan</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th class="text-center" width="5%" style="border:1px solid black;">SAA</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">KAA</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">P.E</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">Real</th>
                    <th class="text-center" width="5%" style="border:1px solid black;">Legal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr style="border:1px solid black;">
                        <td class="text-center" style="border:1px solid black;">{{ $loop->iteration }}</td>
                        <td style="border:1px solid black;">&nbsp; {{ $item->nama_dokumen }}</td>
                        <td style="border:1px solid black;text-align:right;">
                            &nbsp;
                        </td>
                        <td style="border:1px solid black;text-align:right;"></td>
                        <td style="border:1px solid black;text-align:right;"></td>
                        <td style="border:1px solid black;text-align:right;"></td>
                        <td style="border:1px solid black;text-align:right;"></td>
                    </tr>
                @empty
                    NOT FOUND 404
                @endforelse

            </tbody>
            <tfoot>
                <tr style="height: 40px;">
                    <th class="text-center" colspan="2" style="border:1px solid black;"><b>Paraf Petugas QC</b>
                    </th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                    <th class="text-center" width="5%" style="border:1px solid black;"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
