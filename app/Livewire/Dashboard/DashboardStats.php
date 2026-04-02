<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Item;
use App\Models\ItemGenealogy;
use App\Models\ItemMovement;
use App\Models\Product;
use App\Models\Location;
use App\Enums\ItemStatus;
use App\Enums\MovementAction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardStats extends Component
{
    // Bộ lọc thời gian
    public string $period = '30'; // days: 7, 30, 90

    // ──────────────────────────────────────────
    // KPI Cards
    // ──────────────────────────────────────────
    public array $kpis = [];

    // ──────────────────────────────────────────
    // Chart Data (JSON strings for JS)
    // ──────────────────────────────────────────
    public string $productionTrendJson = '{}';  // Line chart — Items tạo theo ngày
    public string $byProductJson       = '{}';  // Bar chart  — Items theo sản phẩm
    public string $statusDistJson      = '{}';  // Doughnut   — Phân bổ trạng thái
    public string $warehouseJson       = '{}';  // Bar chart  — Tồn kho theo vị trí
    public string $coatingTrendJson    = '{}';  // Line chart — Coating theo ngày

    public function mount(): void
    {
        $this->loadAll();
    }

    public function updatedPeriod(): void
    {
        $this->loadAll();
    }

    private function getFrom(): Carbon
    {
        return Carbon::now()->subDays((int) $this->period)->startOfDay();
    }

    private function loadAll(): void
    {
        $this->loadKpis();
        $this->loadProductionTrend();
        $this->loadByProduct();
        $this->loadStatusDist();
        $this->loadWarehouseStock();
        $this->loadCoatingTrend();
    }

    // ──────────────────────────────────────────
    // KPI Cards
    // ──────────────────────────────────────────
    private function loadKpis(): void
    {
        $from = $this->getFrom();

        $totalCreated    = Item::where('created_at', '>=', $from)->count();
        $totalInWarehouse = Item::where('status', ItemStatus::IN_WAREHOUSE)->count();
        $coatingCount    = ItemGenealogy::where('created_at', '>=', $from)->distinct('child_item_id')->count();
        $activeUsers     = \App\Models\ItemMovement::where('created_at', '>=', $from)
                            ->distinct('user_id')->count('user_id');

        // So sánh cùng kỳ trước
        $prevFrom = $from->copy()->subDays((int) $this->period);
        $prevCreated = Item::whereBetween('created_at', [$prevFrom, $from])->count();
        $prevCoating = ItemGenealogy::whereBetween('created_at', [$prevFrom, $from])->distinct('child_item_id')->count();

        $this->kpis = [
            [
                'title'   => 'Tem Đã Tạo',
                'value'   => number_format($totalCreated),
                'icon'    => 'fa-solid fa-tags',
                'color'   => 'primary',
                'change'  => $this->calcChange($prevCreated, $totalCreated),
                'unit'    => 'tem',
            ],
            [
                'title'   => 'Đang Trong Kho',
                'value'   => number_format($totalInWarehouse),
                'icon'    => 'fa-solid fa-warehouse',
                'color'   => 'success',
                'change'  => null,
                'unit'    => 'cây',
            ],
            [
                'title'   => 'Ca Tráng',
                'value'   => number_format($coatingCount),
                'icon'    => 'fa-solid fa-layer-group',
                'color'   => 'warning',
                'change'  => $this->calcChange($prevCoating, $coatingCount),
                'unit'    => 'lần',
            ],
            [
                'title'   => 'Nhân Viên Hoạt Động',
                'value'   => number_format($activeUsers),
                'icon'    => 'fa-solid fa-users',
                'color'   => 'info',
                'change'  => null,
                'unit'    => 'người',
            ],
        ];
    }

    private function calcChange(int $old, int $new): array
    {
        if ($old === 0) return ['pct' => null, 'up' => true];
        $pct = round((($new - $old) / $old) * 100, 1);
        return ['pct' => abs($pct), 'up' => $pct >= 0];
    }

    // ──────────────────────────────────────────
    // Chart 1: Xu hướng sản xuất theo ngày
    // ──────────────────────────────────────────
    private function loadProductionTrend(): void
    {
        $from = $this->getFrom();

        $rows = Item::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', $from)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        // Điền đủ ngày (kể cả ngày không có data = 0)
        $labels = [];
        $data   = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
            $data[]   = $rows[$day] ?? 0;
        }

        $this->productionTrendJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    // ──────────────────────────────────────────
    // Chart 2: Items theo sản phẩm (top 8)
    // ──────────────────────────────────────────
    private function loadByProduct(): void
    {
        $from = $this->getFrom();

        $rows = Item::selectRaw('product_id, COUNT(*) as total')
            ->where('created_at', '>=', $from)
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('product:id,code,name')
            ->get();

        $labels = $rows->map(fn($r) => $r->product?->code ?? 'N/A')->toArray();
        $data   = $rows->pluck('total')->toArray();

        $this->byProductJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    // ──────────────────────────────────────────
    // Chart 3: Phân bổ trạng thái Item
    // ──────────────────────────────────────────
    private function loadStatusDist(): void
    {
        $rows = Item::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = [
            0 => 'Vừa tạo',
            1 => 'Đã xác nhận',
            2 => 'Trong kho',
        ];

        $labels = [];
        $data   = [];
        foreach ($statusLabels as $val => $label) {
            $labels[] = $label;
            $data[]   = $rows[$val] ?? 0;
        }

        $this->statusDistJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    // ──────────────────────────────────────────
    // Chart 4: Tồn kho theo vị trí (top 10)
    // ──────────────────────────────────────────
    private function loadWarehouseStock(): void
    {
        $rows = Item::selectRaw('current_location_id, COUNT(*) as total')
            ->where('status', ItemStatus::IN_WAREHOUSE)
            ->whereNotNull('current_location_id')
            ->groupBy('current_location_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('location:id,code,name')
            ->get();

        $labels = $rows->map(fn($r) => $r->location?->code ?? 'N/A')->toArray();
        $data   = $rows->pluck('total')->toArray();

        $this->warehouseJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    // ──────────────────────────────────────────
    // Chart 5: Xu hướng Tráng theo ngày
    // ──────────────────────────────────────────
    private function loadCoatingTrend(): void
    {
        $from = $this->getFrom();

        $rows = ItemGenealogy::selectRaw('DATE(created_at) as day, COUNT(DISTINCT child_item_id) as total')
            ->where('created_at', '>=', $from)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data   = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
            $data[]   = $rows[$day] ?? 0;
        }

        $this->coatingTrendJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-stats');
    }
}
