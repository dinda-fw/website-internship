<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 2px; }
        p.sub { margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #e2001a; color: #fff; }
        tr:nth-child(even) { background: #f7f7f7; }
    </style>
</head>
<body>
    <h2>Laporan Data Barang</h2>
    <p class="sub">PT Telkomsel &mdash; Dicetak pada {{ now()->translatedFormat('d M Y, H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Total Stok</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ $product->total_stock }}</td>
                    <td>{{ $product->location ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $product->condition)) }}</td>
                    <td>{{ $product->statusLabel() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
