<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemHistory;
use App\Models\ItemMovement;
use App\Enums\ItemStatus;
use App\Enums\MovementAction;

class ItemWeightService
{
    /**
     * Xử lý cập nhật trọng lượng và ghi nhật ký.
     *
     * @param  Item        $item          Cây vải cần cập nhật
     * @param  float       $newWeight     Trọng lượng mới (kg)
     * @param  int|null    $userId        User thực hiện (null = hệ thống/API)
     * @param  string      $userNote      Ghi chú do kho nhập thêm
     * @param  bool        $forceSurplus  true = Tái nhập dư (bắt buộc dùng SURPLUS_ENTRY)
     *                                   false = Chỉ cập nhật cân (WEIGHT_UPDATE, không đổi status)
     * @return array
     */
    public function updateWeight(
        Item $item,
        float $newWeight,
        ?int $userId = null,
        string $userNote = '',
        bool $forceSurplus = false
    ): array {
        $oldWeight = (float) ($item->weight ?? 0);

        // Ghi nhận weight_original nếu là lần cân đầu tiên
        if ($item->weight_original === null) {
            $item->weight_original = $newWeight;
        }

        // Xác định loại hành động
        if ($forceSurplus) {
            $actionType = MovementAction::SURPLUS_ENTRY->value;
            $isSurplus  = true;
        } else {
            $actionType = MovementAction::WEIGHT_UPDATE->value;
            $isSurplus  = false;
        }

        // Tạo ghi chú hệ thống
        $systemNote = $isSurplus
            ? "Tái nhập dư: {$oldWeight}kg → {$newWeight}kg"
            : "Cập nhật trọng lượng: {$newWeight}kg" . ($oldWeight > 0 ? " (trước: {$oldWeight}kg)" : '');

        // Ghép ghi chú của kho nếu có
        $fullNote = $userNote
            ? "{$systemNote} | Lý do: {$userNote}"
            : $systemNote;

        // Ghi ItemMovement
        ItemMovement::create([
            'item_id'     => $item->id,
            'action_type' => $actionType,
            'user_id'     => $userId,
            'note'        => $fullNote,
            'created_at'  => now(),
        ]);

        // Ghi ItemHistory (dấu vết thay đổi trọng lượng)
        if ($oldWeight != $newWeight) {
            ItemHistory::create([
                'item_id'    => $item->id,
                'user_id'    => $userId,
                'field_name' => 'weight',
                'old_value'  => $oldWeight,
                'new_value'  => $newWeight,
                'note'       => $userNote ?: ($isSurplus ? 'Tái nhập dư' : 'Cập nhật cân'),
            ]);
        }

        // Cập nhật item
        $item->weight = $newWeight;
        if ($isSurplus) {
            // Tái nhập dư → chuyển về IN_WAREHOUSE
            $item->status = ItemStatus::IN_WAREHOUSE;
        }
        // WEIGHT_UPDATE → không đổi status
        $item->save();

        return [
            'is_surplus' => $isSurplus,
            'old_weight' => $oldWeight,
            'new_weight' => $newWeight,
            'note'       => $fullNote,
        ];
    }
}
