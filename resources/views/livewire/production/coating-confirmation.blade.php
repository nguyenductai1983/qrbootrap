<div class="position-relative">
    <!-- OVERLAY LOADING -->
    <div wire:loading.flex wire:target="confirmCoating,addScannedItem,removeItem"
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: transparent;">
        <div class="spinner-border text-success" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-success">Đang xử lý, vui lòng đợi...</h4>
    </div>
    <div class="row g-3">
        {{-- KHU VỰC QUÉT MÃ VẠCH / CAMERA --}}
        <div class="col-md-5">
            <label class="small text-muted fw-bold" for="selectedProductId">Chọn Thành phẩm</label>
            <select wire:model="selectedProductId" id="selectedProductId"
                class="form-select form-select-sm border-secondary fw-bold text-success mb-2">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}
                    </option>
                @endforeach
            </select>
            <label class="small text-muted fw-bold" for="selectedMachineId">
                <i class="fa-solid fa-gears me-1"></i>Chọn Máy Thực Hiện
            </label>
            <select wire:model="selectedMachineId" id="selectedMachineId"
                class="form-select form-select-sm border-primary fw-bold text-primary mb-2">
                <option value="">-- Không chọn / Không rõ --</option>
                @foreach ($machines as $machine)
                    <option value="{{ $machine->id }}">
                        [{{ $machine->code }}] {{ $machine->name }}
                    </option>
                @endforeach
            </select>

            <label class="small text-muted fw-bold mt-2" for="printerMac">
                <i class="fa-solid fa-print me-1"></i>Chọn Trạm In
            </label>
            <select wire:model="printerMac" id="printerMac"
                class="form-select form-select-sm border-info fw-bold text-info mb-3">
                @if (count($printStations) === 0)
                    <option value="">-- Không có Trạm In --</option>
                @endif
                @foreach ($printStations as $station)
                    <option value="{{ $station->code }}">[{{ $station->code }}] {{ $station->name }}</option>
                @endforeach
            </select>
            @if (count($machines) === 0)
                <div class="alert alert-warning py-1 px-2 small mb-2">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Bạn chưa được phân công máy nào. Liên hệ Admin.
                </div>
            @endif
            @if (count($printStations) === 0)
                <div class="alert alert-warning py-1 px-2 small mb-2">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Bạn chưa được gán Trạm In. Liên hệ Admin.
                </div>
            @endif
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fa-solid fa-barcode"></i> Quét Mã Vải
                </div>
                <div class="card-body text-center">
                    <x-scanner inputModel="codeInput" onEnter="addScannedItem" onScan="addScannedItem"
                        placeholder="Quét hoặc nhập mã Vải..." buttonText="Xác nhận" />
                </div>
            </div>

        </div>

        {{-- KHU VỰC NHẬP SỐ LIỆU & XÁC NHẬN TRÁNG --}}
        <div class="col-md-7" x-data="{
            mode: @entangle('cutMode'),
            minW: @entangle('minWidth'),
            trimWidth: @entangle('trimWidth'),
            split1: @entangle('splitWidth1'),
            split2: @entangle('splitWidth2'),
            validateTrim() {
                let t = parseFloat(this.trimWidth) || 0;
                let m = parseFloat(this.minW) || 0;
                if (t > m) {
                    this.trimWidth = m;
                }
            },
            validateSplit(fromMinWChange = false) {
                let t1 = parseFloat(this.split1) || 0;
                let t2 = parseFloat(this.split2) || 0;
                let m = parseFloat(this.minW) || 0;
        
                if ((t1 + t2) > m) {
                    if (fromMinWChange) {
                        if (t1 === t2) {
                            this.split1 = m / 2;
                            this.split2 = m / 2;
                        } else {
                            let ratio = t1 / (t1 + t2);
                            this.split1 = parseFloat((m * ratio).toFixed(1));
                            this.split2 = parseFloat((m - this.split1).toFixed(1));
                        }
                    } else {
                        if (t1 > m) {
                            this.split1 = m;
                            this.split2 = 0;
                        } else {
                            this.split2 = parseFloat((m - t1).toFixed(1));
                        }
                    }
                }
            },
            init() {
                this.$watch('minW', () => {
                    this.validateTrim();
                    this.validateSplit(true);
                });
                this.$watch('mode', (val) => {
                    let m = parseFloat(this.minW) || 0;
                    if (val === 'trim' && !this.trimWidth) {
                        this.trimWidth = m;
                    }
                    if (val === 'split' && (!this.split1 || this.split1 == 0)) {
                        this.split1 = m / 2;
                        this.split2 = m / 2;
                    }
                    this.validateTrim();
                    this.validateSplit(false);
                });
            }
        }">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fa-solid fa-layer-group me-2"></i> Khai Báo Thành Phẩm Tráng
                </div>
                <div class="card-body">

                    @if (count($scannedItems) == 0)
                        <div
                            class="alert alert-light border border-secondary border-dashed text-center text-muted py-5">
                            <i class="fa-solid fa-box-open fa-3x mb-3 text-light-subtle"></i>
                            <br>Chưa có cây vải Mộc nào được quét.
                        </div>
                    @else
                        <h6 class="fw-bold text-secondary text-uppercase mb-3"><i
                                class="fa-solid fa-boxes-stacked me-1"></i>Đầu Vào:</h6>
                        <ul class="list-group mb-4 shadow-sm">
                            @foreach ($scannedItems as $index => $item)
                                <li class="list-group-item border-start border-4 border-primary">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-primary fs-5">{{ $item['code'] }}</span>
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa cây vải này"
                                            wire:click="removeItem({{ $index }})">
                                            <i class="fa-solid fa-times"></i> Xóa
                                        </button>
                                    </div>
                                    <div class="row align-items-center p-2 rounded">
                                        <div class="col-4 small text-muted">
                                            Tồn: <b class="fs-6">{{ (float) $item['length'] }} m</b>
                                        </div>
                                        <div class="col-4 small text-muted text-center">
                                            GSM: <b class="fs-6 text-primary">{{ (float) $item['gsm'] }}</b>
                                        </div>
                                        <div class="col-4 small text-muted text-end">
                                            Khổ: <b class="fs-6 text-primary">{{ (float) $item['width'] }}</b>
                                        </div>
                                    </div>
                                    <div class="row align-items-center p-2 rounded">
                                        <div class="col-12 col-md-12">
                                            <label class="form-label small text-muted fw-bold">Dùng (m):</label>
                                            <div class="input-group">
                                                <input type="number" step="0.1" max="{{ $item['length'] }}"
                                                    data-item-id="{{ $item['id'] }}"
                                                    wire:model="usedLengths.{{ $item['id'] }}"
                                                    class="form-control form-control-lg text-end fw-bold input-used-length fs-4"
                                                    oninput="calculateFromUsed()" placeholder="0.0">
                                                <button class="btn btn-sm btn-outline-success" type="button"
                                                    onclick="setUsedLength({{ $item['id'] }}, {{ $item['length'] }} ,50)">Dùng
                                                    50%
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" type="button"
                                                    onclick="setUsedLength({{ $item['id'] }}, {{ $item['length'] }},100)">Dùng
                                                    hết</button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <label class="small text-muted fw-bold" for="selectedOrderId">Chọn Đơn Hàng </label>
                        <select wire:model="selectedOrderId" id="selectedOrderId"
                            class="form-select form-select-sm border-warning fw-bold text-dark mb-2">
                            @if (empty($availableOrders))
                                <option value="">-- Chọn Đơn hàng --</option>
                            @else
                                @foreach ($availableOrders as $order)
                                    <option value="{{ $order->id }}">
                                        {{ $order->code }}
                                        @if ($order->production_order_code)
                                            [{{ $order->production_order_code }}]
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <div class="d-flex justify-content-between align-items-end mb-3 mt-4">
                            <h6 class="fw-bold text-success text-uppercase mb-0"><i
                                    class="fa-solid fa-check-double me-1"></i> Thành phẩm đầu ra:</h6>
                            <span class="badge text-secondary border shadow-sm" title="Tỉ lệ: Tổng mộc / Thành phẩm">
                                <i class="fa-solid fa-robot text-info"></i> Tỉ lệ hao hụt: <span id="ratio-display"
                                    class="text-primary fw-bold">{{ number_format($coatingRatio ?? 1.07, 3) }}</span>
                            </span>
                        </div>

                        @php
                            $minW = $minWidth > 0 ? $minWidth : 0;
                            $mismatchedItems = collect($scannedItems)->filter(function ($item) use ($minW) {
                                return (float) $item['width'] > $minW;
                            });
                        @endphp
                        @if ($mismatchedItems->count() > 0)
                            <div class="alert alert-warning shadow-sm border border-warning mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fa-solid fa-scissors fa-2x me-3 text-warning"></i>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Phát hiện lệch khổ Mộc!</h6>
                                        <p class="mb-0 small text-muted">Hệ thống ghi nhận có cuộn Mộc lớn hơn khổ tối
                                            thiểu ({{ $minW }}). Trong đó:</p>
                                    </div>
                                </div>
                                <ul class="mb-2">
                                    @foreach ($mismatchedItems as $mist)
                                        <li class="small"><b class="text-danger">{{ $mist['code'] }}</b> tước biên
                                            dư ra <b>{{ (float) $mist['width'] - $minW }}</b> mm.</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row g-2 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Dài tráng TT thu được:</label>
                                <div class="input-group shadow-sm">
                                    <input type="number" step="0.1" wire:model="newLength"
                                        id="input-new-length" oninput="calculateFromNew(this.value)"
                                        class="form-control form-control text-end fw-bold text-success fs-4"
                                        placeholder="0.0">
                                    <span class="input-group-text fw-bold fs-5 text-success">mét</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Tổng GSM Thành phẩm (Mộc +
                                    Lami):</label>
                                <div class="input-group shadow-sm">
                                    <input type="number" step="0.1" wire:model="gsmlami"
                                        class="form-control form-control text-end fw-bold text-primary fs-4"
                                        placeholder="0.0">
                                    <span class="input-group-text fw-bold fs-5 text-primary">g/m²</span>
                                </div>
                                <div class="form-text text-danger small"><i class="fa-solid fa-circle-exclamation"></i> Bắt buộc nhập. Nhập 0 nếu không tráng ghép.
                                </div>
                            </div>
                        </div>

                        <!-- Chế độ Khổ Vải -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <label class="form-label text-muted fw-bold mb-3"><i
                                        class="fa-solid fa-scissors me-1"></i> Tùy chọn xử lý Khổ Màng</label>

                                <div class="d-flex flex-column gap-2 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" x-model="mode" value="keep"
                                            id="modeKeep">
                                        <label class="form-check-label fw-bold" for="modeKeep">
                                            Giữ nguyên khổ
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" x-model="mode" value="trim"
                                            id="modeTrim">
                                        <label class="form-check-label fw-bold" for="modeTrim">
                                            Xén biên (Nhập khổ mới)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" x-model="mode" value="split"
                                            id="modeSplit">
                                        <label class="form-check-label fw-bold" for="modeSplit">
                                            Chia đôi (Tạo 2 cuộn mới)
                                        </label>
                                    </div>
                                </div>

                                <!-- Form thay đổi khổ -->
                                <div x-show="mode === 'trim'" x-transition
                                    class="mt-3 p-3 bg-white border border-warning rounded" style="display: none;">
                                    <label class="form-label small fw-bold text-warning">Khổ sau xén (Cây mới)</label>
                                    <input type="number" step="0.1" x-model.lazy="trimWidth"
                                        @input="validateTrim" class="form-control fw-bold" placeholder="VD: 1500.5">
                                </div>
                                <div x-show="mode === 'split'" x-transition
                                    class="mt-3 p-3 bg-white border border-info rounded row g-2"
                                    style="display: none;">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-info">Khổ cây 1</label>
                                        <input type="number" step="0.1" x-model.lazy="split1"
                                            @input="validateSplit" class="form-control fw-bold" placeholder="0.0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-info">Khổ cây 2</label>
                                        <input type="number" step="0.1" x-model.lazy="split2"
                                            @input="validateSplit" class="form-control fw-bold" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="form-check form-switch mt-3 pt-3 border-top border-secondary-subtle">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        wire:model="recoverEdgeTrim" id="switchRecover">
                                    <label class="form-check-label fw-bold text-dark" style="cursor: pointer;"
                                        for="switchRecover">Tự động thu hồi phần biên dư (Sinh mã Mộc mới cất kho cho
                                        dải dư khi xén / lệch khổ)</label>
                                </div>
                            </div>
                        </div>

                        <button wire:click="confirmCoating" class="btn btn-success btn-lg w-100 fw-bold shadow"
                            wire:loading.attr="disabled" wire:target="confirmCoating">
                            <span wire:loading.remove wire:target="confirmCoating">
                                <i class="fa-solid fa-print me-2"></i> XÁC NHẬN TẠO MÃ &amp; IN TEM
                            </span>
                            <span wire:loading wire:target="confirmCoating">
                                <span class="spinner-border spinner-border-sm me-2"></span> Đang tạo mã và in tem...
                            </span>
                        </button>
                    @endif
                    {{-- bắt đầu đoạn thông báo --}}
                    @if ($manualPrintRequired)
                        @php
                            $type = $manualPrintRequired['type'] ?? 'warning';
                            $borderClass =
                                $type === 'success'
                                    ? 'border-success'
                                    : ($type === 'error'
                                        ? 'border-danger'
                                        : 'border-warning');
                            $bgClass = $type === 'success' ? 'LightGreen' : ($type === 'error' ? 'Salmon' : 'Tomato');
                            $iconClass =
                                $manualPrintRequired['icon'] ?? 'fa-solid fa-triangle-exclamation text-warning';
                        @endphp
                        <div class="alert shadow-sm border-2 {{ $borderClass }} position-relative"
                            style="background-color:{{ $bgClass }}">
                            <button wire:click="clearManualPrint" class="btn-close position-absolute top-0 end-0 m-2"
                                aria-label="Close"></button>
                            <div class="text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <i class="{{ $iconClass }} fa-3x"></i>
                                    <h4 class="fw-bold text-uppercase">{{ $manualPrintRequired['header'] }}
                                    </h4>
                                </div>
                                <p>
                                    {{ $manualPrintRequired['content'] }}
                                </p>
                                <div class="d-inline-block border border-primary border-2 rounded shadow-sm">
                                    <div class="fs-1 fw-bold text-primary px-3">{{ $manualPrintRequired['code'] }}
                                    </div>
                                    <hr class="border-primary opacity-25">
                                    <div class="row small fw-bold">
                                        <div class="col-auto text-end border-end"><i
                                                class="fa-solid fa-ruler-horizontal"></i>
                                            {{ $manualPrintRequired['length'] }}m</div>
                                        <div class="col-auto text-start"><i class="fa-solid fa-clock"></i>
                                            {{ $manualPrintRequired['time'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- kết thúc đoạn thông báo --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Lịch sử in gần đây --}}
    <div wire:poll.30s class="mt-3">
        @if (isset($recentPrintJobs) && count($recentPrintJobs) > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử in gần đây
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($recentPrintJobs as $job)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-primary">{{ $job->item->code }}</div>
                                <div class="small text-muted">{{ $job->created_at->format('H:i d/m') }} -
                                    {{ $job->printer_mac }}</div>
                                @if ($job->status == \App\Models\PrintJob::STATUS_SUCCESS)
                                    <span class="badge bg-success rounded-pill"><i class="fa-solid fa-check"></i> Đã
                                        in</span>
                                @elseif($job->status == \App\Models\PrintJob::STATUS_PRINTING)
                                    <span class="badge bg-info rounded-pill"><i
                                            class="fa-solid fa-spinner fa-spin"></i> Đang in</span>
                                @elseif($job->status == \App\Models\PrintJob::STATUS_FAILED)
                                    <span class="badge bg-danger rounded-pill"><i
                                            class="fa-solid fa-triangle-exclamation"></i> Lỗi</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill"><i
                                            class="fa-solid fa-clock"></i> Đang chờ</span>
                                @endif
                            </div>
                            <button wire:click="reprintJob({{ $job->id }})"
                                class="btn btn-sm btn-warning fw-bold shadow-sm" title="In lại mã này">
                                <i class="fa-solid fa-print"></i> In Lại
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- SCRIPT: TÍNH TOÁN NHANH BẰNG JS --}}
    {{-- ========================================== --}}
    <script>
        let currentRatio = {{ $coatingRatio ?? 1.0 }};
        window.getUsedLength = function(itemId) {
            return document.querySelector(`.input-used-length[data-item-id="${itemId}"]`).value;
        }

        window.setUsedLength = function(itemId, maxLength, percentage = 100) {
            let input = document.querySelector(`.input-used-length[data-item-id="${itemId}"]`);
            if (input) {
                let calculatedLength = (maxLength * percentage / 100).toFixed(1);
                input.value = calculatedLength;
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                if (typeof window.calculateFromUsed === 'function') {
                    window.calculateFromUsed();
                }
            }
        };

        window.calculateFromUsed = function() {
            let totalUsed = 0;
            document.querySelectorAll('.input-used-length').forEach(function(input) {
                let val = parseFloat(input.value);
                if (!isNaN(val)) totalUsed += val;
            });

            if (totalUsed > 0 && currentRatio > 0) {
                let newLen = (totalUsed / currentRatio).toFixed(1);
                let newLenInput = document.getElementById('input-new-length');
                newLenInput.value = newLen;
                newLenInput.dispatchEvent(new Event('input'));
            } else if (totalUsed === 0) {
                let newLenInput = document.getElementById('input-new-length');
                newLenInput.value = '';
                newLenInput.dispatchEvent(new Event('input'));
            }
        };

        window.calculateFromNew = function(newLenValue) {
            let totalUsed = 0;
            document.querySelectorAll('.input-used-length').forEach(function(input) {
                let val = parseFloat(input.value);
                if (!isNaN(val)) totalUsed += val;
            });

            let newLen = parseFloat(newLenValue);
            if (!isNaN(newLen) && newLen > 0 && totalUsed > 0) {
                currentRatio = totalUsed / newLen;
                document.getElementById('ratio-display').innerText = currentRatio.toFixed(3);
            }
        };

        document.addEventListener('livewire:initialized', () => {

            // Lắng nghe tín hiệu khi quét mã / xóa mã để tính toán lại
            Livewire.on('update-calculations', () => {
                setTimeout(() => {
                    if (typeof window.calculateFromUsed === 'function') {
                        window.calculateFromUsed();
                    }
                }, 100);
            });

            //     Livewire.on('manual-print-alert', (event) => {
            //         Swal.fire({
            //             icon: 'warning',
            //             title: 'CHƯA CÓ LỆNH IN!',
            //             html: `
        //     <div style="text-align:center;">
        //         <p>Bạn không chọn trạm in, hãy chụp ảnh màn hình này lại:</p>
        //         <div style="border:2px solid #007bff; padding:15px; background:#f8f9fa;">
        //             <h2 style="color:#007bff; font-weight:bold;">${event[0].code}</h2>
        //             <p style="margin:5px 0;">Dài: <b>${event[0].length}m</b> | Tạo lúc: ${event[0].time}</p>
        //         </div>
        //     </div>
        // `,
            //             confirmButtonText: 'Đã chụp',
            //             allowOutsideClick: false // Bắt buộc phải bấm OK mới được tắt
            //         });
            //     });


            Livewire.on('alert', (event) => {
                Swal.fire({
                    icon: event[0].type ?? 'info',
                    title: 'Thông báo',
                    text: event[0].message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: true,
                    timer: 5000,
                    timerProgressBar: true,
                });
            });
        });
    </script>
</div>
