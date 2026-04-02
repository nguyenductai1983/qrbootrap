<div class="container-fluid py-4">

    {{-- ═══ HEADER ═══════════════════════════════════════ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="fa-solid fa-chart-line text-primary me-2"></i>Báo Cáo &amp; Phân Tích
            </h4>
            <small class="text-muted">Dữ liệu thực từ hệ thống · cập nhật mỗi lần tải trang</small>
        </div>
        {{-- Bộ lọc thời gian --}}
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small me-1">Khoảng thời gian:</span>
            <div class="btn-group" role="group">
                <button wire:click="$set('period','7')"
                    class="btn btn-sm {{ $period === '7' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    7 ngày
                </button>
                <button wire:click="$set('period','30')"
                    class="btn btn-sm {{ $period === '30' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    30 ngày
                </button>
                <button wire:click="$set('period','90')"
                    class="btn btn-sm {{ $period === '90' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    90 ngày
                </button>
            </div>
            {{-- Loading indicator --}}
            <span wire:loading class="spinner-border spinner-border-sm text-primary" role="status"></span>
        </div>
    </div>

    {{-- ═══ BANNER PHÂN QUYỀN XEM ════════════════════ --}}
    @if ($isManagerView)
        <div class="alert alert-info border-0 d-flex align-items-center gap-3 mb-4 shadow-sm"
            style="border-left: 4px solid #0dcaf0 !important;">
            <div class="bg-info bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                style="width:44px;height:44px;">
                <i class="fa-solid fa-globe text-info"></i>
            </div>
            <div>
                <div class="fw-bold text-info-emphasis">Chế độ: Toàn Bộ Hệ Thống</div>
                <div class="small">Bạn đang xem dữ liệu của <strong>tất cả nhân viên</strong> trong hệ thống.
                </div>
            </div>
            <div class="ms-auto">
                <span class="badge bg-info text-white rounded-pill px-3 py-2">
                    <i class="fa-solid fa-shield-halved me-1"></i>Admin / Manager
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-0 d-flex align-items-center gap-3 mb-4 shadow-sm"
            style="border-left: 4px solid #ffc107 !important;">
            <div class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                style="width:44px;height:44px;">
                <i class="fa-solid fa-user text-warning"></i>
            </div>
            <div>
                <div class="fw-bold text-warning-emphasis">Chế độ: Dữ Liệu Cá Nhân</div>
                <div class="small">Bạn chỉ đang xem <strong>dữ liệu do chính bạn thực hiện</strong>. Liên hệ
                    quản lý để xem báo cáo toàn hệ thống.</div>
            </div>
            <div class="ms-auto">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                    <i class="fa-solid fa-user-clock me-1"></i>Cá Nhân
                </span>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════
         TABS điều hướng (phân quyền)
    ═══════════════════════════════════════ --}}
    @if (count($tabs) === 0)
        <div class="alert alert-warning">
            <i class="fa-solid fa-lock me-2"></i>Bạn không có quyền xem báo cáo nào.
        </div>
    @else
        <ul class="nav nav-pills mb-4 gap-1" role="tablist">
            @foreach ($tabs as $tab)
                <li class="nav-item" role="presentation">
                    <button wire:click="setTab('{{ $tab['slug'] }}')"
                        class="nav-link px-4 py-2 d-flex align-items-center gap-2
                               {{ $activeTab === $tab['slug'] ? 'active' : 'text-muted' }}"
                        style="{{ $activeTab === $tab['slug'] ? '' : 'background: #f8fafc; border: 1px solid #e2e8f0;' }}">
                        <i class="{{ $tab['icon'] }}"></i>
                        {{ $tab['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>

        {{-- ═══ KPI CARDS ════ --}}
        @if (count($visibleKpis) > 0)
            <div class="row g-3 mb-4">
                @foreach ($visibleKpis as $kpi)
                    <div
                        class="col-6 col-md-4 col-lg-{{ count($visibleKpis) <= 3 ? '4' : '3' }} col-xl-{{ count($visibleKpis) <= 4 ? '3' : '2' }}">
                        <div
                            class="card border-2 border-{{ $kpi['color'] }} border-opacity-25 shadow-sm h-100 hover-kpi">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="bg-{{ $kpi['color'] }} bg-opacity-10 rounded-circle p-2"
                                        style="width: 42px; height: 42px; display:flex; align-items:center; justify-content:center;">
                                        <i class="{{ $kpi['icon'] }} text-{{ $kpi['color'] }}"></i>
                                    </div>
                                    @if ($kpi['change'] && $kpi['change']['pct'] !== null)
                                        <span
                                            class="badge rounded-pill {{ $kpi['change']['up'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} small">
                                            <i
                                                class="fa-solid {{ $kpi['change']['up'] ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i>
                                            {{ $kpi['change']['pct'] }}%
                                        </span>
                                    @endif
                                </div>
                                <div class="fw-bold text-{{ $kpi['color'] }}"
                                    style="font-size: 1.6rem; line-height: 1.1;">
                                    {{ $kpi['value'] }}
                                </div>
                                <div class="fw-semibold text-dark small text-uppercase tracking-wide mt-1">
                                    {{ $kpi['title'] }}
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    {{ $period }} ngày · {{ $kpi['unit'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ═══════════════════════════════════════
             TAB CONTENT
        ═══════════════════════════════════════ --}}

        {{-- ─── TAB: TỔNG QUAN ─────────────────── --}}
        @if ($activeTab === 'overview')
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-chart-line text-primary me-2"></i>Xu Hướng Tạo Tem
                            </h6>
                            <small class="text-muted">Tem/ngày · Toàn hệ thống</small>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-production-trend" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-circle-half-stroke text-warning me-2"></i>Phân Bổ Trạng Thái
                            </h6>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="c-status-dist" style="max-height:220px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-fire text-danger me-2"></i>Xu Hướng Tráng
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-coating-overview" height="140"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-layer-group text-success me-2"></i>Sản Phẩm
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-by-product-overview" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── TAB: SẢN XUẤT ──────────────────── --}}
        @if ($activeTab === 'production')
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-chart-area text-primary me-2"></i>Xu Hướng Tạo Tem Theo Ngày
                            </h6>
                            <small class="text-muted">
                                {{ $period }} ngày gần nhất
                                @if (!$isManagerView)
                                    <span class="badge ms-1"
                                        style="background:#fef9c3; color:#92400e; border:1px solid #fde68a;">Của
                                        bạn</span>
                                @endif
                            </small>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-production-full" height="70"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-trophy text-warning me-2"></i>
                                {{ $isManagerView ? 'Sản Xuất Nhiều Nhất' : 'Model Bạn Sản Xuất' }}
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-by-product-full" height="160"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-circle-half-stroke text-info me-2"></i>Tỷ Lệ Trạng Thái Sản Phẩm
                            </h6>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="c-status-production" style="max-height:250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── TAB: TRÁNG VẢI ─────────────────── --}}
        @if ($activeTab === 'coating')
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-fire text-danger me-2"></i>Xu Hướng Ca Tráng Theo Ngày
                            </h6>
                            <small class="text-muted">
                                Số ca điều xác nhận
                                @if (!$isManagerView)
                                    <span class="badge ms-1"
                                        style="background:#fef9c3; color:#92400e; border:1px solid #fde68a;">Của
                                        bạn</span>
                                @endif
                            </small>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-coating-full" height="70"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── TAB: KHO HÀNG ──────────────────── --}}
        @if ($activeTab === 'warehouse')
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-boxes-stacked text-info me-2"></i>Tồn Kho Theo Vị Trí Kệ
                            </h6>
                            <small class="text-muted">
                                {{ $isManagerView ? 'Top 10 kệ nhiều hàng nhất' : 'Kệ do bạn nhập' }}
                            </small>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-warehouse-stock" height="130"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-arrows-rotate text-success me-2"></i>Hoạt Động Kho
                            </h6>
                            <small class="text-muted">Phân loại di chuyển</small>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="c-movement-dist" style="max-height:220px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-chart-line text-primary me-2"></i>Timeline Hoạt Động Kho
                            </h6>
                            <small class="text-muted">
                                Theo từng loại hành động
                                @if (!$isManagerView)
                                    <span class="badge ms-1"
                                        style="background:#fef9c3; color:#92400e; border:1px solid #fde68a;">Của
                                        bạn</span>
                                @endif
                            </small>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="c-movement-timeline" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>

{{-- ═══════════════════════════════════════
     STYLES
═══════════════════════════════════════ --}}
<style>
    .hover-kpi {
        transition: transform .2s, box-shadow .2s;
        cursor: default;
    }

    .hover-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .12) !important;
    }

    .nav-pills .nav-link {
        border-radius: 8px;
        font-size: .875rem;
        font-weight: 600;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
    }

    .tracking-wide {
        letter-spacing: .05em;
    }
</style>

{{-- ═══════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════ --}}
<script>
    (function() {
        const COLORS = {
            blue: '#4361ee',
            green: '#3bc99a',
            yellow: '#f59e0b',
            cyan: '#06b6d4',
            purple: '#8b5cf6',
            red: '#ef4444',
            orange: '#f97316',
            teal: '#14b8a6'
        };
        const C_LIST = Object.values(COLORS);

        let globalTab = '{{ $activeTab }}';
        let globalData = {
            productionTrend: @json(json_decode($productionTrendJson)),
            byProduct: @json(json_decode($byProductJson)),
            statusDist: @json(json_decode($statusDistJson)),
            warehouseStock: @json(json_decode($warehouseStockJson)),
            coatingTrend: @json(json_decode($coatingTrendJson)),
            movementTimeline: @json(json_decode($movementTimelineJson)),
        };

        const _inst = {};

        function c(id, type, labels, datasets, extra = {}) {
            const el = document.getElementById(id);
            if (!el) return;
            if (_inst[id]) _inst[id].destroy();
            _inst[id] = new Chart(el, {
                type,
                data: {
                    labels: labels ?? [],
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: datasets.length > 1 || type === 'doughnut' || type === 'pie'
                        },
                        tooltip: {
                            cornerRadius: 8,
                            padding: 10
                        },
                        ...(extra.plugins ?? {}),
                    },
                    scales: (type !== 'doughnut' && type !== 'pie') ? {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45
                            }
                        },
                        y: {
                            grid: {
                                color: '#f1f5f9'
                            },
                            beginAtZero: true
                        },
                        ...(extra.scales ?? {})
                    } : {},
                    ...extra,
                }
            });
        }

        function buildCharts() {
            Chart.defaults.font.family = "'Inter','Roboto',sans-serif";
            Chart.defaults.color = '#64748b';

            const tab = globalTab;
            const DATA = globalData;

            if (tab === 'overview') {
                c('c-production-trend', 'line',
                    DATA.productionTrend.labels,
                    [{
                        label: 'Tem tạo',
                        data: DATA.productionTrend.data,
                        borderColor: COLORS.blue,
                        backgroundColor: COLORS.blue + '18',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3
                    }]);

                c('c-status-dist', 'doughnut',
                    DATA.statusDist.labels,
                    [{
                        data: DATA.statusDist.data,
                        backgroundColor: ['#94a3b8', COLORS.blue, COLORS.green],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }], {
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 16,
                                    boxWidth: 12
                                }
                            }
                        }
                    });

                c('c-coating-overview', 'line',
                    DATA.coatingTrend.labels,
                    [{
                        label: 'Ca tráng',
                        data: DATA.coatingTrend.data,
                        borderColor: COLORS.red,
                        backgroundColor: COLORS.red + '18',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3
                    }]);

                c('c-by-product-overview', 'bar',
                    DATA.byProduct.labels,
                    [{
                        label: 'Số tem',
                        data: DATA.byProduct.data,
                        backgroundColor: DATA.byProduct.labels?.map((_, i) => C_LIST[i % C_LIST.length] +
                            'cc'),
                        borderRadius: 6
                    }]);
            }

            if (tab === 'production') {
                c('c-production-full', 'line',
                    DATA.productionTrend.labels,
                    [{
                        label: 'Tem tạo',
                        data: DATA.productionTrend.data,
                        borderColor: COLORS.blue,
                        backgroundColor: COLORS.blue + '18',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 7
                    }]);

                c('c-by-product-full', 'bar',
                    DATA.byProduct.labels,
                    [{
                        label: 'Số tem',
                        data: DATA.byProduct.data,
                        backgroundColor: DATA.byProduct.labels?.map((_, i) => C_LIST[i % C_LIST.length] +
                            'bb'),
                        borderRadius: 8
                    }]);

                c('c-status-production', 'pie',
                    DATA.statusDist.labels,
                    [{
                        data: DATA.statusDist.data,
                        backgroundColor: ['#94a3b8', COLORS.blue, COLORS.green],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }], {
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    });
            }

            if (tab === 'coating') {
                c('c-coating-full', 'line',
                    DATA.coatingTrend.labels,
                    [{
                        label: 'Ca tráng',
                        data: DATA.coatingTrend.data,
                        borderColor: COLORS.red,
                        backgroundColor: COLORS.red + '22',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 8
                    }]);
            }

            if (tab === 'warehouse') {
                c('c-warehouse-stock', 'bar',
                    DATA.warehouseStock.labels,
                    [{
                        label: 'Số cây',
                        data: DATA.warehouseStock.data,
                        backgroundColor: COLORS.cyan + 'bb',
                        borderRadius: 6
                    }]);

                const mvColors = {
                    IN_WAREHOUSE: COLORS.green,
                    OUT_WAREHOUSE: COLORS.red,
                    CONFIRM_LOCATION: COLORS.blue,
                    MOVE: COLORS.yellow
                };
                const mvData = DATA.movementTimeline.datasets ?? [];
                const totalByType = mvData.map(ds => ({
                    label: ds.label,
                    total: ds.data.reduce((a, b) => a + b, 0),
                    color: mvColors[ds.label] || '#94a3b8'
                }));
                if (totalByType.length) {
                    c('c-movement-dist', 'doughnut',
                        totalByType.map(x => x.label),
                        [{
                            data: totalByType.map(x => x.total),
                            backgroundColor: totalByType.map(x => x.color),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }], {
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        });
                }

                if (mvData.length) {
                    c('c-movement-timeline', 'line',
                        DATA.movementTimeline.labels,
                        mvData.map(ds => ({
                            ...ds,
                            borderColor: mvColors[ds.label] || '#94a3b8',
                            backgroundColor: (mvColors[ds.label] || '#94a3b8') + '18',
                            fill: false,
                            tension: 0.4,
                            pointRadius: 2,
                        })));
                }
            }
        }

        function init() {
            if (typeof Chart !== 'undefined') {
                buildCharts();
                return;
            }
            const s = document.createElement('script');
            s.src = '{{ asset('js/chart.umd.min.js') }}';
            s.onload = buildCharts;
            document.head.appendChild(s);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        // Livewire v3: Lắng nghe event từ backend, cập nhật DATA nội bộ rồi render
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh-charts', (event) => {
                let payload = Array.isArray(event) ? event[0] : event;
                if (payload && payload.tab) {
                    globalTab = payload.tab;
                    globalData = payload.data;
                }
                setTimeout(init, 100); // Đợi DOM morph xong mới vẽ lại chart
            });
        });
    })();
</script>
