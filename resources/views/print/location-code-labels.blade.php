<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Code Vị Trí Kho</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/barcode.css') }}">
    <style>
        body { background: white; margin: 0; padding: 0; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .d-print-none { display: none !important; }
        }

        /* Nhãn code text: căn giữa, chữ to đậm */
        .code-label-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            padding: 2px;
        }
        .code-label-code {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1.1;
            word-break: break-all;
            text-align: center;
            color: #000;
        }
        .code-label-name {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            word-break: break-word;
            text-align: center;
            opacity: .7;
            margin-top: 2px;
        }

        @media print {
            .code-label-code { color: #000 !important; -webkit-print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>
    <div class="print-area">
        @php
            $printCols   = max(1, (int)($cols ?? 3));
            $printRows   = max(1, (int)($rows ?? 8));
            $itemsPerPage = $printCols * $printRows;
            $fs          = (int)($fontSize ?? 12);
            $nameFontSize = max(6, $fs - 3);
            $pages = $locations->chunk($itemsPerPage);
        @endphp

        @foreach ($pages as $pageLocations)
            <div class="print-page" @style(['--print-cols: ' . $printCols, '--print-rows: ' . $printRows])>
                <div class="print-grid">
                    @foreach ($pageLocations as $location)
                        <div class="label-item">
                            <div class="barcode-wrapper">
                                <div class="code-label-inner">
                                    <div class="code-label-code" style="font-size: {{ $fs }}px;">
                                        {{ $location->code }}
                                    </div>
                                    @if ($location->name && $location->name !== $location->code)
                                        <div class="code-label-name" style="font-size: {{ $nameFontSize }}px;">
                                            {{ $location->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.onload = function () {
            setTimeout(function () { window.print(); }, 600);
        }
    </script>
</body>
</html>
