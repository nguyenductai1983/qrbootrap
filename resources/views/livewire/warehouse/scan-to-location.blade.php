<div class="container-fluid py-3 position-relative">
    <!-- OVERLAY LOADING TO BỰ CHỐNG CLICK NHẦM -->
    <div wire:loading.flex class="position-absolute w-100 h-100 top-0 start-0 z-3 flex-column justify-content-center align-items-center" 
         style="background: transparent;">
        <div class="spinner-border text-primary" style="width: 4rem; height: 4rem; border-width: 0.35em;" role="status"></div>
        <h4 class="mt-3 fw-bold text-primary">Đang xử lý, vui lòng đợi...</h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-12">

            {{-- CHỌN CHẾ ĐỘ: radio input ẩn, đổi tức thì --}}
            <div class="mode-selector row g-2 mb-3">

                <div class="col-4">
                    <input type="radio" wire:model.live="mode" value="temp" id="mode_temp" class="mode-radio d-none">
                    <label for="mode_temp"
                        class="mode-card card h-100 text-center border-2 border-warning text-warning mb-0">
                        <div class="card-body py-2 px-1">
                            <i class="fa-solid fa-inbox fa-lg d-block mb-1"></i>
                            <div class="fw-bold small">Nhập Tạm</div>
                            <div class="d-none d-sm-block" style="font-size:0.7rem; opacity:.8;">Không cần vị trí</div>
                        </div>
                    </label>
                </div>

                <div class="col-4">
                    <input type="radio" wire:model.live="mode" value="with_loc" id="mode_with_loc"
                        class="mode-radio d-none">
                    <label for="mode_with_loc"
                        class="mode-card card h-100 text-center border-2 border-success text-success mb-0">
                        <div class="card-body py-2 px-1">
                            <i class="fa-solid fa-location-dot fa-lg d-block mb-1"></i>
                            <div class="fw-bold small">Nhập + Vị Trí</div>
                            <div class="d-none d-sm-block" style="font-size:0.7rem; opacity:.8;">Quét kệ → cây vải</div>
                        </div>
                    </label>
                </div>

                <div class="col-4">
                    <input type="radio" wire:model.live="mode" value="confirm" id="mode_confirm"
                        class="mode-radio d-none">
                    <label for="mode_confirm"
                        class="mode-card card h-100 text-center border-2 border-info text-info mb-0">
                        <div class="card-body py-2 px-1">
                            <i class="fa-solid fa-map-pin fa-lg d-block mb-1"></i>
                            <div class="fw-bold small">Xác Nhận Vị Trí</div>
                            <div class="d-none d-sm-block" style="font-size:0.7rem; opacity:.8;">Cập nhật kệ đã nhập
                            </div>
                        </div>
                    </label>
                </div>
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
            </style>

            {{-- BANNER VỊ TRÍ (chỉ hiện khi mode 2 hoặc 3) --}}
            @if (in_array($mode, ['with_loc', 'confirm']))
                <div class="card shadow-sm mb-3 {{ $currentLocation ? 'border-success' : 'border-secondary' }}">
                    <div class="card-header py-2 {{ $currentLocation ? 'bg-success' : 'bg-secondary' }} text-white">
                        @if ($currentLocation)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">
                                    <i class="fa-solid fa-location-dot me-2"></i>
                                    Vị trí: <span class="badge bg-dark fs-6 ms-1">{{ $currentLocation->code }}</span>
                                    <span class="ms-2">{{ $currentLocation->name }}</span>
                                </span>
                                <button wire:click="changeLocation" class="btn btn-sm btn-outline-light">
                                    <i class="fa-solid fa-repeat me-1"></i>Đổi Vị Trí
                                </button>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>
                                    @if ($mode === 'with_loc')
                                        Hãy quét mã QR của <strong>kệ hàng</strong> trước khi quét cây vải.
                                    @else
                                        Hãy quét mã QR của <strong>kệ hàng</strong> để xác nhận vị trí.
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- KHU VỰC QUÉT --}}
            <div class="card shadow border-0">
                <div
                    class="card-header text-white text-center py-2
                    {{ $mode === 'temp' ? 'bg-secondary' : ($mode === 'with_loc' ? 'bg-success' : 'bg-info') }}">
                    <h5 class="mb-0 fw-bold">
                        @if ($mode === 'temp')
                            <i class="fa-solid fa-inbox me-2"></i>NHẬP TẠM (Không Vị Trí)
                        @elseif ($mode === 'with_loc')
                            <i class="fa-solid fa-barcode-scan me-2"></i>NHẬP KHO CÓ VỊ TRÍ
                        @else
                            <i class="fa-solid fa-map-pin me-2"></i>XÁC NHẬN VỊ TRÍ CÂY VẢI
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <x-scanner inputModel="scannedCodeInput" onEnter="handleKeyInput" onScan="handleScan"
                        placeholder="{{ $mode === 'temp' ? 'Quét mã cây vải...' : (!$currentLocation ? 'Quét mã QR kệ hàng trước...' : 'Quét mã cây vải...') }}"
                        buttonText="Xác nhận" />

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
                            <h5 class="alert-heading fw-bold mb-1">
                                <i class="fa-solid {{ $icon }} me-2"></i>{{ $message }}
                            </h5>
                            @if (in_array($scanStatus, ['error', 'warning']))
                                <button wire:click="resetScan" class="btn btn-sm btn-outline-dark mt-2">
                                    <i class="fa-solid fa-rotate-right me-1"></i>Quét lại
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Thông tin cây vải vừa quét --}}
                    @if ($itemInfo)
                        <div class="table-responsive rounded border p-2 mb-3">
                            <table class="table table-borderless mb-0 small">
                                <tr>
                                    <td class="text-muted" width="35%">Mã Tem:</td>
                                    <td class="fw-bold font-monospace text-primary">{{ $itemInfo->code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Model:</td>
                                    <td>{{ optional($itemInfo->product)->code }} -
                                        {{ optional($itemInfo->product)->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Màu:</td>
                                    <td>{{ optional($itemInfo->color)->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">PO:</td>
                                    <td>{{ optional($itemInfo->order)->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Chiều dài:</td>
                                    <td>{{ $itemInfo->length ?? 0 }} m</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Vị trí hiện tại:</td>
                                    <td>
                                        @if ($itemInfo->location)
                                            <span class="badge bg-success">{{ $itemInfo->location->code }} —
                                                {{ $itemInfo->location->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Chưa có vị trí</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Trạng thái:</td>
                                    <td>
                                        <span class="badge {{ $itemInfo->status->badge() }}">
                                            {{ $itemInfo->status->label() }}
                                        </span>
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
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success">
                            <i class="fa-solid fa-list-check me-1"></i>Đã xử lý trong phiên
                            <span class="badge bg-success ms-1">{{ count($sessionItems) }}</span>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mã Tem</th>
                                        <th>Model</th>
                                        <th>Màu</th>
                                        <th>PO</th>
                                        <th>Vị Trí</th>
                                        <th class="text-end">Giờ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessionItems as $idx => $si)
                                        <tr wire:key="si-{{ $idx }}">
                                            <td class="text-muted">{{ $idx + 1 }}</td>
                                            <td class="font-monospace fw-bold text-primary">{{ $si['code'] }}</td>
                                            <td>{{ $si['product_code'] ?? '-' }}</td>
                                            <td>{{ $si['color_code'] ?? '-' }}</td>
                                            <td>{{ $si['order_code'] ?? '-' }}</td>
                                            <td>
                                                @if (!empty($si['location_code']))
                                                    <span class="badge bg-success">{{ $si['location_code'] }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Chưa có</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-muted small">{{ $si['time'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
        });
    </script>
</div>
