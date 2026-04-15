<div class="container-fluid py-3 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: rgba(255, 255, 255, 0.8);">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status">
        </div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <style>
        .mode-card {
            cursor: pointer;
            transition: all .1s ease;
        }

        /* Nhập Tạm - secondary */
        #mode_temp:checked~* .mode-card,
        #mode_temp:checked+label {
            background-color: var(--bs-secondary) !important;
            color: #fff !important;
            box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .15);
        }

        /* Nhập có vị trí - success */
        #mode_with_loc:checked+label {
            background-color: var(--bs-success) !important;
            color: #fff !important;
            box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .15);
        }

        /* Xác nhận vị trí - info */
        #mode_confirm:checked+label {
            background-color: var(--bs-info) !important;
            color: #000 !important;
            box-shadow: 0 .25rem .5rem rgba(0, 0, 0, .15);
        }

        /* Scale weight display */
        .scale-weight-display {
            font-size: 2.5rem;
            font-weight: 900;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            transition: color 0.3s ease;
        }

        .scale-weight-display.stable {
            color: var(--bs-success);
        }

        .scale-weight-display.unstable {
            color: var(--bs-warning);
        }

        .scale-pulse {
            animation: scalePulse 1.5s ease-in-out infinite;
        }

        @keyframes scalePulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Stepper UI */
        .stepper-wrapper {
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0.5;
        }

        .stepper-item::before {
            position: absolute;
            content: "";
            border-bottom: 2px solid #ccc;
            width: 100%;
            top: 20px;
            left: -50%;
            z-index: 2;
        }

        .stepper-item::after {
            position: absolute;
            content: "";
            border-bottom: 2px solid #ccc;
            width: 100%;
            top: 20px;
            left: 50%;
            z-index: 2;
        }

        .stepper-item .step-counter {
            position: relative;
            z-index: 5;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ccc;
            margin-bottom: 6px;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .stepper-item.active {
            opacity: 1;
        }

        .stepper-item.completed .step-counter {
            background-color: var(--bs-primary);
        }

        .stepper-item.active .step-counter {
            background-color: var(--bs-success);
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.2);
        }

        .stepper-item.completed::after {
            border-bottom-color: var(--bs-primary);
        }

        .stepper-item:first-child::before {
            content: none;
        }

        .stepper-item:last-child::after {
            content: none;
        }

        .step-name {
            font-size: 0.85rem;
            font-weight: bold;
            color: #555;
            text-align: center;
        }

        .stepper-item.active .step-name,
        .stepper-item.completed .step-name {
            color: #000;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            {{-- THANH TRÌNH TỰ (STEPPER) --}}
            <div class="stepper-wrapper">
                <div class="stepper-item {{ $currentStep >= 1 ? 'completed' : '' }} {{ $currentStep == 1 ? 'active' : '' }}"
                    wire:click="goToStep(1)">
                    <div class="step-counter">1</div>
                    <div class="step-name">Cấu Hình</div>
                </div>

                @if ($mode !== 'temp')
                    <div class="stepper-item {{ $currentStep >= 2 ? 'completed' : '' }} {{ $currentStep == 2 ? 'active' : '' }}"
                        wire:click="goToStep(2)">
                        <div class="step-counter">2</div>
                        <div class="step-name">Vị Trí</div>
                    </div>
                @endif

                <div class="stepper-item {{ $currentStep >= 3 ? 'completed' : '' }} {{ $currentStep == 3 ? 'active' : '' }}"
                    wire:click="goToStep(3)">
                    <div class="step-counter">{{ $mode === 'temp' ? 2 : 3 }}</div>
                    <div class="step-name">Quét Mã</div>
                </div>
            </div>

            {{-- ===========================================
                 BƯỚC 1: CẤU HÌNH BAN ĐẦU
                 =========================================== --}}
            @if ($currentStep === 1)
                {{-- CHỌN CHẾ ĐỘ: radio input ẩn, đổi tức thì --}}
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white fw-bold py-2">
                        <i class="fa-solid fa-gear me-2"></i>1. Chọn Hình Thức Nhập
                    </div>
                    <div class="card-body">
                        <div class="mode-selector row g-2">
                            <div class="col-12 col-lg-4">
                                <input type="radio" wire:model.live="mode" value="temp" id="mode_temp"
                                    class="mode-radio d-none">
                                <label for="mode_temp"
                                    class="mode-card card h-100 text-center border-2 border-warning text-warning mb-0">
                                    <div class="card-body py-2 px-0 px-sm-1">
                                        <i class="fa-solid fa-inbox fa-lg d-block mb-1"></i>
                                        <div class="fw-bold" style="font-size:0.8rem">Nhập Tạm</div>
                                        <div class="d-none d-sm-block mt-1" style="font-size:0.75rem; opacity:.8;">Không
                                            cần vị trí</div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-12 col-lg-4">
                                <input type="radio" wire:model.live="mode" value="with_loc" id="mode_with_loc"
                                    class="mode-radio d-none">
                                <label for="mode_with_loc"
                                    class="mode-card card h-100 text-center border-2 border-success text-success mb-0">
                                    <div class="card-body py-2 px-0 px-sm-1">
                                        <i class="fa-solid fa-location-dot fa-lg d-block mb-1"></i>
                                        <div class="fw-bold" style="font-size:0.8rem">Nhập + Vị Trí</div>
                                        <div class="d-none d-sm-block mt-1" style="font-size:0.75rem; opacity:.8;">Quét
                                            kệ → cây vải</div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-12 col-lg-4">
                                <input type="radio" wire:model.live="mode" value="confirm" id="mode_confirm"
                                    class="mode-radio d-none">
                                <label for="mode_confirm"
                                    class="mode-card card h-100 text-center border-2 border-info text-info mb-0">
                                    <div class="card-body py-2 px-0 px-sm-1">
                                        <i class="fa-solid fa-map-pin fa-lg d-block mb-1"></i>
                                        <div class="fw-bold" style="font-size:0.8rem">Xác Nhận Vị Trí</div>
                                        <div class="d-none d-sm-block mt-1" style="font-size:0.75rem; opacity:.8;">
                                            Đổi/gán vị trí</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CHỌN TRẠM CÂN --}}
                @if (count($scaleStations) > 0)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-primary text-white fw-bold py-2">
                            <i class="fa-solid fa-weight-scale me-2"></i>Chọn Trạm Cân (Tùy chọn)
                        </div>
                        <div class="card-body">
                            <select wire:model.live="selectedScaleCode" class="form-select form-select-lg">
                                <option value="">-- Không chọn / Nhập tay --</option>
                                @foreach ($scaleStations as $station)
                                    <option value="{{ $station->code }}">{{ $station->code }} – {{ $station->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <button class="btn btn-primary btn-lg w-100 py-3 fw-bold text-uppercase shadow mt-3"
                    wire:click="nextStep">
                    Tiếp theo <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            @endif

            {{-- ===========================================
                 BƯỚC 2: QUÉT KỆ CỦA VỊ TRÍ
                 =========================================== --}}
            @if ($currentStep === 2)
                <div class="card shadow border-0 mb-3 border-success border-2">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-location-dot me-2"></i>BƯỚC 2: QUÉT MÃ KỆ (VỊ
                            TRÍ)</h5>
                    </div>
                    <div class="card-body">
                        @if ($currentLocation)
                            <div class="alert alert-success text-center mb-4">
                                <h6><i class="fa-solid fa-check-circle me-1"></i>Đã chọn vị trí:</h6>
                                <div class="fs-4 fw-bold">{{ $currentLocation->code }}</div>
                                <div class="text-muted">{{ $currentLocation->name }}</div>
                            </div>
                            <button class="btn btn-warning w-100 fw-bold mb-2" wire:click="changeLocation">
                                <i class="fa-solid fa-repeat me-1"></i>Đổi Vị Trí Khác
                            </button>
                            <button class="btn btn-primary btn-lg w-100 fw-bold shadow" wire:click="nextStep">
                                Tiếp tục Quét Vải <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        @else
                            <div class="text-center text-muted mb-3">
                                Hãy đưa mã QR của kệ hàng vào camera hoặc dùng thanh quét bên dưới.
                            </div>
                            <x-scanner inputModel="scannedCodeInput" onEnter="handleKeyInput" onScan="handleScan"
                                placeholder="Quét mã QR kệ hàng..." buttonText="Xác nhận" />
                        @endif

                        {{-- Hiển thị báo lỗi thao tác --}}
                        @if ($message && in_array($scanStatus, ['error', 'warning']))
                            <div class="alert alert-danger text-center shadow-sm mt-3 mb-0" role="alert">
                                <h6 class="alert-heading fw-bold mb-1"><i
                                        class="fa-solid fa-circle-xmark me-2"></i>{{ $message }}</h6>
                                <button wire:click="resetScan" class="btn btn-sm btn-outline-dark mt-2">
                                    <i class="fa-solid fa-rotate-right me-1"></i>Thử lại
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <button class="btn btn-outline-secondary w-100 py-2 fw-bold" wire:click="prevStep">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại Cấu Hình
                </button>
            @endif


            {{-- ===========================================
                 BƯỚC 3: QUÉT MÃ CÂY VẢI VÀ LẤY CÂN
                 =========================================== --}}
            @if ($currentStep === 3)

                {{-- Banner tóm tắt --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <button class="btn btn-sm btn-outline-secondary px-3" wire:click="prevStep">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </button>
                        <div class="text-end small" style="line-height: 1.3;">
                            <div>
                                <span class="text-muted">Chế độ:</span>
                                <span
                                    class="fw-bold {{ $mode == 'temp' ? 'text-warning' : ($mode == 'with_loc' ? 'text-success' : 'text-info') }}">
                                    {{ $mode == 'temp' ? 'Nhập Tạm' : ($mode == 'with_loc' ? 'Nhập + Vị Trí' : 'Xác Nhận Vị Trí') }}
                                </span>
                            </div>
                            @if ($mode != 'temp')
                                <div class="mt-1">
                                    <span class="text-muted">Vị trí:</span>
                                    @if ($currentLocation)
                                        <span class="badge bg-success ms-1 fs-6">{{ $currentLocation->code }}</span>
                                    @else
                                        <span class="badge bg-danger ms-1">CHƯA CÓ CHỌN KỆ!</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ⚖️ CÂN / TRỌNG LƯỢNG --}}
                <div class="card shadow-sm mb-3 border-primary">
                    <div class="card-header py-2 bg-primary text-white text-center fw-bold text-uppercase">
                        <i class="fa-solid fa-weight-scale me-2"></i>LẤY TRỌNG LƯỢNG KÝ
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-2 align-items-center">
                            {{-- Cột TRÁI: Real-time từ WebSocket (chỉ hiện khi đã chọn trạm cân) --}}
                            @if ($selectedScaleCode)
                                <div class="col-6 text-center border-end">
                                    <div class="small fw-bold text-primary mb-1">
                                        <i class="fa-solid fa-wifi me-1"></i>Trạm {{ $selectedScaleCode }}
                                    </div>
                                    <div class="scale-weight-display {{ $scaleStable ? 'stable' : 'unstable' }}"
                                        style="font-size: 2rem;" id="scaleWeightValue">
                                        @if ($scaleWeight !== null)
                                            {{ number_format($scaleWeight, 2) }} <small class="fs-6">kg</small>
                                        @else
                                            <span class="text-muted scale-pulse">---.--</span>
                                        @endif
                                    </div>
                                    <div class="mt-1">
                                        @if ($scaleWeight !== null && $scaleStable)
                                            <span class="badge bg-success">Ổn định</span>
                                        @elseif($scaleWeight !== null)
                                            <span class="badge bg-warning text-dark">Giao động</span>
                                        @else
                                            <span class="badge bg-secondary">Chờ cân</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                @else
                                    <div class="col-12 py-2">
                            @endif

                            {{-- Cột PHẢI (hoặc đầy màn hình): Nhập tay trọng lượng --}}
                            <label class="form-label small text-muted mb-1 d-block text-center fw-bold">
                                <i class="fa-solid fa-keyboard me-1"></i>
                                {{ $selectedScaleCode ? 'Nhập tay bù trừ' : 'Nhập trọng lượng tay (kg)' }}
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" class="form-control text-center fw-bold fs-4"
                                    wire:model="manualWeight" step="0.01" min="0" placeholder="0.00"
                                    inputmode="decimal">
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        {{-- KHU VỰC QUÉT --}}
        <div class="card shadow border-0 border-primary border-2">
            <div
                class="card-header text-white text-center py-2 {{ $mode === 'temp' ? 'bg-secondary' : ($mode === 'with_loc' ? 'bg-success' : 'bg-info') }}">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-barcode-scan me-2"></i>BƯỚC 3: QUÉT MÃ CÂY VẢI</h5>
            </div>
            <div class="card-body">
                <x-scanner inputModel="scannedCodeInput" onEnter="handleKeyInput" onScan="handleScan"
                    placeholder="Quét tem mã cây vải..." buttonText="Xác nhận" />

                <hr>

                {{-- Hiển thị kết quả --}}
                @if ($message)
                    @php
                        $alertClass = match ($scanStatus) {
                            'success' => 'alert-success',
                            'warning' => 'alert-warning',
                            'location' => 'alert-info',
                            default => 'alert-danger',
                        };
                        $icon = match ($scanStatus) {
                            'success' => 'fa-circle-check',
                            'warning' => 'fa-triangle-exclamation',
                            'location' => 'fa-location-dot',
                            default => 'fa-circle-xmark',
                        };
                    @endphp
                    <div class="alert {{ $alertClass }} text-center shadow-sm" role="alert">
                        <h6 class="alert-heading fw-bold mb-1" style="white-space: pre-line;">
                            <i class="fa-solid {{ $icon }} me-2"></i>{{ $message }}
                        </h6>
                        @if (in_array($scanStatus, ['error', 'warning']))
                            <button wire:click="resetScan" class="btn btn-sm btn-outline-dark mt-2">
                                <i class="fa-solid fa-rotate-right me-1"></i>Quét lại
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Thông tin cây vải vừa quét --}}
                @if ($itemInfo)
                    <div class="table-responsive rounded border p-2 mb-0 mt-3 bg-light">
                        <table class="table table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted py-1" width="30%">Mã:</td>
                                <td class="fw-bold font-monospace text-primary py-1">{{ $itemInfo->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Model:</td>
                                <td class="py-1">{{ optional($itemInfo->product)->code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Màu:</td>
                                <td class="py-1">{{ optional($itemInfo->color)->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Trv.Lượng:</td>
                                <td class="py-1">
                                    @if ($itemInfo->weight)
                                        <span class="fw-bold text-primary">{{ number_format($itemInfo->weight, 2) }}
                                            kg</span>
                                        @if ($itemInfo->weight_original && $itemInfo->weight_original != $itemInfo->weight)
                                            <small class="text-muted ms-1">(Mới:
                                                {{ number_format($itemInfo->weight_original, 2) }}kg)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Chưa cân</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">Vị trí:</td>
                                <td class="py-1">
                                    @if ($itemInfo->location)
                                        <span class="badge bg-success">{{ $itemInfo->location->code }}</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa xác định</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- DANH SÁCH ĐÃ XỬ LÝ TRONG PHIÊN --}}
        @if (!empty($sessionItems))
            <div class="card shadow-sm mt-3">
                <div class="card-header py-2 d-flex justify-content-between align-items-center bg-light">
                    <span class="fw-bold text-success" style="font-size: 0.9rem;">
                        <i class="fa-solid fa-list-check me-1"></i>Lịch sử quét (Gần nhất)
                    </span>
                    <span class="badge bg-success">{{ count($sessionItems) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead>
                                <tr class="table-light">
                                    <th class="ps-2">Tem</th>
                                    <th>Vị trí</th>
                                    <th class="text-end">Kg</th>
                                    <th class="text-end pe-2">Giờ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sessionItems as $idx => $si)
                                    <tr wire:key="si-{{ $idx }}">
                                        <td class="font-monospace fw-bold text-primary ps-2">{{ $si['code'] }}</td>
                                        <td>
                                            @if (!empty($si['location_code']))
                                                <span class="badge bg-success">{{ $si['location_code'] }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if (!empty($si['weight']))
                                                <span class="fw-bold">{{ number_format($si['weight'], 2) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted pe-2">{{ $si['time'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        @endif

    </div>
</div>

<script src="/js/sweetalert2@11.js" type="text/javascript"></script>
<script>
    const audioSuccess = new Audio('/audio/cartoon_boing.ogg');
    const audioError = new Audio('/audio/beep_short.ogg');

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('play-success-sound', () => {
            audioSuccess.play().catch(() => {});
        });
        Livewire.on('play-error-sound', () => {
            audioError.play().catch(() => {});
        });
        Livewire.on('play-warning-sound', () => {
            audioError.play().catch(() => {});
        });
        Livewire.on('focus-input', () => {
            const el = document.getElementById('scannedCodeInput');
            if (el) el.focus();
        });

        Livewire.on('show-toast', (data) => {
            const payload = data[0] || data;
            Swal.fire({
                icon: payload.type,
                title: payload.title,
                text: payload.text,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        });

        // ⚖️ ECHO WEBSOCKET — Lắng nghe trọng lượng từ trạm cân
        initScaleListener();
    });

    let scaleChannel = null;

    function initScaleListener() {
        const scaleCode = @json($selectedScaleCode);
        subscribeScale(scaleCode);
    }

    function subscribeScale(stationCode) {
        // Hủy kênh cũ nếu có
        if (scaleChannel) {
            window.Echo.leave(scaleChannel);
            scaleChannel = null;
        }

        if (!stationCode || !window.Echo) return;

        scaleChannel = 'scale.' + stationCode;

        window.Echo.channel(scaleChannel)
            .listen('.ScaleWeightUpdated', (e) => {
                // Cập nhật hiển thị real-time trên UI
                const weightEl = document.getElementById('scaleWeightValue');
                if (weightEl) {
                    const w = parseFloat(e.weight).toFixed(2);
                    const stableClass = e.is_stable ? 'stable' : 'unstable';
                    weightEl.className = 'scale-weight-display ' + stableClass;
                    weightEl.innerHTML = w + ' <small class="fs-6">kg</small>';
                }

                // Gửi trọng lượng cho Livewire component
                @this.call('updateScaleWeight', e.weight, e.is_stable);
            });
    }

    // Lắng nghe khi user đổi trạm cân qua dropdown (Livewire re-render)
    document.addEventListener('livewire:navigated', () => {
        initScaleListener();
    });

    document.addEventListener('livewire:initialized', () => {
        // Theo dõi thay đổi của dropdown bằng Livewire hook
        Livewire.hook('morph.updated', ({
            el
        }) => {
            if (el.wire && el.wire.get) {
                const newScale = @this.get('selectedScaleCode');
                if (newScale !== scaleChannel?.replace('scale.', '')) {
                    subscribeScale(newScale);
                }
            }
        });
    });
</script>
</div>
