<div class="container-fluid py-3 position-relative">
    {{-- OVERLAY LOADING --}}
    <div wire:loading.flex
        class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center"
        style="background: rgba(255,255,255,0.7); z-index: 999;">
        <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 fw-bold text-warning">Đang xử lý...</h5>
    </div>

    {{-- HEADER --}}
    <div class="alert alert-warning border-warning border-2 mb-3 py-2 px-3 d-flex align-items-center gap-2">
        <i class="fa-solid fa-recycle fa-lg text-warning"></i>
        <div>
            <div class="fw-bold">Tái nhập Dư (Surplus Entry)</div>
            <div class="small text-muted">Quét cây vải ĐÃ nhập kho để cập nhật lại trọng lượng thực tế và vị trí kho.</div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ===== CỘT TRÁI: CÀI ĐẶT PHIÊN ===== --}}
        <div class="col-lg-4">

            {{-- Vị trí kệ --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header py-2 {{ $currentLocationId ? 'bg-success text-white' : 'bg-light' }}">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    <span class="fw-bold small">Vị trí kho</span>
                    <span class="float-end small opacity-75">(Tùy chọn)</span>
                </div>
                <div class="card-body py-2">
                    @if ($currentLocationId && $currentLocation)
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-success fs-6 me-1">{{ $currentLocation->code }}</span>
                                <span class="small text-muted">{{ $currentLocation->name }}</span>
                            </div>
                            <button wire:click="clearLocation" class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @else
                        <div class="text-muted small text-center py-1">
                            <i class="fa-solid fa-qrcode me-1"></i>
                            Quét mã QR kệ hàng để chọn vị trí
                        </div>
                    @endif
                </div>
            </div>

            {{-- Trọng lượng --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header py-2 bg-primary text-white">
                    <i class="fa-solid fa-weight-scale me-1"></i>
                    <span class="fw-bold small">Trọng lượng <span class="text-warning">*</span></span>
                </div>
                <div class="card-body py-2">

                    {{-- Cân WebSocket --}}
                    @if ($scaleStations->isNotEmpty())
                        <div class="mb-2">
                            <select wire:model.live="selectedScaleCode" class="form-select form-select-sm">
                                <option value="">-- Chọn trạm cân --</option>
                                @foreach ($scaleStations as $station)
                                    <option value="{{ $station->code }}">{{ $station->code }} - {{ $station->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($selectedScaleCode)
                            <div class="text-center mb-2">
                                <div id="scaleWeightValue"
                                    class="scale-weight-display {{ $scaleStable ? 'stable' : 'unstable' }}"
                                    style="font-size: 2rem; font-weight: 700; font-family: monospace; letter-spacing: 2px;
                                           color: {{ $scaleStable ? '#198754' : '#6c757d' }};">
                                    {{ $scaleWeight !== null ? number_format($scaleWeight, 2) : '---' }}
                                    <small style="font-size: 1rem;">kg</small>
                                </div>
                                <div class="small mt-1">
                                    @if ($scaleWeight !== null)
                                        <span class="badge {{ $scaleStable ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $scaleStable ? '✓ Ổn định' : '⟳ Đang cân...' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Chờ tín hiệu cân...</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Nhập tay --}}
                    <div class="input-group input-group-sm mt-1">
                        <span class="input-group-text"><i class="fa-solid fa-keyboard"></i></span>
                        <input type="number" step="0.01" min="0"
                            wire:model.live="manualWeight"
                            class="form-control fw-bold text-center {{ $effectiveWeight ? 'border-success' : '' }}"
                            placeholder="Nhập tay (kg)...">
                        <span class="input-group-text bg-light">kg</span>
                    </div>
                    @if ($effectiveWeight)
                        <div class="text-success small mt-1 text-center">
                            <i class="fa-solid fa-circle-check me-1"></i>
                            Sẽ dùng: <strong>{{ $effectiveWeight }}kg</strong>
                            @if ($scaleWeight > 0)
                                <span class="text-muted">(từ cân)</span>
                            @else
                                <span class="text-muted">(nhập tay)</span>
                            @endif
                        </div>
                    @else
                        <div class="text-danger small mt-1 text-center">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Bắt buộc nhập trọng lượng trước khi quét
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ghi chú lý do --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header py-2 bg-light">
                    <i class="fa-solid fa-comment me-1"></i>
                    <span class="fw-bold small">Lý do tái nhập</span>
                    <span class="float-end small opacity-75">(Tùy chọn)</span>
                </div>
                <div class="card-body py-2">
                    <input type="text" wire:model="warehouseNote"
                        class="form-control form-control-sm"
                        placeholder="VD: Trả dư từ chuyền 3, cây bị hỏng đầu...">
                </div>
            </div>

            {{-- Bộ đếm phiên --}}
            @if (!empty($sessionItems))
                <div class="card border-0 shadow-sm">
                    <div class="card-header py-2 bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="small fw-bold">
                            <i class="fa-solid fa-list me-1"></i>
                            Đã tái nhập: <span class="badge bg-warning text-dark">{{ count($sessionItems) }}</span>
                        </span>
                        <button wire:click="clearSession" wire:confirm="Xóa lịch sử phiên?"
                            class="btn btn-sm btn-outline-light py-0 px-2">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                            @foreach ($sessionItems as $si)
                                <li class="list-group-item py-2 px-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold text-primary small">{{ $si['code'] }}</span>
                                        <span class="text-muted small">{{ $si['time'] }}</span>
                                    </div>
                                    <div class="d-flex gap-2 small text-muted">
                                        @if ($si['location_code'])
                                            <span><i class="fa-solid fa-location-dot me-1"></i>{{ $si['location_code'] }}</span>
                                        @endif
                                        @if ($si['weight'])
                                            <span><i class="fa-solid fa-weight-scale me-1"></i>{{ $si['weight'] }}kg</span>
                                        @endif
                                    </div>
                                    @if ($si['note'])
                                        <div class="small text-warning fst-italic">"{{ $si['note'] }}"</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        {{-- ===== CỘT PHẢI: QUÉT MÃ ===== --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 bg-warning text-dark fw-bold">
                    <i class="fa-solid fa-barcode me-1"></i>
                    Quét mã cây vải cần tái nhập dư
                </div>
                <div class="card-body">
                    <x-scanner
                        inputModel="scannedCodeInput"
                        onEnter="handleKeyInput"
                        onScan="handleScan"
                        placeholder="Quét mã cây vải hoặc mã kệ..."
                        buttonText="Tái nhập" />

                    {{-- Kết quả quét --}}
                    @if ($message)
                        <div class="mt-3 alert alert-{{ $scanStatus === 'success' ? 'success' : ($scanStatus === 'location' ? 'info' : ($scanStatus === 'warning' ? 'warning' : 'danger')) }} border-2">
                            <div class="fw-bold">
                                @if ($scanStatus === 'success')
                                    <i class="fa-solid fa-circle-check me-1"></i>
                                @elseif ($scanStatus === 'location')
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                @elseif ($scanStatus === 'warning')
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                @else
                                    <i class="fa-solid fa-circle-xmark me-1"></i>
                                @endif
                                {{ $message }}
                            </div>
                            @if ($scanStatus === 'error' || $scanStatus === 'warning')
                                <button wire:click="resetScan" class="btn btn-sm btn-outline-dark mt-2">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Quét lại
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Chi tiết cây vải vừa xử lý --}}
                    @if ($itemInfo)
                        <div class="card border-success border-2 mt-3">
                            <div class="card-header bg-success text-white py-2 small fw-bold">
                                <i class="fa-solid fa-recycle me-1"></i>
                                {{ $itemInfo->code }}
                            </div>
                            <div class="card-body py-2">
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="text-muted small">Sản phẩm</div>
                                        <div class="fw-bold">{{ optional($itemInfo->product)->code ?? '-' }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Màu</div>
                                        <div class="fw-bold">{{ optional($itemInfo->color)->code ?? '-' }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Trọng lượng</div>
                                        <div class="fw-bold text-success">{{ $itemInfo->weight ?? '-' }}kg</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Đơn hàng</div>
                                        <div class="fw-bold">{{ optional($itemInfo->order)->code ?? '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Vị trí kho</div>
                                        <div class="fw-bold text-primary">{{ optional($itemInfo->location)->code ?? 'Chưa xác định' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Nhật ký di chuyển --}}
                            @if ($itemInfo->movements && $itemInfo->movements->isNotEmpty())
                                <div class="card-footer p-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="fa-solid fa-clock-rotate-left text-secondary me-2 small"></i>
                                        <span class="fw-bold small text-secondary">Nhật ký cây vải</span>
                                    </div>
                                    <div class="table-responsive rounded border" style="max-height: 200px; overflow-y: auto;">
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 0.75rem;">
                                            <tbody>
                                                @foreach ($itemInfo->movements->take(10) as $mv)
                                                    <tr class="border-bottom">
                                                        <td class="py-1 ps-2 text-nowrap text-muted" style="width:70px;">
                                                            {{ $mv->created_at->format('d/m H:i') }}
                                                        </td>
                                                        <td class="py-1">
                                                            <span class="badge {{ $mv->action_type->badge() }}">
                                                                {{ $mv->action_type->label() }}
                                                            </span>
                                                        </td>
                                                        <td class="py-1 text-muted text-wrap" style="font-size:0.72rem;">
                                                            {{ $mv->note }}
                                                        </td>
                                                        <td class="py-1 text-muted text-nowrap" style="width:60px;">
                                                            {{ optional($mv->user)->username ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/sweetalert2@11.js" type="text/javascript"></script>
<script>
    const audioSuccess = new Audio('/audio/cartoon_boing.ogg');
    const audioError   = new Audio('/audio/beep_short.ogg');

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('play-success-sound', () => {
            audioError.pause();
            audioSuccess.pause();
            audioSuccess.currentTime = 0;
            audioSuccess.play().catch(() => {});
        });
        Livewire.on('play-error-sound', () => {
            audioSuccess.pause();
            audioError.pause();
            audioError.currentTime = 0;
            audioError.play().catch(() => {});
        });
        Livewire.on('play-warning-sound', () => {
            audioSuccess.pause();
            audioError.pause();
            audioError.currentTime = 0;
            audioError.play().catch(() => {});
        });
        Livewire.on('focus-input', () => {
            const el = document.getElementById('scannedCodeInput');
            if (el) el.focus();
        });

        if (typeof window.initWebSocket === 'function') {
            window.initWebSocket();
        }
        initScaleListener();
    });

    let scaleChannel = null;

    function initScaleListener() {
        const scaleCode = @json($selectedScaleCode);
        subscribeScale(scaleCode);
    }

    function subscribeScale(stationCode) {
        if (scaleChannel) {
            window.Echo.leave(scaleChannel);
            scaleChannel = null;
        }
        if (!stationCode || !window.Echo) return;
        scaleChannel = 'scale.' + stationCode;
        window.Echo.channel(scaleChannel)
            .listen('.ScaleWeightUpdated', (e) => {
                const weightEl = document.getElementById('scaleWeightValue');
                if (weightEl) {
                    const w = parseFloat(e.weight).toFixed(2);
                    weightEl.innerHTML = w + ' <small style="font-size:1rem;">kg</small>';
                    weightEl.style.color = e.is_stable ? '#198754' : '#6c757d';
                }
                @this.call('updateScaleWeight', e.weight, e.is_stable);
            });
    }

    document.addEventListener('livewire:navigated', () => {
        initScaleListener();
    });
</script>
