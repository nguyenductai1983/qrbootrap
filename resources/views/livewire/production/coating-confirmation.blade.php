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
        <div class="col-md-7">
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
                                class="fa-solid fa-boxes-stacked me-1"></i> Mộc Đầu Vào:</h6>
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
                                        <div class="col-12 col-md-5 small text-muted">
                                            Tồn: <b class="fs-6">{{ (float) $item['length'] }} m</b>
                                        </div>
                                        <div class="col-12 col-md-7">
                                            <div class="input-group input-group-sm shadow-sm">
                                                <span class="input-group-text fw-bold text-primary">Dùng:</span>
                                                <input type="number" step="0.1" max="{{ $item['length'] }}"
                                                    wire:model="usedLengths.{{ $item['id'] }}"
                                                    class="form-control text-end fw-bold input-used-length"
                                                    oninput="calculateFromUsed()" placeholder="0.0">
                                                <span class="input-group-text">m</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <hr class="text-secondary opacity-25">

                        <div class="d-flex justify-content-between align-items-end mb-3 mt-4">
                            <h6 class="fw-bold text-success text-uppercase mb-0"><i
                                    class="fa-solid fa-check-double me-1"></i> Thành phẩm đầu ra:</h6>
                            <span class="badge text-secondary border shadow-sm" title="Tỉ lệ: Tổng mộc / Thành phẩm">
                                <i class="fa-solid fa-robot text-info"></i> Tỉ lệ hao hụt: <span id="ratio-display"
                                    class="text-primary fw-bold">{{ number_format($coatingRatio ?? 1.07, 3) }}</span>
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold">Nhập chiều dài cây tráng thực tế thu
                                được:</label>
                            <div class="input-group shadow-sm">
                                <input type="number" step="0.1" wire:model="newLength" id="input-new-length"
                                    oninput="calculateFromNew(this.value)"
                                    class="form-control form-control-lg text-end fw-bold text-success fs-4"
                                    placeholder="0.0">
                                <span class="input-group-text fw-bold fs-5 text-success">mét (m)</span>
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
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- SCRIPT: TÍNH TOÁN NHANH BẰNG JS --}}
    {{-- ========================================== --}}
    <script>
        let currentRatio = {{ $coatingRatio ?? 1.07 }};

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
                document.getElementById('input-new-length').value = '';
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



            Livewire.on('alert', (event) => {
                Swal.fire({
                    icon: 'warning',
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
