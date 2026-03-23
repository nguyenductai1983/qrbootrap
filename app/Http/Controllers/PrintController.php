<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

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
}
