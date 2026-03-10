<?php

namespace App\Exports;

use App\Models\QrScan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use Carbon\Carbon; // Thêm Carbon để xử lý ngày tháng

class QrScansExport implements FromCollection, WithHeadings, WithMapping
{
    protected string $startdate;
    protected string $enddate;
    protected Collection $filteredQrScansData; // Để lưu trữ data đã lọc
    protected array $allDataKeys = []; // Để lưu trữ tất cả các khóa duy nhất

    public function __construct(string $startdate, string $enddate)
    {
        $this->startdate = $startdate;
        $this->enddate = $enddate;

        $query = QrScan::query();

        // Áp dụng bộ lọc ngày
        if ($this->startdate) {
            $query->whereDate('created_at', '>=', $this->startdate);
        }
        if ($this->enddate) {
            // Thêm 1 ngày để bao gồm toàn bộ ngày cuối cùng
            $query->whereDate('created_at', '<=', Carbon::parse($this->enddate)->addDay()->toDateString());
        }

        // Lấy tất cả các bản ghi QrScan đã lọc
        $this->filteredQrScansData = $query->get();

        // Thu thập tất cả các khóa duy nhất từ trường 'data' của TẤT CẢ các bản ghi ĐÃ LỌC
        foreach ($this->filteredQrScansData as $qrscan) {
            if (is_array($qrscan->data)) {
                $this->allDataKeys = array_merge($this->allDataKeys, array_keys($qrscan->data));
            }
        }
        // Loại bỏ các khóa trùng lặp và sắp xếp chúng (tùy chọn)
        $this->allDataKeys = array_values(array_unique($this->allDataKeys));
        sort($this->allDataKeys); // Sắp xếp theo bảng chữ cái để thứ tự cột ổn định
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Trả về collection ban đầu đã được lấy trong constructor
        return $this->filteredQrScansData;
    }

    /**
     * Định nghĩa tiêu đề cho các cột trong file Excel.
     * Sẽ bao gồm ID của QrScan và tất cả các khóa từ trường 'data'.
     * @return array
     */
    public function headings(): array
    {
        // Thêm một cột 'ID QrScan' để biết dữ liệu này thuộc bản ghi nào
        return array_merge(['ID QrScan'], $this->allDataKeys);
    }

    /**
     * Tùy chỉnh cách mỗi hàng dữ liệu được hiển thị.
     * @param mixed $qrscan
     * @return array
     */
    public function map($qrscan): array
    {
        $rowData = [];
        $rowData[] = $qrscan->id; // Cột ID của QrScan

        // Nếu data không phải là mảng hoặc rỗng, coi nó là mảng rỗng
        $qrData = is_array($qrscan->data) ? $qrscan->data : [];

        // Lấy giá trị cho từng khóa đã thu thập trong headings
        foreach ($this->allDataKeys as $key) {
            // Sử dụng ?? 'N/A' để hiển thị 'N/A' nếu khóa không tồn tại
            $rowData[] = $qrData[$key] ?? 'N/A';
        }

        return $rowData;
    }
}
