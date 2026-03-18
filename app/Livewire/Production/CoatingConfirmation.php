<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Factory;
use App\Enums\ActionType;
use App\Services\ItemCodeService;
use App\Models\ItemProperty;

class CoatingConfirmation extends Component
{
    public $codeInput = '';
    public $scannedItems = [];
    public $usedLengths = [];
    public $newLength = '';
    public $coatingRatio = 1.07;
    // Hàm nhận mã vạch quét được
    public function mount()
    {
        // Lấy tỉ lệ cũ từ Cache (nếu user đã từng thao tác), mặc định là 1.07
        $this->coatingRatio = cache()->get('coating_ratio_' . Auth::id(), 1.07);
    }
    public function addScannedItem($code = null)
    {
        $codeToSearch = $code ? $code : $this->codeInput;
        $this->codeInput = '';

        if (count($this->scannedItems) >= 2) {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'Chỉ được ghép tối đa 2 cây vải!']);
            return;
        }

        $item = Item::where('code', trim($codeToSearch))->first();

        if (!$item) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Không tìm thấy mã tem này!']);
            return;
        }

        if ($item->length <= 0) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Cây vải này đã sử dụng hết (0m)!']);
            return;
        }

        // Validate: Không cho phép mang Cây Vải Đã Tráng đi tráng tiếp lần 2
        // Dựa vào việc kiểm tra xem cây đang quét có phải là "con" sinh ra từ hành động TRÁNG hay không.
        if ($item->parents()->wherePivot('action_type', ActionType::COATING->value)->exists()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Mã ' . $item->code . ' là cuộn Vải Tráng (không được tráng lại lần 2)!']);
            return;
        }

        // Nếu chưa quét thì thêm vào danh sách
        if (!collect($this->scannedItems)->contains('id', $item->id)) {
            $this->scannedItems[] = $item;
            $this->usedLengths[$item->id] = $item->length; // Mặc định dùng hết số mét đang có
        }
        $this->dispatch('update-calculations');
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
        // 1. KIỂM TRA ĐẦU VÀO (VALIDATE)
        if (empty($this->scannedItems)) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Vui lòng quét ít nhất 1 cây vải!']);
            return;
        }
        if (!$this->newLength || $this->newLength <= 0) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Vui lòng nhập chiều dài cây thành phẩm!']);
            return;
        }

        // Validate: used_length không được vượt quá chiều dài tồn kho của cây mộc
        foreach ($this->scannedItems as $scannedItem) {
            $itemId = $scannedItem['id'];
            $used = (float) ($this->usedLengths[$itemId] ?? 0);
            $currentLength = (float) $scannedItem['length'];

            if ($used <= 0) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Số mét sử dụng cho mã ' . $scannedItem['code'] . ' phải lớn hơn 0!']);
                return;
            }
            if ($used > $currentLength) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Mã ' . $scannedItem['code'] . ' không đủ mét (Tồn: ' . $currentLength . 'm, Đang dùng: ' . $used . 'm)!']);
                return;
            }
        }

        DB::beginTransaction();
        try {
            // 2. TÍNH TOÁN TỈ LỆ HAO HỤT
            $totalUsedLength = array_sum($this->usedLengths);
            if ($this->newLength > 0 && $totalUsedLength > 0) {
                $this->coatingRatio = $totalUsedLength / $this->newLength;
                cache()->forever('coating_ratio_' . Auth::id(), $this->coatingRatio);
            }

            $itemCount = count($this->scannedItems);
            $finalCode = '';
            $isUpdatedExisting = false;

            if ($itemCount == 1) {
                // ==========================================
                // NHÁNH 1: 1 CÂY ĐƠN
                // ==========================================
                $item1 = Item::find($this->scannedItems[0]['id']);
                $used1 = (float) $this->usedLengths[$item1->id];

                if ($used1 >= $item1->length) {
                    // [Yes] Dùng hết -> Lấy luôn cây cũ làm cây Tráng
                    $this->updateItemToCoated($item1, $totalUsedLength);
                    $finalCode = $item1->code;
                    $isUpdatedExisting = true;
                } else {
                    // [No] Không dùng hết -> Đẻ mã mới, trừ mét cây cũ
                    $coatedItem = $this->createCoatedItemFromSource($item1, $totalUsedLength);
                    $this->deductLengthAndAttach($coatedItem, $item1, $used1);
                    $finalCode = $coatedItem->code;
                }
            } else {
                // ==========================================
                // NHÁNH 2: 2 CÂY GHÉP
                // ==========================================
                $item1 = Item::find($this->scannedItems[0]['id']);
                $used1 = (float) $this->usedLengths[$item1->id];
                $isFullyUsed1 = ($used1 >= $item1->length);

                $item2 = Item::find($this->scannedItems[1]['id']);
                $used2 = (float) $this->usedLengths[$item2->id];
                $isFullyUsed2 = ($used2 >= $item2->length);

                if ($isFullyUsed1 && $isFullyUsed2) {
                    // [Dùng hết 2 cây] -> Cây 1 làm chính (Tráng), trừ mét cây 2
                    $this->updateItemToCoated($item1, $totalUsedLength);
                    $this->deductLengthAndAttach($item1, $item2, $used2);
                    $finalCode = $item1->code;
                    $isUpdatedExisting = true;
                } elseif ($isFullyUsed1 || $isFullyUsed2) {
                    // [Có 1 cây dùng hết] -> Lấy cây bị dùng hết làm chính (Tráng), trừ mét cây còn lại
                    $mainItem  = $isFullyUsed1 ? $item1 : $item2;
                    $otherItem = $isFullyUsed1 ? $item2 : $item1;
                    $otherUsed = $isFullyUsed1 ? $used2 : $used1;

                    $this->updateItemToCoated($mainItem, $totalUsedLength);
                    $this->deductLengthAndAttach($mainItem, $otherItem, $otherUsed);
                    $finalCode = $mainItem->code;
                    $isUpdatedExisting = true;
                } else {
                    // [Không cây nào dùng hết] -> Đẻ 1 mã mới hoàn toàn, trừ mét cả 2 cây cũ
                    $coatedItem = $this->createCoatedItemFromSource($item1, $totalUsedLength);
                    $this->deductLengthAndAttach($coatedItem, $item1, $used1);
                    $this->deductLengthAndAttach($coatedItem, $item2, $used2);
                    $finalCode = $coatedItem->code;
                }
            }

            DB::commit();
            $this->resetForm();

            $msg = $isUpdatedExisting ? 'Đã tráng xong! Cập nhật trên tem cũ: ' : 'Đã tạo thành công! Mã tem mới: ';
            $this->dispatch('alert', ['type' => 'success', 'message' => $msg . $finalCode]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // CÁC HÀM HỖ TRỢ (HELPERS) - ĐÃ ÁP DỤNG CHUẨN 4 CỘT CHIỀU DÀI
    // =========================================================================

    /**
     * Hành động: Tái sử dụng Record của cây cũ để biến nó thành cây Tráng Thành Phẩm
     */
    private function updateItemToCoated($item, $totalUsedLength)
    {
        $cleanProps = $item->properties ?? [];
        unset($cleanProps['DAI'], $cleanProps['DAI_THANH_PHAM'], $cleanProps['LENGTH']);
        $item->update([
            // 'type' => 2, // Đổi type thành loại Tráng để chống tráng lại lần 2
            // KHÔNG CẬP NHẬT original_length: Vẫn giữ chiều dài gốc (VD: 1000m) để tra cứu lịch sử mộc
            'finished_length' => $this->newLength, // Kết quả vải tráng
            'length' => $this->newLength,          // Tồn kho vải tráng
            'used_length' => $totalUsedLength,     // Tổng số mộc đầu vào đã ném vào nồi tráng này
            'properties' => $cleanProps
        ]);

        return $item;
    }

    /**
     * Hành động: Đẻ ra 1 Record hoàn toàn mới cho cây Tráng Thành Phẩm
     */
    private function createCoatedItemFromSource($sourceItem, $totalUsedLength)
    {
        $propParts = [];
        $dynamicProps = ItemProperty::where('is_code', true)->get();
        foreach ($dynamicProps as $prop) {
            $val = $sourceItem->properties[$prop->code] ?? null;
            if ($val !== null && $val !== '') {
                $part = ($prop->code_usage == 1) ? $prop->code : '';
                $part .= $val . ($prop->unit ?? '');
                $propParts[] = trim($part);
            }
        }

        // Nhét chiều dài thành phẩm vào mảng để sinh mã vạch
        if ($this->newLength > 0) {
            $propParts[] = intval($this->newLength);
        }

        $nextNo = Item::where('order_id', $sourceItem->order_id)->count() + 1;
        $finalCode = ItemCodeService::generateStandardCode(
            $sourceItem->order->code ?? '',
            $sourceItem->color->code ?? '',
            $sourceItem->specification->code ?? '',
            $sourceItem->width->code ?? '',
            $sourceItem->plasticType->code ?? '',
            $propParts,
            $nextNo
        );

        $cleanProps = $sourceItem->properties ?? [];
        unset($cleanProps['DAI'], $cleanProps['DAI_THANH_PHAM'], $cleanProps['LENGTH']);

        return Item::create([
            'code' => $finalCode,
            // 'type' => 2, // Đổi type thành loại Tráng
            'status' => 1,

            // 🌟 ÁP DỤNG CHUẨN 4 CỘT CHO CÂY MỚI:
            'original_length' => $this->newLength, // Khai sinh với số mét tem tráng để bảo vệ tính đúng đắn của mã
            'finished_length' => $this->newLength, // Kết quả vải tráng
            'length' => $this->newLength,          // Tồn kho vải tráng
            'used_length' => $totalUsedLength,     // Tổng số mộc đầu vào để đạt được kết quả tráng này

            'created_by' => Auth::id(),
            'order_id'         => $sourceItem->order_id,
            'product_id'       => $sourceItem->product_id,
            'color_id'         => $sourceItem->color_id,
            'specification_id' => $sourceItem->specification_id,
            'width_id'         => $sourceItem->width_id,
            'plastic_type_id'  => $sourceItem->plastic_type_id,
            'properties'       => $cleanProps,
        ]);
    }

    /**
     * Hành động: Trừ mét cây mộc (Tồn kho) và ghi phả hệ Pivot
     */
    private function deductLengthAndAttach($coatedItem, $oldItem, $usedLength)
    {
        $coatedItem->parents()->attach($oldItem->id, [
            'action_type' => ActionType::COATING->value,
            'used_length' => $usedLength,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        $remainingLength = $oldItem->length - $usedLength;

        $oldItem->update([
            'length' => $remainingLength > 0 ? $remainingLength : 0,           // Tồn kho mộc còn lại
            'used_length' => $oldItem->used_length + $usedLength,              // Cộng dồn lượng mộc đã xuất dùng
            'status' => $remainingLength <= 0 ? 0 : $oldItem->status           // Khóa nếu hết
        ]);
    }
    // Tách phần reset ra 1 hàm nhỏ cho code gọn gàng
    private function resetForm()
    {
        $this->scannedItems = [];
        $this->usedLengths = [];
        $this->newLength = '';
        $this->codeInput = '';
    }

    public function render()
    {
        // View chuẩn xác theo thư mục của bạn
        return view('livewire.production.coating-confirmation');
    }
}
