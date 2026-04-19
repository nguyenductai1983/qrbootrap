<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Item;
use App\Models\ItemGenealogy;
use App\Models\ItemMovement;
use App\Models\Product;
use App\Models\Location;
use App\Models\Department;
use App\Enums\ItemStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsDashboard extends Component
{
    // ─── Bộ lọc ────────────────────────────────
    public string $period    = '30';  // 7 | 30 | 90

    // ─── Phân quyền xem dữ liệu ────────────────
    public bool $isManagerView = false; // true = admin/manager, false = user thường
    public bool $isCurrentUserWarehouse = false;

    // ─── Theo sản phẩm (Product Loop Data) ────
    public array $productsData = [];
    public array $globalChart = [];

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->isManagerView = $user->canViewAllDepartments();
        $this->isCurrentUserWarehouse = (bool) optional($user->department)->is_warehouse;

        // Redirect nhân viên kho (không phải quản lý) sang WarehouseDashboard
        if ($this->isCurrentUserWarehouse && !$this->isManagerView) {
            // Nhân viên kho cũng xem analytics — không redirect nữa
        }

        $this->loadProductsData();
    }

    public function updatedPeriod(): void
    {
        $this->loadProductsData();
        $this->triggerChartRefresh();
    }

    private function triggerChartRefresh(): void
    {
        $this->dispatch('refresh-charts', [
            'globalChart' => $this->globalChart,
        ]);
    }

    private function getFrom(): Carbon
    {
        return Carbon::now()->subDays((int) $this->period)->startOfDay();
    }

    // ─── Load Data Loop ────────
    private function loadProductsData(): void
    {
        $from = $this->getFrom();
        $user = Auth::user();

        // === Lấy danh sách products theo phân quyền ===
        if ($this->isManagerView || $this->isCurrentUserWarehouse) {
            // Admin/Manager/Kho: thấy tất cả products
            $products = Product::all();
        } else {
            // User thường: chỉ thấy products liên kết với department của họ
            $products = Product::whereHas('departments', function ($q) use ($user) {
                $q->where('departments.id', $user->department_id);
            })->get();
        }

        $allDays = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $allDays[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        $labels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $allDays);

        $data = [];
        $globalChartDatasets = [];
        $colorList = ['#4361ee', '#f59e0b', '#3bc99a', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];
        $colorIdx = 0;

        foreach ($products as $product) {
            $prodColor = $colorList[$colorIdx % count($colorList)];
            $colorIdx++;
            $prodId = $product->id;
            $prodName = $product->name;

            // --- Metrics chung cho mọi product ---
            $totalCreated = Item::where('product_id', $prodId)
                ->where('created_at', '>=', $from)->count();

            $coatingCount = ItemGenealogy::where('created_at', '>=', $from)
                ->whereHas('item', function ($q) use ($prodId) {
                    $q->where('product_id', $prodId);
                })->distinct('child_item_id')->count();

            $totalInWarehouse = Item::where('product_id', $prodId)
                ->where('status', ItemStatus::IN_WAREHOUSE)->count();

            $metrics = [
                [
                    'title' => 'SP Mới',
                    'value' => number_format($totalCreated),
                    'icon'  => 'fa-solid fa-tags',
                    'color' => 'primary',
                    'unit'  => 'cây'
                ],
                [
                    'title' => 'Đã Xác Nhận Dùng',
                    'value' => number_format($coatingCount),
                    'icon'  => 'fa-solid fa-layer-group',
                    'color' => 'warning',
                    'unit'  => 'cây'
                ],
                [
                    'title' => 'Tồn Kho',
                    'value' => number_format($totalInWarehouse),
                    'icon'  => 'fa-solid fa-warehouse',
                    'color' => 'info',
                    'unit'  => 'cây'
                ],
            ];

            // --- Nhân viên kho: thêm metric nhập kho chi tiết ---
            if ($this->isCurrentUserWarehouse || $this->isManagerView) {
                $warehousedInPeriod = Item::where('product_id', $prodId)
                    ->where('warehoused_at', '>=', $from)
                    ->count();

                $metrics[] = [
                    'title' => 'Nhập Kho Trong Kỳ',
                    'value' => number_format($warehousedInPeriod),
                    'icon'  => 'fa-solid fa-boxes-stacked',
                    'color' => 'success',
                    'unit'  => 'cây · ' . $this->period . ' ngày'
                ];
            }

            // --- Timeline chart: Sản lượng mới theo ngày ---
            $prodTrend = Item::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('product_id', $prodId)
                ->where('created_at', '>=', $from)
                ->groupBy('day')->orderBy('day')->pluck('total', 'day');

            $globalChartDatasets[] = [
                'label'           => 'SL Mới (' . $prodName . ')',
                'data'            => array_map(fn($d) => $prodTrend[$d] ?? 0, $allDays),
                'borderColor'     => $prodColor,
                'backgroundColor' => $prodColor . '18',
                'tension'         => 0.4,
                'fill'            => true,
                'pointRadius'     => 3,
            ];

            // --- Timeline chart: Nhập kho theo ngày ---
            $inboundTrend = Item::selectRaw('DATE(warehoused_at) as day, COUNT(*) as total')
                ->where('product_id', $prodId)
                ->where('warehoused_at', '>=', $from)
                ->groupBy('day')->orderBy('day')->pluck('total', 'day');

            $globalChartDatasets[] = [
                'label'           => 'Nhập Kho (' . $prodName . ')',
                'data'            => array_map(fn($d) => $inboundTrend[$d] ?? 0, $allDays),
                'borderColor'     => $prodColor,
                'backgroundColor' => 'transparent',
                'borderDash'      => [5, 5],
                'tension'         => 0.4,
                'fill'            => false,
                'pointRadius'     => 3,
            ];

            $data[] = [
                'id'      => $prodId,
                'name'    => $prodName,
                'code'    => $product->code,
                'metrics' => $metrics,
            ];
        }

        $this->productsData = $data;
        $this->globalChart = [
            'labels'   => $labels,
            'datasets' => $globalChartDatasets,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.analytics-dashboard', [
            'productsData' => $this->productsData,
        ]);
    }
}
