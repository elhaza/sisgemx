<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobantes de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #fff;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #366092;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-validated {
            background-color: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .filters {
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 4px;
            font-weight: bold;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Comprobantes de Pago</h1>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        @if($status)
            <span>Estado: {{ ucfirst($status) }}</span>
        @endif
        @if($month)
            <span>Mes: {{ $month }}</span>
        @endif
        @if($year)
            <span>Año: {{ $year }}</span>
        @endif
        <span>Generado: {{ now()->format('d/m/Y H:i:s') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha de Pago</th>
                <th>Estudiante</th>
                <th>Padre/Tutor</th>
                <th>Monto Pagado</th>
                <th>Período</th>
                <th>Tipo</th>
                <th>Método de Pago</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $receipt)
                <tr>
                    <td>{{ $receipt->payment_date?->format('d/m/Y') ?? 'N/A' }}</td>
                    <td>{{ $receipt->student->user->full_name ?? 'N/A' }}</td>
                    <td>{{ $receipt->parent?->name ?? 'N/A' }}</td>
                    <td class="amount">${{ number_format($receipt->amount_paid, 2) }}</td>
                    <td>
                        @if($receipt->payment_month && $receipt->payment_year)
                            {{ $receipt->payment_month }}/{{ $receipt->payment_year }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if(isset($receipt->type) && $receipt->type === 'admin_payment')
                            Admin
                        @else
                            Padre
                        @endif
                    </td>
                    <td>{{ $receipt->payment_method ?? 'N/A' }}</td>
                    <td>
                        @if($receipt->status?->value === 'pending')
                            <span class="status-pending">Pendiente</span>
                        @elseif($receipt->status?->value === 'validated')
                            <span class="status-validated">Validado</span>
                        @elseif($receipt->status?->value === 'rejected')
                            <span class="status-rejected">Rechazado</span>
                        @else
                            {{ $receipt->status?->value ?? 'N/A' }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        No hay comprobantes para mostrar
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($receipts->count() > 0)
        <div class="summary">
            <div class="summary-item">
                <span>Total de Comprobantes:</span>
                <span>{{ $receipts->count() }}</span>
            </div>
            <div class="summary-item">
                <span>Monto Total:</span>
                <span>${{ number_format($receipts->sum('amount_paid'), 2) }}</span>
            </div>
        </div>
    @endif
</body>
</html>
