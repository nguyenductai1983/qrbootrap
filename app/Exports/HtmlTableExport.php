<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; // Thêm để có tiêu đề cột
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Tùy chọn: tự động điều chỉnh độ rộng cột
class HtmlTableExport implements FromCollection
{

    protected $data; // Khai báo một biến để lưu trữ dữ liệu từ bảng HTML

    public function __construct(array $data)
    {
        $this->data = $data; // Gán dữ liệu được truyền vào
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
        //
    }
    public function headings(): array
    {
        // Đây là các tiêu đề cho cột Excel của bạn
        // Đảm bảo thứ tự và tên khớp với dữ liệu bạn cung cấp
        if (empty($this->data)) {
            return [];
        }
        // Lấy keys của dòng đầu tiên làm headings
        return array_keys($this->data[0]);
    }
}
