<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemMovement;
use App\Enums\ItemStatus;
use App\Enums\MovementAction;

class ItemWeightService
{
    /**
     * Process weight update logic and item movement logging.
     * User ID can be null for automated machine/API processes.
     * 
     * @param Item $item
     * @param float $newWeight
     * @param int|null $userId
     * @return array
     */
    public function updateWeight(Item $item, float $newWeight, ?int $userId = null): array
    {
        $oldWeight = (float) ($item->weight ?? 0);

        // Record weight_original if this is the first time the item is weighed
        if ($item->weight_original === null) {
            $item->weight_original = $newWeight;
        }

        // Determine if this is a secondary surplus entry
        $isSurplus = ($oldWeight > 0 && $newWeight < $oldWeight);

        $actionType = $isSurplus
            ? MovementAction::SURPLUS_ENTRY->value
            : MovementAction::WEIGHT_UPDATE->value;

        // Generate note depending on standard update or surplus
        $note = $isSurplus
            ? "Tái nhập dư sau SX: {$oldWeight}kg → {$newWeight}kg (giảm " . round($oldWeight - $newWeight, 2) . "kg)"
            : "Cập nhật trọng lượng: {$newWeight}kg" . ($oldWeight > 0 ? " (trước: {$oldWeight}kg)" : '');

        // Generate and record item movement
        ItemMovement::create([
            'item_id'     => $item->id,
            'action_type' => $actionType,
            'user_id'     => $userId,
            'note'        => $note,
            'created_at'  => now(),
        ]);

        // Update item data
        if ($isSurplus) {
            $item->status = ItemStatus::SURPLUS_ENTRY;
        }
        $item->weight = $newWeight;
        $item->save();

        return [
            'is_surplus' => $isSurplus,
            'old_weight' => $oldWeight,
            'new_weight' => $newWeight,
            'note'       => $note,
        ];
    }
}
