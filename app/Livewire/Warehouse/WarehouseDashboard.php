<?php

namespace App\Livewire\Warehouse;

use Livewire\Component;
use App\Models\Item;
use App\Enums\ItemStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Title('Bảng Điều Khiển Kho')]
class WarehouseDashboard extends Component
{
    public $totalItems = 0;
    public $totalLength = 0;
    public $totalWeight = 0;
    
    // Thống kê theo sản phẩm
    public $stockByProduct = [];

    // Chart
    public string $period = '30';
    public array $timelineChart = [];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedPeriod(): void
    {
        $this->loadData();
        $this->dispatch('refresh-charts', [
            'timelineChart' => $this->timelineChart,
        ]);
    }

    public function loadData()
    {
        // Lấy dữ liệu tổng (cơ bản)
        // Lưu ý: Đếm số lượng tem không quan tâm tới status nếu chưa update đầy đủ,
        // Nhưng ở module kho, tốt nhất là ItemStatus::IN_WAREHOUSE
        $query = Item::where('status', ItemStatus::IN_WAREHOUSE);

        $this->totalItems = $query->count();
        $this->totalLength = (clone $query)->sum('original_length');
        $this->totalWeight = (clone $query)->sum('weight');

        // Danh sách thống kê gom nhóm theo product_id
        $this->stockByProduct = Item::where('status', ItemStatus::IN_WAREHOUSE)
            ->whereNotNull('product_id')
            ->select('product_id', DB::raw('count(*) as total_items'), DB::raw('sum(original_length) as total_length'))
            ->with('product') // Load thông tin sản phẩm
            ->groupBy('product_id')
            ->orderByDesc('total_items')
            ->get();

        // --- Dữ liệu Chart ---
        $from = Carbon::now()->subDays((int) $this->period)->startOfDay();
        
        $allDays = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $allDays[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        $labels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $allDays);

        $inboundDataRaw = Item::selectRaw('DATE(warehoused_at) as day, department_id, COUNT(*) as total')
            ->where('warehoused_at', '>=', $from)
            ->whereNotNull('department_id')
            ->groupBy('day', 'department_id')
            ->get();

        $deptIds = $inboundDataRaw->pluck('department_id')->unique();
        $departments = \App\Models\Department::whereIn('id', $deptIds)->get()->keyBy('id');

        $datasets = [];
        $colorList = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1', '#fd7e14', '#e83e8c', '#20c997'];
        $colorIdx = 0;

        foreach ($deptIds as $deptId) {
            $deptName = isset($departments[$deptId]) ? $departments[$deptId]->name : "Khác ($deptId)";
            $deptData = $inboundDataRaw->where('department_id', $deptId)->pluck('total', 'day')->toArray();
            
            $color = $colorList[$colorIdx % count($colorList)];
            $datasets[] = [
                'label'           => 'Nhập từ ' . $deptName,
                'data'            => array_map(fn($d) => $deptData[$d] ?? 0, $allDays),
                'borderColor'     => $color,
                'backgroundColor' => 'transparent',
                'tension'         => 0.4,
                'fill'            => false,
                'pointRadius'     => 3,
            ];
            $colorIdx++;
        }

        // --- Thêm một đường Tổng cộng (Đứt nét) ---
        $totalData = Item::selectRaw('DATE(warehoused_at) as day, COUNT(*) as total')
            ->where('warehoused_at', '>=', $from)
            ->groupBy('day')
            ->pluck('total', 'day');

        $datasets[] = [
            'label'           => 'Tổng Nhập Kho (Tất cả)',
            'data'            => array_map(fn($d) => $totalData[$d] ?? 0, $allDays),
            'borderColor'     => '#334155',
            'backgroundColor' => '#33415510',
            'borderDash'      => [5, 5],
            'tension'         => 0.4,
            'fill'            => true,
            'pointRadius'     => 4,
        ];

        $this->timelineChart = [
            'labels'   => $labels,
            'datasets' => $datasets,
        ];
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-dashboard');
    }
}
