<?php

namespace App\Livewire;

use App\Models\QrScan; // Đảm bảo đúng namespace cho model của bạn
use Livewire\Component;
use Livewire\WithPagination; // Import trait WithPagination
use Maatwebsite\Excel\Facades\Excel; // Import facade của Laravel Excel
use App\Exports\QrScansExport;       // Import Export class của bạn
use Carbon\Carbon; // Thêm Carbon để xử lý ngày tháng
// use Illuminate\Auth\Access\AuthorizationException; // Để ném ngoại lệ
class QrcodeIndex extends Component
{
    use WithPagination; // Sử dụng trait WithPagination cho phân trang

    // Thuộc tính công khai cho bộ lọc và sắp xếp
    public $lastFetchedAt; // Lưu trữ thời gian của bản ghi mới nhất đã tải (để kiểm tra bản ghi mới)
    public $sortBy = 'created_at'; // Mặc định sắp xếp theo created_at
    public $sortDirection = 'desc'; // Mặc định sắp xếp giảm dần (mới nhất lên đầu)
    public $startdate; // Thuộc tính cho ngày bắt đầu
    public $enddate;   // Thuộc tính cho ngày kết thúc
    public $numpaginate = 5;
    // Thuộc tính cho chức năng thông báo bản ghi mới
    public $newItemsCount = 0; // Đếm số lượng bản ghi mới được phát hiện
    public $showNewItemsButton = false; // Điều khiển hiển thị nút "Tải bản ghi mới"
    // Thêm thuộc tính tìm kiếm
    public $search = '';
    // Constructor hoặc mount để khởi tạo dữ liệu ban đầu
    public function mount()
    {
        // Khởi tạo ngày bắt đầu là ngày hôm qua và ngày kết thúc là ngày hiện tại
        $this->startdate = Carbon::yesterday()->toDateString();
        $this->enddate = Carbon::now()->toDateString();

        // Lấy thời gian của bản ghi mới nhất trong toàn bộ DB khi component mount
        // Đây là mốc để kiểm tra các bản ghi mới hơn sau này
        $this->lastFetchedAt = QrScan::max('created_at');
        if (!$this->lastFetchedAt instanceof Carbon) {
            $this->lastFetchedAt = Carbon::parse($this->lastFetchedAt) ?? Carbon::now();
        }
    }

    /**
     * Computed Property để lấy dữ liệu QR codes đã được phân trang.
     * Livewire sẽ tự động gọi phương thức này và gán kết quả cho $this->qrcodes trong view.
     */
    public function getQrcodesProperty()
    {
        $query = QrScan::query();

        // Áp dụng bộ lọc ngày tháng
        if ($this->startdate) {
            $query->whereDate('created_at', '>=', $this->startdate);
        }
        if ($this->enddate) {
            $query->whereDate('created_at', '<=', $this->enddate);
        }
        // Áp dụng tìm kiếm theo qr_code_id
        if (!empty($this->search)) {
            $query->where('qr_code', 'like', '%' . $this->search . '%');
            // Hoặc 'qr_code_id' nếu đó là tên cột bạn thực sự muốn tìm kiếm
            // $query->where('qr_code_id', 'like', '%' . $this->search . '%');
        }
        // Áp dụng sắp xếp
        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } else {
            // Mặc định sắp xếp theo created_at giảm dần nếu không có sortBy
            $query->latest();
        }

        // Trả về dữ liệu đã phân trang
        return $query->with('user')->paginate($this->numpaginate); // Phân trang 10 bản ghi mỗi trang
    }

    /**
     * Phương thức này sẽ được gọi bằng wire:poll để kiểm tra các bản ghi MỚI HƠN.
     * Nó không trực tiếp thêm vào danh sách hiện tại mà thông báo cho người dùng.
     */
    public function loadNewQrcodes()
    {
        // Lấy thời gian của bản ghi mới nhất hiện tại trong DB
        $latestDbRecordTime = QrScan::max('created_at');

        // Chuyển đổi $latestDbRecordTime thành Carbon instance nếu nó không phải là null
        if ($latestDbRecordTime && !$latestDbRecordTime instanceof Carbon) {
            $latestDbRecordTime = Carbon::parse($latestDbRecordTime);
        }

        // Chỉ tiếp tục nếu $latestDbRecordTime không phải là null và là một Carbon instance hợp lệ
        if ($latestDbRecordTime instanceof Carbon && $latestDbRecordTime->greaterThan($this->lastFetchedAt)) {
            // Đếm số lượng bản ghi mới
            $newItems = QrScan::where('created_at', '>', $this->lastFetchedAt)->count();
            $this->newItemsCount = $newItems;
            $this->showNewItemsButton = true;
            $this->lastFetchedAt = $latestDbRecordTime; // Cập nhật thời gian bản ghi mới nhất
        }
    }
    /**
     * Phương thức này được gọi khi người dùng nhấn nút "Tải bản ghi mới".
     * Nó sẽ làm mới toàn bộ dữ liệu phân trang và đặt lại về trang đầu tiên.
     */
    public function refreshData()
    {
        $this->resetPage(); // Đặt lại về trang đầu tiên
        $this->newItemsCount = 0; // Reset số lượng bản ghi mới
        $this->showNewItemsButton = false; // Ẩn nút thông báo
        // Livewire sẽ tự động gọi lại getQrcodesProperty() và render lại view
    }
 public function applyFilters()
    {
        $this->resetPage(); // Reset về trang 1 khi áp dụng bộ lọc mới
        // Livewire sẽ tự động gọi lại getQrcodesProperty() khi render() được gọi
        // và resetPage() sẽ kích hoạt render.
    }
    /**
     * Các hook (phương thức "updated") để reset trang khi các bộ lọc/sắp xếp thay đổi.
     */
    public function updatedStartdate()
    {
        $this->resetPage();
    }

    public function updatedEnddate()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedSortDirection()
    {
        $this->resetPage();
    }

    public function exportDataToExcel()
    {
        $fileName = 'qr_data_details_' . now()->format('Ymd_His') . '.xlsx';

        // Gọi QrScansExport với các bộ lọc ngày tháng
        return Excel::download(new QrScansExport($this->startdate, $this->enddate), $fileName);
    }

    public function render()
    {
        // throw new AuthorizationException('Bạn không có quyền truy cập bảng điều khiển quản trị.');
        // View sẽ nhận biến $qrcodes từ computed property getQrcodesProperty()
        return view('livewire.qrcode.index', [
            'qrcodes' => $this->qrcodes // Livewire tự động biến getQrcodesProperty thành $this->qrcodes
        ]);
    }
}
