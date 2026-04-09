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

    // ─── Theo bộ phận (Department Loop Data) ────
    public array $departmentsData = [];
    public array $globalChart = [];

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->isManagerView = $user->canViewAllDepartments();
        $this->isCurrentUserWarehouse = (bool) optional($user->department)->is_warehouse;

        // Redirect nhân viên kho (không phải quản lý) sang WarehouseDashboard
        if ($this->isCurrentUserWarehouse && !$this->isManagerView) {
            return redirect()->route('warehouse.dashboard');
        }

        $this->loadDepartmentsData();
    }

    public function updatedPeriod(): void
    {
        $this->loadDepartmentsData();
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
    private function loadDepartmentsData(): void
    {
        $from = $this->getFrom();

        $departmentsQuery = Department::query();
        if (!$this->isManagerView) {
            $departmentsQuery->where('id', Auth::user()->department_id);
        }
        $departmentsQuery->where('is_admin', false);
        $departments = $departmentsQuery->with('users')->get();

        $allDays = [];
        for ($i = (int)$this->period - 1; $i >= 0; $i--) {
            $allDays[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        $labels = array_map(fn($d) => Carbon::parse($d)->format('d/m'), $allDays);

        $data = [];
        $globalChartDatasets = [];
        $colorList = ['#4361ee', '#f59e0b', '#3bc99a', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];
        $colorIdx = 0;

        foreach ($departments as $dept) {
            $isWarehouse = (bool) $dept->is_warehouse;
            $deptName = $dept->name;
            $deptId   = $dept->id;
            
            $deptColor = $colorList[$colorIdx % count($colorList)];
            $colorIdx++;

            if ($isWarehouse) {
                // Khâu trung gian
                $userIds = $dept->users->pluck('id');
                $totalInWarehouse = Item::whereIn('warehoused_by', $userIds)
                    ->where('status', ItemStatus::IN_WAREHOUSE)->count();
                
                $metrics = [
                    [
                        'title' => 'Đang / Đã Nhập Kho',
                        'value' => number_format($totalInWarehouse),
                        'icon'  => 'fa-solid fa-warehouse',
                        'color' => 'info',
                        'unit'  => 'cây'
                    ]
                ];

                // Timeline chart (Chỉ lấy hành động Nhập Kho làm sản lượng xuất của Khâu Kho)
                $mvTrend = ItemMovement::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                    ->where('created_at', '>=', $from)
                    ->where('action_type', 'IN_WAREHOUSE')
                    ->whereIn('user_id', $userIds)
                    ->groupBy('day')->orderBy('day')->pluck('total', 'day');

                $globalChartDatasets[] = [
                    'label'           => 'Nhập Kho (' . $deptName . ')',
                    'data'            => array_map(fn($d) => $mvTrend[$d] ?? 0, $allDays),
                    'borderColor'     => $deptColor,
                    'backgroundColor' => $deptColor . '18',
                    'tension'         => 0.4,
                    'fill'            => true,
                    'pointRadius'     => 3,
                ];

            } else {
                // Sản xuất
                $totalCreated = Item::where('department_id', $dept->id)
                    ->where('created_at', '>=', $from)->count();

                $coatingCount = ItemGenealogy::where('created_at', '>=', $from)
                    ->whereHas('item', function($q) use ($dept) {
                        $q->where('department_id', $dept->id);
                    })->distinct('child_item_id')->count();

                $totalInWarehouse = Item::where('department_id', $dept->id)
                    ->where('status', ItemStatus::IN_WAREHOUSE)->count();

                $metrics = [
                    [
                        'title' => 'Item Mới',
                        'value' => number_format($totalCreated),
                        'icon'  => 'fa-solid fa-tags',
                        'color' => 'primary',
                        'unit'  => 'tem'
                    ],
                    [
                        'title' => 'Đã Xác Nhận Dùng',
                        'value' => number_format($coatingCount),
                        'icon'  => 'fa-solid fa-layer-group',
                        'color' => 'warning',
                        'unit'  => 'lần'
                    ],
                    [
                        'title' => 'Đang / Đã Nhập Kho',
                        'value' => number_format($totalInWarehouse),
                        'icon'  => 'fa-solid fa-warehouse',
                        'color' => 'info',
                        'unit'  => 'cây'
                    ]
                ];

                // Timeline chart (Lấy lượng tem tạo mới đổi thành sản lượng của khâu)
                $prodTrend = Item::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                    ->where('department_id', $deptId)
                    ->where('created_at', '>=', $from)
                    ->groupBy('day')->orderBy('day')->pluck('total', 'day');
                    
                $globalChartDatasets[] = [
                    'label'           => 'Sản Lượng (' . $deptName . ')',
                    'data'            => array_map(fn($d) => $prodTrend[$d] ?? 0, $allDays),
                    'borderColor'     => $deptColor,
                    'backgroundColor' => $deptColor . '18',
                    'tension'         => 0.4,
                    'fill'            => true,
                    'pointRadius'     => 3,
                ];

                // Biểu đồ lượng Nhập Kho của khâu sản xuất
                $inboundTrend = Item::selectRaw('DATE(warehoused_at) as day, COUNT(*) as total')
                    ->where('department_id', $deptId)
                    ->where('warehoused_at', '>=', $from)
                    ->groupBy('day')->orderBy('day')->pluck('total', 'day');

                $globalChartDatasets[] = [
                    'label'           => 'Nhập Kho (' . $deptName . ')',
                    'data'            => array_map(fn($d) => $inboundTrend[$d] ?? 0, $allDays),
                    'borderColor'     => $deptColor,
                    'backgroundColor' => 'transparent',
                    'borderDash'      => [5, 5],
                    'tension'         => 0.4,
                    'fill'            => false,
                    'pointRadius'     => 3,
                ];

            }

            $data[] = [
                'id'           => $deptId,
                'name'         => $deptName,
                'is_warehouse' => $isWarehouse,
                'metrics'      => $metrics,
            ];
        }

        $this->departmentsData = $data;
        $this->globalChart = [
            'labels'   => $labels,
            'datasets' => $globalChartDatasets,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.analytics-dashboard', [
            'departmentsData' => $this->departmentsData,
        ]);
    }
}
