<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Item;
use App\Models\ItemGenealogy;
use App\Models\ItemMovement;
use App\Models\Product;
use App\Models\Location;
use App\Enums\ItemStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsDashboard extends Component
{
    // ─── Bộ lọc ────────────────────────────────
    public string $period    = '30';  // 7 | 30 | 90
    public string $activeTab = 'overview'; // slug của tab đang active

    // ─── Phân quyền xem dữ liệu ────────────────
    public bool $isManagerView = false; // true = admin/manager, false = user thường

    // ─── KPI Cards ─────────────────────────────
    public array $kpis = [];

    // ─── Chart data (JSON) ─────────────────────
    public string $productionTrendJson = '{}';
    public string $byProductJson       = '{}';
    public string $statusDistJson      = '{}';
    public string $warehouseStockJson  = '{}';
    public string $coatingTrendJson    = '{}';
    public string $movementTimelineJson = '{}';

    // ─── Danh sách tab hiển thị (phân quyền) ───
    public array $tabs = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check explicit feature permission for viewing system analytics
        $this->isManagerView = $user->can('view_all_departments') || $user->hasRole('admin');

        $this->tabs      = $this->buildTabs();
        $this->activeTab = $this->tabs[0]['slug'] ?? 'overview';
        $this->loadAll();
    }

    public function updatedPeriod(): void
    {
        $this->loadAll();
        $this->triggerChartRefresh();
    }

    public function setTab(string $slug): void
    {
        $this->activeTab = $slug;
        $this->triggerChartRefresh();
    }

    private function triggerChartRefresh(): void
    {
        // Livewire v3 named arguments are properly passed to JS
        $this->dispatch('refresh-charts', [
            'tab' => $this->activeTab,
            'data' => [
                'productionTrend'  => json_decode($this->productionTrendJson),
                'byProduct'        => json_decode($this->byProductJson),
                'statusDist'       => json_decode($this->statusDistJson),
                'warehouseStock'   => json_decode($this->warehouseStockJson),
                'coatingTrend'     => json_decode($this->coatingTrendJson),
                'movementTimeline' => json_decode($this->movementTimelineJson),
            ]
        ]);
    }

    // ═══════════════════════════════════════════
    // Xây dựng danh sách tab theo quyền user
    // ═══════════════════════════════════════════
    private function buildTabs(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tabs = [];

        // Tab Tổng Quan — chỉ Admin/Manager
        if ($this->isManagerView) {
            $tabs[] = [
                'slug'  => 'overview',
                'label' => 'Tổng Quan',
                'icon'  => 'fa-solid fa-chart-pie',
                'color' => 'primary',
            ];
        }

        // Tab Sản Xuất — ai có quyền in tem hoặc quét, hoặc admin/manager
        if ($user->can('products.print') || $user->can('products.scan') || $this->isManagerView) {
            $tabs[] = [
                'slug'  => 'production',
                'label' => 'Sản Xuất',
                'icon'  => 'fa-solid fa-industry',
                'color' => 'success',
            ];
        }

        // Tab Tráng — ai có quyền coating hoặc admin/manager
        if ($user->can('coating.scan') || $this->isManagerView) {
            $tabs[] = [
                'slug'  => 'coating',
                'label' => 'Tráng Vải',
                'icon'  => 'fa-solid fa-layer-group',
                'color' => 'warning',
            ];
        }

        // Tab Kho — ai có quyền warehouse hoặc admin/manager
        if ($user->can('warehouse.scan') || $user->can('warehouse.report') || $this->isManagerView) {
            $tabs[] = [
                'slug'  => 'warehouse',
                'label' => 'Kho Hàng',
                'icon'  => 'fa-solid fa-warehouse',
                'color' => 'info',
            ];
        }

        // Nếu không có tab nào (user không có quyền gì), vẫn cho vào tab thống kê cá nhân
        if (empty($tabs)) {
            $tabs[] = [
                'slug'  => 'production',
                'label' => 'Hoạt Động Của Tôi',
                'icon'  => 'fa-solid fa-user-clock',
                'color' => 'secondary',
            ];
        }

        return $tabs;
    }

    // ═══════════════════════════════════════════
    // Tải tất cả dữ liệu
    // ═══════════════════════════════════════════
    private function loadAll(): void
    {
        $this->loadKpis();
        $this->loadProductionTrend();
        $this->loadByProduct();
        $this->loadStatusDist();
        $this->loadWarehouseStock();
        $this->loadCoatingTrend();
        $this->loadMovementTimeline();
    }

    private function getFrom(): Carbon
    {
        return Carbon::now()->subDays((int) $this->period)->startOfDay();
    }

    // ─── Helper: áp dụng filter user nếu không phải manager ─
    /**
     * Tự động áp dụng scope thông qua whereHas('item') cho các bảng không có department_id
     */
    private function applyUserScope($query)
    {
        if (!$this->isManagerView) {
            $query->whereHas('item');
        }
        return $query;
    }

    // ─── KPI Cards ─────────────────────────────
    private function loadKpis(): void
    {
        $from     = $this->getFrom();
        $prevFrom = $from->copy()->subDays((int) $this->period);
        $userId   = Auth::id();

        // Tem đã tạo
        $qCreated = Item::where('created_at', '>=', $from);
        $totalCreated = $qCreated->count();

        $qPrevCreated = Item::whereBetween('created_at', [$prevFrom, $from]);
        $prevCreated = $qPrevCreated->count();

        // Đang trong kho (toàn hệ thống hoặc theo department)
        // Lưu ý: với Item, Global Scope đã tự động lọc theo department_id rồi.
        $totalInWarehouse = Item::where('status', ItemStatus::IN_WAREHOUSE)->count();

        // Ca tráng (liên quan đến ItemGenealogy, cần dùng whereHas)
        $qCoating = ItemGenealogy::where('created_at', '>=', $from);
        $this->applyUserScope($qCoating);
        $coatingCount = $qCoating->distinct('child_item_id')->count();

        $qPrevCoating = ItemGenealogy::whereBetween('created_at', [$prevFrom, $from]);
        $this->applyUserScope($qPrevCoating);
        $prevCoating = $qPrevCoating->distinct('child_item_id')->count();

        // Nhân viên hoạt động (chỉ admin/manager mới thấy KPI này)
        $activeUsers = $this->isManagerView
            ? ItemMovement::where('created_at', '>=', $from)->distinct('user_id')->count('user_id')
            : null;

        // Tổng chiều dài
        $qLength = Item::where('created_at', '>=', $from);
        $totalLength = $qLength->sum('original_length');

        $kpis = [
            [
                'slug'    => 'production',
                'title'   => $this->isManagerView ? 'Tem Đã Tạo' : 'Tem Của Bộ Phận',
                'value'   => number_format($totalCreated),
                'icon'    => 'fa-solid fa-tags',
                'color'   => 'primary',
                'change'  => $this->calcChange($prevCreated, $totalCreated),
                'unit'    => 'tem',
                'visible' => true,
            ],
            [
                'slug'    => 'warehouse',
                'title'   => $this->isManagerView ? 'Đang Trong Kho' : 'Kho Bộ Phận',
                'value'   => number_format($totalInWarehouse),
                'icon'    => 'fa-solid fa-warehouse',
                'color'   => 'success',
                'change'  => null,
                'unit'    => 'cây',
                'visible' => true,
            ],
            [
                'slug'    => 'coating',
                'title'   => $this->isManagerView ? 'Ca Tráng' : 'Ca Tráng Bộ Phận',
                'value'   => number_format($coatingCount),
                'icon'    => 'fa-solid fa-layer-group',
                'color'   => 'warning',
                'change'  => $this->calcChange($prevCoating, $coatingCount),
                'unit'    => 'lần',
                'visible' => true,
            ],
            [
                'slug'    => 'overview',
                'title'   => 'NV Hoạt Động',
                'value'   => $activeUsers !== null ? number_format($activeUsers) : '—',
                'icon'    => 'fa-solid fa-users',
                'color'   => 'info',
                'change'  => null,
                'unit'    => 'người',
                'visible' => $this->isManagerView, // chỉ admin/manager
            ],
            [
                'slug'    => 'production',
                'title'   => $this->isManagerView ? 'Tổng Chiều Dài' : 'Chiều Dài Bộ Phận',
                'value'   => number_format($totalLength, 1),
                'icon'    => 'fa-solid fa-ruler',
                'color'   => 'secondary',
                'change'  => null,
                'unit'    => 'mét',
                'visible' => true,
            ],
        ];

        $this->kpis = array_filter($kpis, fn($k) => $k['visible']);
        $this->kpis = array_values($this->kpis);
    }

    private function calcChange(int $old, int $new): array
    {
        if ($old === 0) return ['pct' => null, 'up' => true];
        $pct = round((($new - $old) / $old) * 100, 1);
        return ['pct' => abs($pct), 'up' => $pct >= 0];
    }

    // ─── Chart: Xu hướng sản xuất mỗi ngày ────
    private function loadProductionTrend(): void
    {
        $from = $this->getFrom();

        $query = Item::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', $from);

        $rows = $query->groupBy('day')->orderBy('day')->pluck('total', 'day');

        [$labels, $data] = $this->fillDays($rows);
        $this->productionTrendJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    // ─── Chart: Items theo sản phẩm (top 8) ───
    private function loadByProduct(): void
    {
        $from = $this->getFrom();

        $query = Item::selectRaw('product_id, COUNT(*) as total')
            ->where('created_at', '>=', $from)
            ->whereNotNull('product_id');

        $rows = $query->groupBy('product_id')->orderByDesc('total')->limit(8)
            ->with('product:id,code,name')->get();

        $this->byProductJson = json_encode([
            'labels' => $rows->map(fn($r) => $r->product?->code ?? 'N/A')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
            'names'  => $rows->map(fn($r) => $r->product?->name ?? 'N/A')->toArray(),
        ]);
    }

    // ─── Chart: Phân bổ trạng thái ─────────────
    private function loadStatusDist(): void
    {
        $query = Item::selectRaw('status, COUNT(*) as total');

        $rows = $query->groupBy('status')->pluck('total', 'status');

        $this->statusDistJson = json_encode([
            'labels' => ['Vừa tạo', 'Đã xác nhận', 'Trong kho'],
            'data'   => [$rows[0] ?? 0, $rows[1] ?? 0, $rows[2] ?? 0],
        ]);
    }

    // ─── Chart: Tồn kho theo vị trí (top 10) ──
    private function loadWarehouseStock(): void
    {
        $query = Item::selectRaw('current_location_id, COUNT(*) as total')
            ->where('status', ItemStatus::IN_WAREHOUSE)
            ->whereNotNull('current_location_id');

        $rows = $query->groupBy('current_location_id')->orderByDesc('total')->limit(10)
            ->with('location:id,code,name')->get();

        $this->warehouseStockJson = json_encode([
            'labels' => $rows->map(fn($r) => $r->location?->code ?? 'N/A')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ]);
    }

    // ─── Chart: Xu hướng coating ───────────────
    private function loadCoatingTrend(): void
    {
        $from = $this->getFrom();

        $query = ItemGenealogy::selectRaw('DATE(created_at) as day, COUNT(DISTINCT child_item_id) as total')
            ->where('created_at', '>=', $from);
        
        $this->applyUserScope($query);

        $rows = $query->groupBy('day')->orderBy('day')->pluck('total', 'day');

        [$labels, $data] = $this->fillDays($rows);
        $this->coatingTrendJson = json_encode(['labels' => $labels, 'data' => $data]);
    }

    // ─── Chart: Timeline movement theo hành động ─
    private function loadMovementTimeline(): void
    {
        $from = $this->getFrom();

        $query = ItemMovement::selectRaw('DATE(created_at) as day, action_type, COUNT(*) as total')
            ->where('created_at', '>=', $from);
        
        $this->applyUserScope($query);

        $rows = $query->groupBy('day', 'action_type')->orderBy('day')
            ->get()->groupBy('action_type');

        $allDays = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $allDays[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        $labels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $allDays);

        $datasets = [];
        $colorMap  = ['IN_WAREHOUSE' => '#3bc99a', 'OUT_WAREHOUSE' => '#ef4444', 'CONFIRM_LOCATION' => '#4361ee', 'MOVE' => '#f59e0b'];

        foreach ($rows as $action => $dayRows) {
            $byDay = $dayRows->pluck('total', 'day');
            $datasets[] = [
                'label'           => $action,
                'data'            => array_map(fn($d) => $byDay[$d] ?? 0, $allDays),
                'borderColor'     => $colorMap[$action] ?? '#94a3b8',
                'backgroundColor' => ($colorMap[$action] ?? '#94a3b8') . '22',
                'tension'         => 0.4,
                'fill'            => false,
            ];
        }

        $this->movementTimelineJson = json_encode(['labels' => $labels, 'datasets' => $datasets]);
    }

    // ─── Helper: điền đủ ngày ──────────────────
    private function fillDays($rows): array
    {
        $labels = [];
        $data   = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $day      = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
            $data[]   = $rows[$day] ?? 0;
        }
        return [$labels, $data];
    }

    public function render()
    {
        return view('livewire.dashboard.analytics-dashboard', [
            'visibleKpis' => $this->kpis,
        ]);
    }
}
