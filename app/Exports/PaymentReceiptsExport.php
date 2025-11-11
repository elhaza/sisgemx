<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentReceiptsExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(private array $receipts, private string $title = 'Comprobantes de Pago') {}

    public function headings(): array
    {
        return [
            'Fecha de Pago',
            'Estudiante',
            'Padre/Tutor',
            'Monto Pagado',
            'Período (Mes)',
            'Período (Año)',
            'Tipo',
            'Método de Pago',
            'Estado',
            'Fecha de Registro',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->receipts as $receipt) {
            $data[] = [
                $receipt->payment_date?->format('d/m/Y') ?? 'N/A',
                $receipt->student->user->full_name ?? 'N/A',
                $receipt->parent?->name ?? 'N/A',
                $receipt->amount_paid,
                $receipt->payment_month ?? 'N/A',
                $receipt->payment_year ?? 'N/A',
                isset($receipt->type) && $receipt->type === 'admin_payment' ? 'Admin' : 'Padre',
                $receipt->payment_method ?? 'N/A',
                $receipt->status?->value ?? 'N/A',
                $receipt->created_at?->format('d/m/Y H:i') ?? 'N/A',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '366092']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
