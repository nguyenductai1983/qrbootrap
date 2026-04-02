<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In QR Vị Trí Kho</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
    <style>
        body { background: white; margin: 0; padding: 0; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .d-print-none { display: none !important; }
        }
        .location-label-name {
            font-size: 9px;
            text-align: center;
            word-wrap: break-word;
            line-height: 1.2;
            opacity: .85;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="print-area">
        @php
            $printCols = $cols > 0 ? $cols : 3;
            $printRows = $rows > 0 ? $rows : 4;
            $itemsPerPage = $printCols * $printRows;
            $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
            $pages = $locations->chunk($itemsPerPage);
        @endphp

        @foreach ($pages as $pageLocations)
            <div class="print-page" @style(['--print-cols: ' . $printCols, '--print-rows: ' . $printRows])>
                <div class="print-grid">
                    @foreach ($pageLocations as $location)
                        <div class="label-item">
                            <div class="barcode-wrapper">
                                @if ($format === 'QR')
                                    <div class="d-flex flex-column align-items-center justify-content-center"
                                        style="height:100%; width:100%;">
                                        <div class="qr-container">
                                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($location->code) !!}
                                        </div>
                                        <div class="code-text fw-bold text-center w-100 mt-1"
                                            @style(['font-size: ' . ($fontSize ?? 10) . 'px', 'letter-spacing: 0.5px', 'word-wrap: break-word', 'line-height: 1.2'])>
                                            {{ $location->code }}
                                        </div>
                                        <div class="location-label-name">{{ $location->name }}</div>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 pt-1">
                                        <div class="w-98 text-center">
                                            {!! $generator->getBarcode($location->code, $generator::TYPE_CODE_128, 2, 45) !!}
                                        </div>
                                        <div class="fw-bold mt-1 text-center w-100"
                                            @style(['font-size: ' . ($fontSize ?? 10) . 'px', 'letter-spacing: 1px', 'word-wrap: break-word'])>
                                            {{ $location->code }}
                                        </div>
                                        <div class="location-label-name">{{ $location->name }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.onload = function () {
            setTimeout(function () { window.print(); }, 800);
        }
    </script>
</body>
</html>
