<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #000;
            font-size: 11px;
        }
        th, td {
            text-align: center;
            padding: 8px;
            font-size: 11px;
        }

        @page {
            margin: 0;
        }
        tr:nth-child(even){background-color: #f2f2f2}
    </style>
</head>
<body onload="window.print()">
    <h2 align="center" style="margin-top: 40px; margin-bottom: 10px">Laporan Transaksi</h2>
    <p align="center" style="margin-bottom: 5px">Periode : {{ $periode }}</p>
    <p align="center" style="margin-bottom: 40px">Dicetak oleh : {{ Auth::user()->name }}</p>
    <table border="1">
        <tr>
            <th>No.</th>
            <th>No. Invoice</th>
            <th>Pemesan</th>
            <th>Biaya</th>
            <th>Status</th>
            <th>Dibuat</th>
        </tr>

        @if($report->count() == 0)
            <tr>
                <td colspan="7">Tidak ditemukan data pada periode {{ $periode }}</td>
            </tr>
        @else
            @php
                $no = 1;
            @endphp

            @foreach($report->get() as $detail)
            @php
                if ($detail->is_status == 1) {
                    $status = '<span class="mb-1 badge font-medium bg-light-dark text-dark py-3 px-4 fs-7 text-center">Pending</span>';
                } elseif($detail->is_status == 2) {
                    $status = '<span class="mb-1 badge font-medium bg-light-primary text-primary py-3 px-4 fs-7 text-center">Dalam pengerjaan</span>';
                } elseif($detail->is_status == 3) {
                    $status = '<span class="mb-1 badge font-medium bg-light-info text-info py-3 px-4 fs-7 text-center">Dikirim</span>';
                } elseif($detail->is_status == 4) {
                    $status = '<span class="mb-1 badge font-medium bg-light-danger text-danger py-3 px-4 fs-7 text-center">Dibatalkan</span>';
                } elseif($detail->is_status == 5) {
                    $status = '<span class="mb-1 badge font-medium bg-light-success text-success py-3 px-4 fs-7 text-center">Selesai</span>';
                }
            @endphp
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $detail->invoice_number }}</td>
                <td>{{ \App\Models\User::where('id', $detail->user_id)->first()->name }}</td>
                <td>{{ 'Rp. ' . number_format($detail->price, 0, ',', '.') }}</td>
                <td>{!! $status !!}</td>
                <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('j F Y') }}</td>
            </tr>
            @endforeach
        @endempty
    </table>
</body>
</html>
