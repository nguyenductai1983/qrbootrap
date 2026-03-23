<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use App\Models\ItemProperty;
use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\ActionType;
use App\Models\Product;

class CoatingConfirmation extends Component
{
    public $codeInput = '';
    public $scannedItems = [];
    public $usedLengths = [];
    public $newLength = '';
    public $coatingRatio = 1.07;
    public $products = [];
    public $selectedProductId = ''; // Model nhân viên chọn
    public $selectedMachineId = ''; // Máy đang thực hiện tráng
    public $machines = [];          // Danh sách máy được gán cho user này
    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Lấy đơn hàng đang chạy

        if ($user->department_id) {
            $this->products = Product::whereHas('departments', function ($q) use ($user) {
                $q->where('departments.id', $user->department_id);
            })->get();
        } else {
            $this->products = Product::all();
        }
        $this->coatingRatio = cache()->get('coating_ratio_' . Auth::id(), 1.07);

        // Lấy danh sách máy được phân công cho user (có status=true)
        $this->machines = $user->machines()->where('status', true)->orderBy('code')->get();

        // Gán mã thành phẩm mặc định là kết quả đầu tiên của danh sách products
        if (!empty($this->products) && count($this->products) > 0) {
            $firstProduct = is_array($this->products) ? $this->products[0] : $this->products->first();
            $this->selectedProductId = $firstProduct->id;
        }

        // Gán máy mặc định nếu chỉ có 1 máy
        if (count($this->machines) === 1) {
            $this->selectedMachineId = collect($this->machines)->first()->id;
        }
    }

    public function addScannedItem($code = null)
    {
        $codeToSearch = $code ? $code : $this->codeInput;
        $this->codeInput = '';

        $item = Item::where('code', trim($codeToSearch))->first();

        if (!$item) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Không tìm thấy mã tem này!']);
            return;
        }

        if ($item->length <= 0) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Cây vải này đã sử dụng hết (0m)!']);
            return;
        }

        // Tùy chọn: Chặn quét cây đã tráng (Nếu type của bạn Mộc = 1, Tráng = 2)
        // if ($item->type == 2) {
        //     $this->dispatch('alert', ['type' => 'error', 'message' => 'Mã này là Vải Tráng, không thể tráng tiếp!']);
        //     return;
        // }

        if (!collect($this->scannedItems)->contains('id', $item->id)) {
            $this->scannedItems[] = $item;
            $this->usedLengths[$item->id] = $item->length;

            // Bắn tín hiệu để JS tự động tính lại số mét Thành phẩm
            $this->dispatch('update-calculations');
        }
    }

    public function removeItem($index)
    {
        $itemId = $this->scannedItems[$index]['id'];
        unset($this->usedLengths[$itemId]);
        unset($this->scannedItems[$index]);
        $this->scannedItems = array_values($this->scannedItems);

        $this->dispatch('update-calculations');
    }

    public function confirmCoating()
    {
        // 1. VALIDATE ĐẦU VÀO
        if (empty($this->scannedItems)) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Vui lòng quét ít nhất 1 cây vải!']);
            return;
        }
        if (!$this->newLength || $this->newLength <= 0) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Vui lòng nhập chiều dài cây thành phẩm!']);
            return;
        }

        foreach ($this->scannedItems as $scannedItem) {
            $itemId = $scannedItem['id'];
            $used = (float) ($this->usedLengths[$itemId] ?? 0);
            $currentLength = (float) $scannedItem['length'];

            if ($used <= 0) {
                $this->dispatch('alert', ['type' => 'error', 'message' => "Số mét sử dụng cho mã {$scannedItem['code']} phải lớn hơn 0!"]);
                return;
            }
            if ($used > $currentLength) {
                $this->dispatch('alert', ['type' => 'error', 'message' => "Mã {$scannedItem['code']} không đủ mét (Tồn: {$currentLength}m)!"]);
                return;
            }
        }

        DB::beginTransaction();
        try {
            // 2. TÍNH TỈ LỆ HAO HỤT
            $totalUsedLength = array_sum($this->usedLengths);
            if ($this->newLength > 0 && $totalUsedLength > 0) {
                $this->coatingRatio = $totalUsedLength / $this->newLength;
                cache()->forever('coating_ratio_' . Auth::id(), $this->coatingRatio);
            }

            // 3. LẤY GỐC CÂY ĐẦU TIÊN ĐỂ BUILD MÃ
            $firstItemData = $this->scannedItems[0];
            $sourceItem = Item::with(['order', 'color', 'specification', 'width', 'plasticType'])
                ->find($firstItemData['id']);

            // 4. BUILD MÃ TRÁNG MỚI (CÓ CHỮ T)
            $baseParts = array_filter([
                $sourceItem->order->code ?? '',
                $sourceItem->color->code ?? '',
                $sourceItem->specification->code ?? '',
                $sourceItem->width->code ?? '',
                $sourceItem->plasticType->code ?? ''
            ]);

            // Lấy thuộc tính động (Ví dụ: GSM)
            $dynamicProps = ItemProperty::where('is_code', true)->get();
            foreach ($dynamicProps as $prop) {
                $val = $sourceItem->properties[$prop->code] ?? null;
                if ($val !== null && $val !== '') {
                    $part = ($prop->code_usage == 1) ? $prop->code : '';
                    $part .= $val . ($prop->unit ?? '');
                    $baseParts[] = trim($part);
                }
            }

            // Gắn chiều dài tráng vào trước chữ T
            $baseParts[] = intval($this->newLength);

            // Nối chuỗi cơ sở (Ví dụ: "H212NDS98 WE D8 1780 PP 150 650")
            $baseString = implode(' ', $baseParts);

            // Tìm STT tiếp theo dựa trên code của Product được chọn
            $selectedProduct = $this->selectedProductId ? \App\Models\Product::find($this->selectedProductId) : null;
            $productCode = $selectedProduct?->code ?? '';

            // Nếu có product code thì prefix = " PRODUCTCODE", không có thì để trống
            $prefix = $productCode ? (' ' . $productCode) : '';

            $countExisting = Item::where('code', 'LIKE', $baseString . $prefix . '%')->count();
            $nextNo = str_pad($countExisting + 1, 3, '0', STR_PAD_LEFT); // Format: 001, 002...

            // MÃ FINAL: Ví dụ: "H212NDS98 WE D8 1780 PP 150 650 VT001" hoặc "H212NDS98 WE D8 1780 PP 150 650 001"
            $finalCode = $baseString . $prefix . $nextNo;

            // Dọn rác JSON
            $cleanProps = $sourceItem->properties ?? [];
            unset($cleanProps['DAI'], $cleanProps['DAI_THANH_PHAM'], $cleanProps['LENGTH']);

            // 5. KHAI SINH CÂY TRÁNG THÀNH PHẨM
            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();

            $coatedItem = Item::create([
                'code' => $finalCode,
                // 'type' => 2, // 🌟 Thay số này bằng Type của Vải Tráng trong hệ thống bạn
                'status' => 1,
                'type' => 2,
                // 🌟 CHUẨN MỰC MỚI:
                'original_length' => $this->newLength, // Lấy độ dài tem làm gốc để bảo vệ mã code
                'length' => $this->newLength,          // Tồn kho hiện tại để bán/in

                'created_by' => Auth::id(),
                'order_id'         => $sourceItem->order_id,
                'product_id'       => $this->selectedProductId,
                'color_id'         => $sourceItem->color_id,
                'specification_id' => $sourceItem->specification_id,
                'width_id'         => $sourceItem->width_id,
                'plastic_type_id'  => $sourceItem->plastic_type_id,
                'properties'       => $cleanProps,
                'department_id'    => $currentUser->department_id,
                'machine_id'       => $this->selectedMachineId ?: null,
            ]);

            // 6. CẬP NHẬT CÂY MỘC CŨ VÀ GHI PHẢ HỆ
            foreach ($this->scannedItems as $oldItemData) {
                $oldItem = Item::find($oldItemData['id']);
                $used = (float) $this->usedLengths[$oldItem->id];

                // Ghi vết Pivot
                $coatedItem->parents()->attach($oldItem->id, [
                    'action_type' => ActionType::COATING->value,
                    'used_length' => $used,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                ]);

                // Trừ mét tồn kho Mộc
                $remainingLength = $oldItem->length - $used;
                $oldItem->update([
                    'length' => $remainingLength > 0 ? $remainingLength : 0,
                ]);
            }

            // 7. HOÀN TẤT
            DB::commit();
            $this->resetForm();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Đã tráng xong! Mã tem mới: ' . $finalCode]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    private function resetForm()
    {
        $this->scannedItems = [];
        $this->usedLengths = [];
        $this->newLength = '';
        $this->codeInput = '';
    }

    public function render()
    {
        return view('livewire.production.coating-confirmation');
    }
}
