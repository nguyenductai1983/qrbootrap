<?php

namespace App\Livewire\Production;

use App\Models\Color;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\WithReprinting;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\Department;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;

#[Title('Danh sách mã code')]
class ItemManager extends Component
{
    use WithPagination;
    use WithReprinting;

    // --- CÁC BIẾN BỘ LỌC ---
    public $searchCode = '';
    public $filterOrderId = '';
    public $filterProductId = '';
    public $filterColorId = '';
    public $filterDepartmentId = '';
    public $fromDate = '';
    public $toDate = '';

    // --- CÁC BIẾN CHỈNH SỬA ---
    public $editItemId = null;
    public $editCode = '';
    public $editProperties = []; // Mảng chứa dữ liệu JSON để edit
    public $showSuggestions = false; // Biến kiểm soát ẩn/hiện bảng gợi ý
    // Khai báo sẵn cho tương lai: public $current_location_id;
    public $editOriginalLength = null; // 🌟 Thêm biến này
    public $editLength = null;         // 🌟 Thêm biến này
    public $editGsm = null;
    public $editWeight = null;
    public $editShift = '';
    public $editNotes = '';
    public $historyItemCode = '';
    public $itemHistories = [];
    public function mount()
    {
        // Mặc định xuất 30 ngày gần đây
        $this->fromDate = now()->subDays(30)->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    // Reset trang khi thay đổi điều kiện lọc
    public function updatingSearchCode()
    {
        $this->resetPage();
        $this->showSuggestions = true; // Hiện gợi ý khi người dùng bắt đầu gõ
    }
    public function updatingFilterOrderId()
    {
        $this->resetPage();
    }
    public function updatingFilterProductId()
    {
        $this->resetPage();
    }
    public function updatingFilterColorId()
    {
        $this->resetPage();
    }
    public function updatingFilterDepartmentId()
    {
        $this->resetPage();
    }
    public function edit($id)
    {
        $item = Item::find($id);
        if ($item) {
            $this->editItemId = $item->id;
            $this->editCode = $item->code;

            // Ép kiểu về mảng để xử lý an toàn
            $this->editProperties = is_array($item->properties) ? $item->properties : json_decode($item->properties, true) ?? [];
            $this->editOriginalLength = $item->original_length;
            $this->editLength = $item->length;
            $this->editGsm = $item->gsm;
            $this->editWeight = $item->weight;
            $this->editShift = $item->shift;
            $this->editNotes = $item->notes;
            // Chuẩn bị cho chức năng định vị sắp tới:
            // $this->current_location_id = $item->current_location_id;

            $this->dispatch('open-modal');
        }
    }

    public function update()
    {
        if ($this->editItemId) {
            $item = Item::find($this->editItemId);

            $changesToLog = [
                'original_length' => ['old' => $item->original_length, 'new' => $this->editOriginalLength],
                'length' => ['old' => $item->length, 'new' => $this->editLength],
                'gsm'    => ['old' => $item->gsm, 'new' => $this->editGsm],
                'weight' => ['old' => $item->weight, 'new' => $this->editWeight],
                'shift'  => ['old' => $item->shift, 'new' => $this->editShift],
                'notes'  => ['old' => $item->notes, 'new' => $this->editNotes],
            ];

            foreach ($changesToLog as $field => $values) {
                // Chỉ so sánh giá trị null nếu nó thực sự khác, nếu == thì không lưu (float vs string)
                // Lưu ý so sánh weak '!='
                if ((string)$values['old'] !== (string)$values['new']) {
                    \App\Models\ItemHistory::create([
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'field_name' => $field,
                        'old_value' => $values['old'],
                        'new_value' => $values['new'],
                    ]);
                }
            }

            // Có thể thêm validation nếu cần thiết
            $item->update([
                'properties' => $this->editProperties,
                'original_length' => $this->editOriginalLength,
                'length' => $this->editLength,
                'gsm' => $this->editGsm,
                'weight' => $this->editWeight,
                'shift' => $this->editShift,
                'notes' => $this->editNotes,
                // 'current_location_id' => $this->current_location_id, // Mở ra khi bạn làm xong table Locations
            ]);

            session()->flash('message', 'Cập nhật chi tiết tem thành công!');
            $this->dispatch('close-modal');
        }
    }

    public function delete($id)
    {
        $item = Item::find($id);
        if ($item) {
            $code = $item->code;
            $item->delete();
            session()->flash('message', "🗑️ Đã xóa tem [{$code}] thành công!");
        }
    }

    public function viewHistory($id)
    {
        $item = Item::with('histories.user')->find($id);
        if ($item) {
            $this->historyItemCode = $item->code;
            $this->itemHistories = $item->histories;
            $this->dispatch('open-history-modal');
        }
    }

    public function selectSuggestion($Codestring)
    {
        $this->searchCode = $Codestring; // Điền tên vào ô input
        $this->showSuggestions = false;  // Giấu bảng gợi ý đi
        $this->resetPage();              // Cập nhật lại bảng dữ liệu chính
    }
    public function clearSearch()
    {
        $this->searchCode = '';
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function exportExcel()
    {
        if (empty($this->fromDate) || empty($this->toDate)) {
            session()->flash('message', 'Vui lòng chọn Từ ngày và Đến ngày để xuất Excel!');
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Item::with(['order', 'product', 'color', 'department', 'parents'])
            ->whereDate('created_at', '>=', $this->fromDate)
            ->whereDate('created_at', '<=', $this->toDate)
            ->when(!$user->canViewAllDepartments(), function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->when($this->searchCode, function ($q) {
                $q->where('code', 'like', '%' . $this->searchCode . '%');
            })
            ->when($this->filterOrderId, function ($q) {
                $q->where('order_id', $this->filterOrderId);
            })
            ->when($this->filterProductId, function ($q) {
                $q->where('product_id', $this->filterProductId);
            })
            ->when($this->filterColorId, function ($q) {
                $q->where('color_id', $this->filterColorId);
            })
            ->when($this->filterDepartmentId, function ($q) {
                $q->where('department_id', $this->filterDepartmentId);
            });

        $items = $query->orderBy('id', 'desc')->get();

        if ($items->isEmpty()) {
            session()->flash('message', 'Không có dữ liệu trong khoảng thời gian này để xuất Excel!');
            return;
        }

        return Excel::download(new ItemsExport($items), 'danh-sach-tem-' . date('Ymd_His') . '.xlsx');
    }

    public function render()
    {
        $this->js("console.log('Danh sách mã code')");
        // Query cơ bản kèm theo Relationship để tránh N+1 Query
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Item::with(['order', 'product', 'color', 'department', 'creator', 'parents'])
            ->when(!$user->canViewAllDepartments(), function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->when($this->searchCode, function ($q) {
                $q->where('code', 'like', '%' . $this->searchCode . '%');
            })
            ->when($this->filterOrderId, function ($q) {
                $q->where('order_id', $this->filterOrderId);
            })
            ->when($this->filterProductId, function ($q) {
                $q->where('product_id', $this->filterProductId);
            })
            ->when($this->filterColorId, function ($q) {
                $q->where('color_id', $this->filterColorId);
            })
            ->when($this->filterDepartmentId, function ($q) {
                $q->where('department_id', $this->filterDepartmentId);
            });
        // 3. Lấy dữ liệu cho danh sách gợi ý (Chỉ lấy 5 kết quả đầu tiên cho nhẹ)
        $suggestions = collect();
        if ($this->showSuggestions && strlen($this->searchCode) > 0) {
            $suggestions = (clone $query)->limit(5)->get();
        }
        return view('livewire.production.item-manager', [
            'items' => $query->orderBy('id', 'desc')->paginate(15),
            'orders' => Order::orderBy('id', 'desc')->get(),
            'products' => Product::orderBy('name', 'asc')->get(),
            'colors' => Color::orderBy('name', 'asc')->get(),
            'departments' => Department::orderBy('name', 'asc')->get(),
            'suggestions' => $suggestions, // Trả thêm biến suggestions ra View
        ]);
    }
}
