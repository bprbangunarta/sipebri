<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Analisa 5C</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            /* font-family: 'Calibri', serif; */
            font-family: "Times New Roman", Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* border: 2px solid #000000; */
            /* Menambahkan border ke seluruh tabel */
        }

        th:last-child,
        td:last-child {
            border-right: none;
            /* Menghapus border kanan pada sel terakhir setiap baris */
        }

        th {
            background-color: #ffffff;
        }

        input {
            border: none;
        }

        .text-center {
            text-align: center;
        }

        /* .content {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            margin-top: -10px;
            text-align: justify;
        } */

        @media print {
            body {
                font-size: 12pt;
            }

            .content {
                padding: 1.5cm;
            }
        }

        .penghasilan {
            margin-left: 26px;
            margin-top: -14px;
        }

        .taksasi {
            border: 1px solid black;
            padding: 2px;
            margin-top: -8px;
        }

        .taksasi th {
            font-size: 14px;
            text-align: center;
            border-bottom: 1px solid black;
            padding: 5px;
        }

        .rg {
            font-size: 12px;
            border-right: 1px solid black;
            padding: 5px;
        }

        .taksasi td {
            position: relative;
            padding-left: 15px;
            padding-top: 1px;
        }
    </style>
</head>

<body>
    <div class="content">

        <img src="{{ asset('assets/img/pba.png') }}" style="width:200px;">
        <hr style="border: 1px solid 034871;">

        <p style="font-weight: bold; margin-top: -8px;">VI. ANALISA 5C</p>
        <div class="taksasi">
            <table>
                <tr>
                    <th style="width: 250px;">KRITERIA</th>
                    <th style="width: 150px;" colspan="2">PENILAIAN</th>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>1. CHARACTER</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Tidak Melakukan hal-hal tercela
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['perbuatan_tercela'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Terbuka dan konsisten
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['konsisten'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;c. Gaya Hidup
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['gaya_hidup'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;d. Kepatuhan Terhadap Kewajiba
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['kepatuhan'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;e. Keharmonisan Rumah Tangga
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['harmonis'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;f. Hubungan Sosial dengan Lingkungan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['hubungan_sosial'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;g. Pengendalian Emosi
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $analisa['pengendalian_emosi'] }}</td>
                    <td style="width: 200px;"></td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>2. CAPACITY</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Pengalaman usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['pengalaman_usaha'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['pengalaman_usaha'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['pertumbuhan_usaha'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['pertumbuhan_usaha'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;c. Kontinuitas usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['kontinuitas'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['kontinuitas'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;d. Aset terkait usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['aset_terkait_usaha'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['aset_terkait_usaha'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;e. Aset diluar usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['aset_diluar_usaha'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['aset_diluar_usaha'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;f. Repayment Capacity
                    </td>
                    <td class="rg" tyle="width: 150px;">-</td>
                    <td style="width: 200px;">{{ $capacity['rc'] . ' ' . '%' }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;g. Pendatatan Laporan Keuangan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['laporan_keuangan'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['laporan_keuangan'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;h. Pengalaman Kredit Masa Lalu
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['catatan_kredit'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['catatan_kredit'][1] }}</td>
                </tr>
                {{-- <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;i. BI Checking
                    </td>
                    <td class="rg" tyle="width: 150px;"> > 5 Tahun</td>
                    <td style="width: 200px;">Sangat baik</td>
                </tr> --}}
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Pertumbuhan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['pertumbuhan_usaha'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['pertumbuhan_usaha'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>3. CAPITAL</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Sumber Pemodalan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $capacity['capital_sumber_modal'][0] }}</td>
                    <td style="width: 200px;">{{ $capacity['capital_sumber_modal'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>4. COLLATERAL</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Kepemilikan Agunan Utama
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['agunan_utama'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['agunan_utama'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Kepemilikan Agunan Tambahan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['agunan_tambahan'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['agunan_tambahan'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;c. Legalitas Agunan Utama
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['legalitas_agunan_utama'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['legalitas_agunan_utama'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;d. Legalitas Agunan Tambahan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['legalitas_agunan_tambahan'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['legalitas_agunan_tambahan'][0] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;e. Mudah diuangkan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['mudah_diuangkan'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['mudah_diuangkan'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;f. Stabilitas Harga
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['stabilitas_harga'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['stabilitas_harga'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;g. Kondisi Fisik Kendaraan
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['kondisi_kendaraan'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['kondisi_kendaraan'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;h. Lokasi SHM
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $collateral['lokasi_shm'][0] }}</td>
                    <td style="width: 200px;">{{ $collateral['lokasi_shm'][0] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;i. Permohonan Taksasi Agunan
                    </td>
                    <td class="rg" tyle="width: 150px;">-</td>
                    <td style="width: 200px;">{{ $collateral['taksasi_agunan'] . ' ' . '%' }}</td>
                </tr>
                <tr>
                    <td class="rg" style="displpay-flex; font-size: 12px;"><b>5. CONDITION</b></td>
                    <td class="rg"></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;a. Kondisi Alam
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $condition['kondisi_alam'][0] }}</td>
                    <td style="width: 200px;">{{ $condition['kondisi_alam'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;b. Persaingan usaha
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $condition['persaingan_usaha'][0] }}</td>
                    <td style="width: 200px;">{{ $condition['persaingan_usaha'][1] }}</td>
                </tr>
                <tr>
                    <td class="rg" style="width: 250px;">
                        &ensp;&ensp;c. Regulasi Pemerintah
                    </td>
                    <td class="rg" tyle="width: 150px;">{{ $condition['regulasi_pemerintah'][0] }}</td>
                    <td style="width: 200px;">{{ $condition['regulasi_pemerintah'][1] }}</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; position:relative; float: right;">Pamanukan, {{ $data->hari }} <br>
            Surveyor <br>
            <br>
            <br><br>
            <b>{{ $data->surveyor }}</b>
        </p>
    </div>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
