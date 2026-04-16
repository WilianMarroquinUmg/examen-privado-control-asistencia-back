<table width="100%" style="margin-bottom:10px; font-family: DejaVu Sans, sans-serif; border: none; border-collapse: separate;">
    <tr>
        <td style="width:20%; text-align:left; border:none;">
            @if(file_exists(public_path('logo_universidad.jpg')))
                <img src="{{ public_path('logo_universidad.jpg') }}" width="80" alt="Logo Guayacán">
            @else
                <strong>Logo Guayacán</strong>
            @endif
        </td>

        <td style="width:60%; text-align:center; border:none;">
            <h2 style="margin:0;"> Universidad Mariano Galvez de Guatemala</h2>
            <p style="margin:0; font-size:10px;">Reporte</p>
        </td>

        <td style="width:20%; text-align:right; font-size:10px; border:none;">
            <p style="margin:0;">Generado por {{ $user ?? 'Sistema' }}</p>
            <p style="margin:0;">{{ now()->format('d/m/Y H:i') }}</p>
        </td>
    </tr>
</table>
