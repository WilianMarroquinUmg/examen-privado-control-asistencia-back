<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; word-wrap: break-word; overflow-wrap: break-word; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<header>
    @include('datatables_exports.headers', ['title' => $title ?? 'Reporte', 'user' => $user ?? 'Sistema'])
</header>

<table>
    <thead>
    <tr>
        @foreach($columns as $col)
            @if($col['key'] === 'valor')
                <th style="width: 15%;">{{ $col['title'] }}</th>
            @else
                <th>{{ $col['title'] }}</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
        <tr>
            @foreach($columns as $col)
                <td>{{ data_get($row, $col['key']) }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->get_font("helvetica");
        $pdf->page_text($pdf->get_width() - 72, 583, "Pagina: {PAGE_NUM} / {PAGE_COUNT}", $font, 8, array(0, 0, 0));
    }
</script>
</body>
</html>
