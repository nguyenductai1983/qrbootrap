<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Tem Nhãn - Hệ Thống QR</title>
    <!-- CSS Bootstrap cơ bản -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- File CSS in mã vạch của hệ thống -->
    <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
    <style>
        /* Đảm bảo trang html/body vừa khít lúc in */
        body { background: white; margin: 0; padding: 0; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .d-print-none { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-area">
        @php
            $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            // 🌟 TÍNH TOÁN TỔNG SỐ TEM TRÊN 1 TỜ GIẤY
            $printCols = $cols > 0 ? $cols : 2;
            $printRows = $rows > 0 ? $rows : 5;
            $itemsPerPage = $printCols * $printRows;

            // Cắt nhỏ collection ra thành nhiều trang
            $pages = $items->chunk($itemsPerPage);
        @endphp
        
        @foreach ($pages as $pageItems)
            {{-- 🌟 MỖI VÒNG LẶP LÀ 1 TỜ GIẤY ĐỘC LẬP --}}
            <div class="print-page" @style(['--print-cols: ' . $printCols, '--print-rows: ' . $printRows])>
                <div class="print-grid">
                    @foreach ($pageItems as $item)
                        <div class="label-item">
                            <div class="barcode-wrapper">
                                @if ($format == 'QR')
                                    <div class="d-flex flex-column align-items-center justify-content-center"
                                        style="height: 100%; width: 100%;">
                                        <div class="qr-container">
                                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($item->code) !!}
                                        </div>
                                        <div class="code-text fw-bold text-center w-100 mt-1" @style(['font-size: ' . ($fontSize ?? 10) . 'px', 'letter-spacing: 0.5px', 'word-wrap: break-word', 'line-height: 1.2'])>
                                            {{ $item->code }}
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 pt-1">
                                        <div class="w-98 text-center">
                                            {!! $generator->getBarcode($item->code, $generator::TYPE_CODE_128, 2, 45) !!}
                                        </div>
                                        <div class="fw-bold mt-1 text-center w-100" @style(['font-size: ' . ($fontSize ?? 10) . 'px', 'letter-spacing: 1px', 'word-wrap: break-word'])>
                                            {{ $item->code }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Script Tự Động Bật Hộp Thoại In -->
    <script>
        window.onload = function() {
            // Tăng thời gian chờ lên 800ms để đảm bảo các mã QR/Barcode (SVG) đã được duyệt vẽ xong 100%
            setTimeout(function() {
                window.print();
            }, 800);
        }
    </script>
</body>
</html>
