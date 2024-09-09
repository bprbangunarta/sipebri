<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No Surat</th>
            <th>No Loan</th>
            <th>No CIF</th>
            <th>No SPK</th>
            <th>Nama Debitur</th>
            <th>Pendamping</th>
            <th>Plafond Awal</th>
            <th>JW Awal</th>
            <th>Eff Real</th>
            <th>Alamat</th>
            <th>No Acc SIMAPAN</th>
            <th>ID KTP</th>
            <th>Pekerjaan</th>
            <th>No PK RSC RSC</th>
            <th>Plafond RSC</th>
            <th>JW RSC</th>
            <th>Rate RSC</th>
            <th>RPS RSC</th>
            <th>Jenis RSC</th>
            <th>Eff RSC</th>
            <th>RSC Byr Pokok</th>
            <th>RSC Byr Bunga</th>
            <th>Covid-19 Pokok</th>
            <th>JW Covid-19</th>
            <th>Status Covid-19</th>
            <th>Sisa OS</th>
            <th>Konversi</th>
            <th>Tung. Bunga</th>
            <th>Denda</th>
            <th>Adm. Provisi RSC</th>
            <th>Angs. Pokok</th>
            <th>Angs. Bunga</th>
            <th>Denda</th>
            <th>UJROH</th>
            <th>Asuransi TLO (211203)</th>
            <th>Polis TLO (211206)</th>
            <th>Asuransi Jiwa</th>
            <th>Vendor AJK</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ (int) $item->nomor }}</td>
                <td>{{ $item->no_loan }}</td>
                <td>{{ $item->no_cif }}</td>
                <td>{{ $item->spk }}</td>
                <td>{{ $item->nama_nasabah }}</td>
                <td>{{ $item->nama_pendamping }}</td>
                <td>{{ $item->plafon }}</td>
                <td>{{ $item->jangka_waktu }}</td>
                <td>{{ $item->tgl_eff }}</td>
                <td>{{ $item->alamat_ktp }}</td>
                <td>{{ $item->no_acc_simapan }}</td>
                <td>{{ $item->no_identitas }}</td>
                <td>{{ $item->nama_pekerjaan }}</td>
                <td>{{ $item->spk_rsc }}</td>
                <td>{{ $item->plafon_rsc }}</td>
                <td>{{ $item->jw_rsc }}</td>
                <td>{{ $item->rate_rsc }}</td>
                <td>{{ $item->rps_rsc }}</td>
                <td>{{ $item->jenis_rsc }}</td>
                <td>{{ $item->tgl_eff_rsc }}</td>
                <td>{{ $item->rsc_bayar_pokok }}</td>
                <td>{{ $item->rsc_bayar_bunga }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $item->sisa_os }}</td>
                <td></td>
                <td>{{ $item->tunggakan_bunga }}</td>
                <td>{{ $item->tunggakan_denda }}</td>
                <td>{{ $item->administrasi_nominal }}</td>
                <td>{{ $item->angsuran_pokok }}</td>
                <td>{{ $item->angsuran_bunga }}</td>
                <td>{{ $item->denda_dibayar }}</td>
                <td>{{ $item->ujroh }}</td>
                <td>{{ $item->asuransi_tlo }}</td>
                <td></td>
                <td>{{ $item->asuransi_jiwa }}</td>
                <td></td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
