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
use Livewire\Attributes\Title;
use App\Events\PrintLabelRequested;
use App\Events\PrintLabelAppEvent;
use App\Models\PrintJob;
use App\Models\PrintStation;

#[Title('Xác nhận Tráng Ghép')]
class CoatingConfirmation extends Component
{
    public $codeInput = '';
    public $scannedItems = [];
    public $usedLengths = [];
    public $newLength = '';
    public $coatingRatio = 1.0;
    public $minWidth = 0; // Theo dõi khổ nhỏ nhất của các cây được quét
    public $products = [];
    public $selectedProductId = ''; // Model nhân viên chọn
    public $selectedMachineId = ''; // Máy đang thực hiện tráng
    public $machines = [];          // Danh sách máy được gán cho user này
    public $printStations = [];     // Danh sách trạm in được gán cho user
    public $printerMac = ''; // Máy in mặc định
    public $manualPrintRequired = null; // Trạng thái chứa mã khi chưa in

    // Cấu hình tính năng mới: Lami và Cắt khổ
    public $lami = 1; // Độ dày màng ghép
    public $cutMode = 'keep'; // 'keep', 'trim', 'split'
    public $trimWidth = ''; // Nhập khổ nếu xén
    public $splitWidth1 = ''; // Khổ cuộn chia 1
    public $splitWidth2 = ''; // Khổ cuộn chia 2
    public $recoverEdgeTrim = false; // Phục vụ tính năng thu hồi biên dư

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
        $this->coatingRatio = cache()->get('coating_ratio_' . Auth::id(), 1);

        // Lấy danh sách máy và trạm in được phân công
        $this->machines = $user->machines()->where('status', true)->orderBy('code')->get();
        $this->printStations = $user->printStations()->where('status', true)->orderBy('code')->get();

        $cachedPrinterMac = cache()->get('printer_mac_' . Auth::id());
        $cachedProductId = cache()->get('selected_product_id_' . Auth::id());
        $cachedMachineId = cache()->get('selected_machine_id_' . Auth::id());

        if ($cachedPrinterMac && collect($this->printStations)->contains('code', $cachedPrinterMac)) {
            $this->printerMac = $cachedPrinterMac;
        } elseif (count($this->printStations) > 0) {
            $this->printerMac = collect($this->printStations)->first()->code;
        }

        // Gán mã thành phẩm mặc định (Ưu tiên Cache -> Phần tử đầu)
        if ($cachedProductId && collect($this->products)->contains('id', $cachedProductId)) {
            $this->selectedProductId = $cachedProductId;
        } elseif (!empty($this->products) && count($this->products) > 0) {
            $firstProduct = is_array($this->products) ? $this->products[0] : $this->products->first();
            $this->selectedProductId = $firstProduct->id;
        }

        // Gán máy thực hiện mặc định (Ưu tiên Cache -> Nếu chỉ có 1 máy)
        if ($cachedMachineId && collect($this->machines)->contains('id', $cachedMachineId)) {
            $this->selectedMachineId = $cachedMachineId;
        } elseif (count($this->machines) === 1) {
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

        $this->manualPrintRequired = null; // Tắt thông báo in tay khi bắt đầu quét mới

        // Tùy chọn: Chặn quét cây đã tráng (Nếu type của bạn Mộc = 1, Tráng = 2)
        // if ($item->type == 2) {
        //     $this->dispatch('alert', ['type' => 'error', 'message' => 'Mã này là Vải Tráng, không thể tráng tiếp!']);
        //     return;
        // }

        if (!collect($this->scannedItems)->contains('id', $item->id)) {
            $this->scannedItems[] = $item;
            $this->usedLengths[$item->id] = 0;
            $this->minWidth = collect($this->scannedItems)->min('width');

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
        $this->minWidth = count($this->scannedItems) > 0 ? collect($this->scannedItems)->min('width') : 0;

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

        // Validate tuỳ Chọn Cắt/Xén
        $sourceWidth = $this->minWidth;

        if ($this->cutMode === 'trim') {
            if (!$this->trimWidth || $this->trimWidth <= 0) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Vui lòng nhập khổ mới (Xén)!']);
                return;
            }
            if ($this->trimWidth > $sourceWidth) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Khổ xén (' . $this->trimWidth . ') không thể lớn hơn khổ cha (' . $sourceWidth . ')!']);
                return;
            }
        } elseif ($this->cutMode === 'split') {
            if (!$this->splitWidth1 || $this->splitWidth1 <= 0 || !$this->splitWidth2 || $this->splitWidth2 <= 0) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Vui lòng nhập đủ 2 khổ mới (Chia đôi)!']);
                return;
            }
            if (($this->splitWidth1 + $this->splitWidth2) > $sourceWidth) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Tổng 2 khổ chia (' . ($this->splitWidth1 + $this->splitWidth2) . ') không thể lớn hơn khổ cha (' . $sourceWidth . ')!']);
                return;
            }
        }
        // kiểm tra chiều dài
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
            $sourceItem = Item::with(['order', 'color', 'specification', 'plasticType'])
                ->find($firstItemData['id']);

            // Lấy khổ nhỏ nhất của các cây mục để tính toán làm cấu trúc cây con
            $sourceWidth = count($this->scannedItems) > 0 ? (float) $this->minWidth : (float) $sourceItem->width;

            // 4. LẤY SẴN CÁC THUỘC TÍNH NỀN có dùng để build mã
            $dynamicProps = ItemProperty::where('is_code', true)->get();

            // Xử lý phân loại sản phẩm dựa trên tuỳ chọn Lami
            $isLamiApplied = floatval($this->lami) > 0;
            $productCode = '';

            if ($isLamiApplied) {
                // CÓ LAMI: Chuyển đổi thành sản phẩm mới (Tráng ghép)
                $selectedProduct = $this->selectedProductId ? Product::find($this->selectedProductId) : null;
                $productCode = $selectedProduct?->code ?? '';
                $targetProductId = $this->selectedProductId;
                $targetType = 2; // 2: Tráng ghép (Sản phẩm mới)
            } else {
                // KHÔNG LAMI: Thành phẩm vẫn là sản phẩm bị tách ra (Mộc / Giữ nguyên loại)
                $targetProductId = $sourceItem->product_id;
                $targetType = $sourceItem->type ?: 1; // Giữ nguyên tính chất gốc
            }

            // Dọn rác JSON
            $cleanProps = $sourceItem->properties ?? [];
            // 5. CHUẨN BỊ THÔNG SỐ KHỔ VÀ TẠO QUY TRÌNH BAO NHIÊU CÂY
            $widthsToCreate = [];
            if ($this->cutMode === 'trim') {
                $widthsToCreate[] = $this->trimWidth;
            } elseif ($this->cutMode === 'split') {
                $widthsToCreate[] = $this->splitWidth1;
                $widthsToCreate[] = $this->splitWidth2;
            } else {
                $widthsToCreate[] = $sourceWidth; // Keep
            }
            $targetTotalWidth = array_sum($widthsToCreate);

            /** @var \App\Models\User $currentUser */
            $currentUser = Auth::user();
            $generatedItems = [];

            // Duyệt tạo Item tuỳ số lượng (1 cây Keep/Trim, 2 cây Split)
            foreach ($widthsToCreate as $index => $targetWidth) {
                // TẠO MÃ THEO CHÍNH XÁC KHỔ CỦA CÂY THÀNH PHẨM (targetWidth)
                $baseParts = array_filter([
                    $sourceItem->order->code ?? '',
                    $sourceItem->color->code ?? '',
                    $sourceItem->specification->code ?? '',
                    intval($targetWidth) ?: '',
                    $sourceItem->plasticType->code ?? ''
                ]);

                foreach ($dynamicProps as $prop) {
                    $val = $sourceItem->properties[$prop->code] ?? null;
                    if ($val !== null && $val !== '') {
                        $part = ($prop->code_usage == 1) ? $prop->code : '';
                        $part .= $val . ($prop->unit ?? '');
                        $baseParts[] = trim($part);
                    }
                }

                $baseParts[] = intval($this->newLength);
                $baseString = implode(' ', $baseParts);

                // Thêm productCode trước nextNo nhưng KHÔNG có khoảng trắng ở giữa
                $suffixCode = ($isLamiApplied && $productCode) ? (' ' . $productCode) : ' ';
                $countExisting = Item::where('code', 'LIKE', $baseString . $suffixCode . '%')->count();
                $nextNo = str_pad($countExisting + 1, 3, '0', STR_PAD_LEFT);
                $finalCode = $baseString . $suffixCode . $nextNo;

                $coatedItem = Item::create([
                    'code' => trim($finalCode),
                    'status' => 1,
                    'type' => $targetType,
                    'original_length' => $this->newLength,
                    'length' => $this->newLength,
                    'created_by' => Auth::id(),
                    'order_id'         => $sourceItem->order_id,
                    'product_id'       => $targetProductId,
                    'color_id'         => $sourceItem->color_id,
                    'specification_id' => $sourceItem->specification_id,
                    'width_original'   => $targetWidth,
                    'width'            => $targetWidth,
                    'gsm'              => $sourceItem->gsm,
                    'lami'             => $this->lami !== '' ? $this->lami : null,
                    'plastic_type_id'  => $sourceItem->plastic_type_id,
                    'properties'       => $cleanProps,
                    'department_id'    => $currentUser->department_id,
                    'machine_id'       => $this->selectedMachineId ?: null,
                ]);

                $generatedItems[] = $coatedItem;

                // 6. CẬP NHẬT CÂY MỘC CŨ VÀ GHI PHẢ HỆ CHO *TỪNG CÂY TRÁNG MỚI*
                // Lưu ý: Chỉ trừ hao hụt lần đầu tiên ở cuộn đầu, tránh trừ 2 lần nếu split!
                foreach ($this->scannedItems as $oldItemData) {
                    $oldItem = Item::find($oldItemData['id']);
                    $used = (float) $this->usedLengths[$oldItem->id];

                    // Kênh Pivot lưu liên kết (Tráng ghép hoặc Cắt khổ tùy theo có Lami hay không)
                    $coatedItem->parents()->attach($oldItem->id, [
                        'action_type' => $isLamiApplied ? ActionType::COATING->value : ActionType::CUTTING->value,
                        'used_length' => $used, // Đặt đủ số mét cho tất cả các cuộn con
                        'user_id' => Auth::id(),
                        'created_at' => now(),
                    ]);

                    // Chỉ update trừ mét kho ở vòng lập đầu tiên của mảng cut (để không bị trừ 2 lần tồn kho nếu Split 2 cuộn)
                    if ($index === 0) {
                        $remainingLength = $oldItem->length - $used;
                        $oldItem->update([
                            'length' => $remainingLength > 0 ? $remainingLength : 0,
                        ]);
                    }
                }
            } // Hết xử lý cây tráng

            // 7. XỬ LÝ THU HỒI BIÊN DƯ NẾU BẬT (CHỈ SINH 1 CÂY DUY NHẤT LÀ MỘC TRỪ KHO CỦA CHA ĐÚNG BẰNG used)
            if ($this->recoverEdgeTrim) {
                foreach ($this->scannedItems as $oldItemData) {
                    $oldItem = Item::with(['order', 'color', 'specification', 'plasticType'])->find($oldItemData['id']);
                    $diffWidth = (float) $oldItem->width - $targetTotalWidth;
                    $used = (float) $this->usedLengths[$oldItem->id];

                    if ($diffWidth > 0 && $used > 0) {
                        // 1. Build Base Code cho cây mộc thu hồi
                        $basePartsRecover = array_filter([
                            $oldItem->order->code ?? '',
                            $oldItem->color->code ?? '',
                            $oldItem->specification->code ?? '',
                            intval($diffWidth) ?: '',
                            $oldItem->plasticType->code ?? ''
                        ]);

                        // Ghép tiếp thuộc tính động (nếu có, giữ nguyên của cha)
                        foreach ($dynamicProps as $prop) {
                            $val = $oldItem->properties[$prop->code] ?? null;
                            if ($val !== null && $val !== '') {
                                $part = ($prop->code_usage == 1) ? $prop->code : '';
                                $part .= $val . ($prop->unit ?? '');
                                $basePartsRecover[] = trim($part);
                            }
                        }

                        $basePartsRecover[] = intval($used); // Gắn chiều dài vào mã gốc
                        $baseStringRec = implode(' ', $basePartsRecover);

                        $suffixCodeRec = ($isLamiApplied && $productCode) ? (' ' . $productCode) : ' ';
                        $countExistingRec = Item::where('code', 'LIKE', $baseStringRec . $suffixCodeRec . '%')->count();
                        $nextNoRec = str_pad($countExistingRec + 1, 3, '0', STR_PAD_LEFT);
                        $finalCodeRec = $baseStringRec . $suffixCodeRec . $nextNoRec;

                        $recoveredItem = Item::create([
                            'code' => trim($finalCodeRec),
                            'status' => 1,
                            'type' => $targetType,
                            'original_length' => $used,
                            'length' => $used, // Chiều dài của biên bị lạng ra khớp đúng con số đã tiêu tốn
                            'created_by' => Auth::id(),
                            'order_id'         => $oldItem->order_id,
                            'product_id'       => $targetProductId,
                            'department_id'    => $currentUser->department_id,
                            'machine_id'       => $this->selectedMachineId ?: null,
                            'color_id'         => $oldItem->color_id,
                            'specification_id' => $oldItem->specification_id,
                            'plastic_type_id'  => $oldItem->plastic_type_id,
                            'width_original'   => $diffWidth, // Khổ phần lỡ thu hồi
                            'width'            => $diffWidth,
                            'lami'             => $this->lami !== '' ? $this->lami : null,
                            'notes'            => 'Thu hồi biên dư từ cuộn ' . $oldItem->code,
                            'properties'       => $oldItem->properties,
                        ]);

                        $generatedItems[] = $recoveredItem; // Đẩy vào array lấy Lệnh IN luôn

                        // Liên kết cha con - Kiểu cắt dọc (hoặc tráng nếu áp dụng Lami)
                        $recoveredItem->parents()->attach($oldItem->id, [
                            'action_type' => $isLamiApplied ? ActionType::COATING->value : ActionType::CUTTING->value,
                            'used_length' => $used,
                            'user_id' => Auth::id(),
                            'created_at' => now(),
                        ]);
                    }
                }
            }

            // 8. HOÀN TẤT
            DB::commit();

            // Lưu cài đặt vào bộ nhớ đệm cho lần thao tác tiếp theo
            cache()->forever('printer_mac_' . Auth::id(), $this->printerMac);
            cache()->forever('selected_product_id_' . Auth::id(), $this->selectedProductId);
            cache()->forever('selected_machine_id_' . Auth::id(), $this->selectedMachineId);

            // 🌟 BẮN LỆNH IN QUA WEBSOCKET CHO TOÀN BỘ DANH SÁCH GENERATED
            $msgCodes = [];
            foreach ($generatedItems as $gItem) {
                $msgCodes[] = $gItem->code;
                if (!empty($this->printerMac)) {
                    try {
                        $printJob = PrintJob::create([
                            'item_id' => $gItem->id,
                            'printer_mac' => $this->printerMac,
                            'user_id' => Auth::id(),
                            'status' => PrintJob::STATUS_PENDING
                        ]);

                        $station = PrintStation::where('code', $this->printerMac)->first();

                        if ($station && $station->client_type === 'app') {
                            $printData = [
                                'Path' => $station->template_name,
                                'Data' => [
                                    ['Name' => 'MaSP', 'Value' => $gItem->code],
                                    ['Name' => 'TenSP', 'Value' => $gItem->product->name ?? ''],
                                ],
                                'JobId' => $printJob->id
                            ];
                            broadcast(new PrintLabelAppEvent($station->station_token, $printData));
                        } else {
                            broadcast(new PrintLabelRequested($gItem, $this->printerMac, $printJob->id));
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Lỗi broadcast gửi lệnh in: ' . $e->getMessage());
                    }
                }
            }

            $codesString = implode(', ', $msgCodes);

            if (!empty($this->printerMac)) {
                $successMsg = 'Đã tráng xong và gửi xử lý in! Mã tem: ' . $codesString;
                $this->setManualPrintState(
                    'success',
                    'Đã tráng xong và chuyển in',
                    'Đã xử lý in thành công! Vui lòng kiểm tra máy in.',
                    $codesString
                );
            } else {
                $successMsg = 'Đã tráng xong (Chưa trạm in)! Mã tem: ' . $codesString;
                $this->setManualPrintState(
                    'warning',
                    'Tráng xong nhưng chưa gửi IN',
                    'Chưa có máy in, hãy gọi lệnh in lại trên màn hình hoặc báo quản lý.',
                    $codesString
                );
            }

            $this->resetForm();
            $this->dispatch('alert', ['type' => 'success', 'message' => $successMsg]);
            // $this->dispatch('manual-print-alert', [
            //     'code' => $finalCode,
            //     'length' => (float) $this->newLength,
            //     'time' => now()->format('d/m/Y H:i:s'),
            // ]);
        } catch (\Throwable $e) {
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
        $this->cutMode = 'keep';
        $this->trimWidth = '';
        $this->splitWidth1 = '';
        $this->splitWidth2 = '';
        $this->lami = 1;
        $this->minWidth = 0;
    }

    public function clearManualPrint()
    {
        $this->manualPrintRequired = null;
    }

    private function setManualPrintState($type, $header, $content, $code)
    {
        $icons = [
            'success' => 'fa-solid fa-circle-check text-success',
            'error'   => 'fa-solid fa-circle-xmark text-danger',
            'warning' => 'fa-solid fa-triangle-exclamation text-warning',
        ];

        $this->manualPrintRequired = [
            'type'    => $type,
            'icon'    => $icons[$type] ?? $icons['warning'],
            'header'  => $header,
            'content' => $content,
            'code'    => $code,
            'length'  => (float) $this->newLength,
            'time'    => now()->format('d/m/Y H:i:s'),
        ];
    }

    public function reprintJob($jobId)
    {
        $job = PrintJob::with('item')->find($jobId);
        if ($job && $job->item) {
            $mac = $this->printerMac ?: $job->printer_mac;
            $job->update([
                'status' => PrintJob::STATUS_PENDING,
                'printer_mac' => $mac,
                'user_id' => Auth::id()
            ]);

            $station = PrintStation::where('code', $mac)->first();

            if ($station && $station->client_type === 'app') {
                $printData = [
                    'Path' => $station->template_name,
                    'Data' => [
                        ['Name' => 'MaSP', 'Value' => $job->item->code],
                        ['Name' => 'TenSP', 'Value' => $job->item->product->name],
                    ],
                    'JobId' => $job->id
                ];
                broadcast(new PrintLabelAppEvent($station->station_token, $printData));
            } else {
                broadcast(new PrintLabelRequested($job->item, $mac, $job->id));
            }

            $this->dispatch('alert', ['type' => 'success', 'message' => 'Đã gửi lại lệnh in cho mã: ' . $job->item->code]);
        } else {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Không tìm thấy công việc in.']);
        }
    }

    public function render()
    {
        $recentPrintJobs = PrintJob::with('item')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('livewire.production.coating-confirmation', [
            'recentPrintJobs' => $recentPrintJobs
        ]);
    }
}
