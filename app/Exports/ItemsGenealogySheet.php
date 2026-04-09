<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ItemsGenealogySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected Collection $genealogyRows;

    public function __construct($items)
    {
        // Làm phẳng mối quan hệ cha-con thành danh sách hàng đơn
        $rows = collect();

        foreach ($items as $item) {
            if ($item->parents && $item->parents->isNotEmpty()) {
                foreach ($item->parents as $parent) {
                    $rows->push([
                        'child_code'   => $item->code,
                        'parent_code'  => $parent->code,
                        'used_length'  => $parent->pivot->used_length ?? '',
                        'action_type'  => $parent->pivot->action_type ?? '',
                        'traced_at'    => $parent->pivot->created_at
                            ? \Carbon\Carbon::parse($parent->pivot->created_at)->format('Y-m-d H:i:s')
                            : '',
                        'parent_product'    => $parent->product->name ?? '',
                        'parent_department' => $parent->department->name ?? '',
                    ]);
                }
            }
        }

        $this->genealogyRows = $rows;
    }

    public function title(): string
    {
        return 'Nguồn gốc Phả hệ';
    }

    public function collection()
    {
        return $this->genealogyRows;
    }

    public function headings(): array
    {
        return [
            'Mã Con (Child Code)',
            'Mã Cha (Parent Code)',
            'Mét đã trích (m)',
            'Hành động (Action)',
            'Thời điểm trích (Traced At)',
            'Sản phẩm Cha',
            'Xưởng Cha',
        ];
    }

    public function map($row): array
    {
        return [
            $row['child_code'],
            $row['parent_code'],
            $row['used_length'],
            $row['action_type'],
            $row['traced_at'],
            $row['parent_product'],
            $row['parent_department'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
