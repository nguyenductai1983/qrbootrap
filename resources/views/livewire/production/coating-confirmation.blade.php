<div>
    <div class="row g-3">
        {{-- KHU VỰC QUÉT MÃ VẠCH / CAMERA --}}
        <div class="col-md-5">
            <label class="small text-muted fw-bold" for="selectedProductId">Chọn Thành phẩm</label>
            <select wire:model="selectedProductId" id="selectedProductId"
                class="form-select form-select-sm border-secondary fw-bold text-success">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}
                    </option>
                @endforeach
            </select>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fa-solid fa-barcode me-2"></i> Quét Mã Mộc (Nguyên Liệu)
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Dùng súng quét (nhấp vào đây):</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-keyboard"></i></span>
                            <input type="text" wire:model="codeInput" wire:keydown.enter="addScannedItem"
                                class="form-control form-control-lg text-center text-primary fw-bold"
                                placeholder="Quét hoặc nhập mã..." autofocus>
                        </div>
                    </div>

                    <div class="position-relative mb-4">
                        <hr class="text-secondary">
                        <span
                            class="position-absolute top-50 start-50 translate-middle bg-white px-2 small text-muted fw-bold">HOẶC</span>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-dark btn-lg w-100 fw-bold shadow-sm"
                            id="btn-start-camera">
                            <i class="fa-solid fa-camera me-2"></i> Bật Camera
                        </button>
                    </div>

                    <div id="reader" width="100%" class="shadow-sm border border-2 border-primary"
                        style="display: none; border-radius: 8px; overflow: hidden;">
                    </div>
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
                                    <div class="row align-items-center bg-light p-2 rounded">
                                        <div class="col-5 small text-muted">
                                            Tồn kho: <b class="text-dark fs-6">{{ (float) $item['length'] }} m</b>
                                        </div>
                                        <div class="col-7">
                                            <div class="input-group input-group-sm shadow-sm">
                                                <span class="input-group-text bg-white fw-bold text-primary">Xuất
                                                    dùng:</span>
                                                <input type="number" step="0.1" max="{{ $item['length'] }}"
                                                    wire:model="usedLengths.{{ $item['id'] }}"
                                                    class="form-control text-end fw-bold input-used-length"
                                                    oninput="calculateFromUsed()" placeholder="0.0">
                                                <span class="input-group-text bg-white">m</span>
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
                            <span class="badge bg-light text-secondary border shadow-sm"
                                title="Tỉ lệ: Tổng mộc / Thành phẩm">
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
                                <span class="input-group-text bg-white fw-bold fs-5 text-success">mét (m)</span>
                            </div>
                        </div>

                        <button wire:click="confirmCoating" class="btn btn-success btn-lg w-100 fw-bold shadow">
                            <i class="fa-solid fa-print me-2"></i> XÁC NHẬN TẠO MÃ & IN TEM
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- SCRIPT: TÍNH TOÁN NHANH BẰNG JS & CAMERA   --}}
    {{-- ========================================== --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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

            const btnStart = document.getElementById('btn-start-camera');
            const readerDiv = document.getElementById('reader');
            let html5QrcodeScanner = null;

            if (btnStart) {
                btnStart.addEventListener('click', () => {
                    readerDiv.style.display = 'block';
                    btnStart.style.display = 'none';
                    html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    }, false);
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                });
            }

            function onScanSuccess(decodedText, decodedResult) {
                html5QrcodeScanner.clear();
                readerDiv.style.display = 'none';
                btnStart.style.display = 'block';
                @this.call('addScannedItem', decodedText);
            }

            function onScanFailure(error) {}

            Livewire.on('alert', (event) => {
                alert(event[0].message);
            });
        });
    </script>
</div>
