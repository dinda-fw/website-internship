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
    <h2>Laporan Peminjaman Barang</h2>
    <p class="sub">PT Telkomsel &mdash; Dicetak pada {{ now()->translatedFormat('d M Y, H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Barang</th>
                <th>Tgl Pinjam</th>
                <th>Batas Kembali</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowings as $borrowing)
                <tr>
                    <td>{{ $borrowing->borrower_name }}</td>
                    <td>
                        @foreach($borrowing->details as $detail)
                            {{ $detail->product->name ?? '-' }} (x{{ $detail->quantity }})@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>{{ $borrowing->borrow_date->format('d-m-Y') }}</td>
                    <td>{{ $borrowing->due_date?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ $borrowing->return_date?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ ucfirst($borrowing->status) }}</td>
                    <td>{{ $borrowing->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
