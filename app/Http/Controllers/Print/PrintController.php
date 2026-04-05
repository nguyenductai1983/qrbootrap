<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;

class PrintController extends Controller
{
    /**
     * Màn hình In Tập Trung (Centralized Print View)
     * Nhận mảng IDs từ URL và render ra View chứa HTML in ấn.
     */
    public function printLabels(Request $request)
    {
        $idsString = $request->query('ids', '');
        if (empty($idsString)) {
            return back()->with('error', 'Không tìm thấy tem hợp lệ để in.');
        }

        $ids = explode(',', $idsString);

        // Lấy danh sách tem từ DB
        $items = Item::whereIn('id', $ids)->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Không tìm thấy thông tin tem trong hệ thống.');
        }

        // Đọc các thiết lập khổ giấy từ URL (Mặc định: QR Code, 2 khối ngang, 5 khối dọc, chữ 10px)
        $format = $request->query('format', 'QR');
        $cols = (int) $request->query('cols', 2);
        $rows = (int) $request->query('rows', 5);
        $fontSize = (int) $request->query('size', 10);

        return view('print.labels', compact('items', 'format', 'cols', 'rows', 'fontSize'));
    }

    /**
     * In QR / Barcode cho các Vị Trí Kho (Location)
     * Dán lên cột/kệ trong kho thực tế
     */
    public function printLocations(Request $request)
    {
        $idsString = $request->query('ids', '');
        if (empty($idsString)) {
            return back()->with('error', 'Không tìm thấy vị trí hợp lệ để in.');
        }

        $ids = explode(',', $idsString);
        $locations = Location::whereIn('id', $ids)->get();

        if ($locations->isEmpty()) {
            return back()->with('error', 'Không tìm thấy thông tin vị trí trong hệ thống.');
        }

        $format   = $request->query('format', 'QR');
        $cols     = (int) $request->query('cols', 3);
        $rows     = (int) $request->query('rows', 4);
        $fontSize = (int) $request->query('fontSize', 10);

        return view('print.location-labels', compact('locations', 'format', 'cols', 'rows', 'fontSize'));
    }

    /**
     * In nhãn Code Text (chỉ mã vị trí dạng văn bản, không QR/Barcode)
     * Dùng để dán thẻ code lên kệ/hàng trong kho
     */
    public function printLocationCodes(Request $request)
    {
        $idsString = $request->query('ids', '');
        if (empty($idsString)) {
            return back()->with('error', 'Không tìm thấy vị trí hợp lệ để in.');
        }

        $ids = explode(',', $idsString);
        $locations = Location::whereIn('id', $ids)->get();

        if ($locations->isEmpty()) {
            return back()->with('error', 'Không tìm thấy thông tin vị trí trong hệ thống.');
        }

        $cols     = (int) $request->query('cols', 3);
        $rows     = (int) $request->query('rows', 8);
        $fontSize = (int) $request->query('fontSize', 12);

        return view('print.location-code-labels', compact('locations', 'cols', 'rows', 'fontSize'));
    }
}
