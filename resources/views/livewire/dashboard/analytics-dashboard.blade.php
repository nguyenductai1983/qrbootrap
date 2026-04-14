<div class="container-fluid py-4">

    {{-- ═══ HEADER ═══════════════════════════════════════ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="row">
            <div class="col-md-8 col-12">
                <div>
                    <h4 class="fw-bold mb-0">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>Báo Cáo &amp; Phân Tích
                    </h4>
                    <small class="text-muted">Dữ liệu thực từ hệ thống · cập nhật mỗi lần tải trang</small>
                </div>
            </div>
            <div class="col-md-4 col-12">
                {{-- Bộ lọc thời gian --}}

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


    </div>

    {{-- ═══ BANNER PHÂN QUYỀN XEM ════════════════════ --}}
    @if ($isManagerView)
        <div class="alert alert-info border-0 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 mb-4 shadow-sm"
            style="border-left: 4px solid #0dcaf0 !important;">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="bg-info bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:44px;height:44px;">
                    <i class="fa-solid fa-globe text-info"></i>
                </div>
                <div>
                    <div class="fw-bold text-info-emphasis">Chế độ: Toàn Bộ Hệ Thống</div>
                    <div class="small">Bạn đang xem dữ liệu của <strong>tất cả BP</strong> trong hệ thống.
                    </div>
                </div>
            </div>
            <div class="ms-md-auto w-100 w-md-auto text-start text-md-end">
                <span class="badge bg-info text-white rounded-pill px-3 py-2">
                    <i class="fa-solid fa-shield-halved me-1"></i>Quản lý
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-0 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 mb-4 shadow-sm"
            style="border-left: 4px solid #ffc107 !important;">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:44px;height:44px;">
                    <i class="fa-solid fa-user text-warning"></i>
                </div>
                <div>
                    <div class="fw-bold text-warning-emphasis">Chế độ: Dữ Liệu Cá Nhân</div>
                    <div class="small">Bạn chỉ đang xem <strong>dữ liệu do chính bạn thực hiện</strong>. Liên hệ
                        quản lý để xem báo cáo toàn hệ thống.</div>
                </div>
            </div>
            <div class="ms-md-auto w-100 w-md-auto text-start text-md-end">
                <span class="badge bg-warning rounded-pill px-3 py-2">
                    <i class="fa-solid fa-user-clock me-1"></i>Nhân Viên
                </span>
            </div>
        </div>
    @endif

    {{-- ═══ TIMELINE CHUNG ════ --}}
    @if (!empty($globalChart) && !empty($globalChart['datasets']))
        <div class="card border-0 shadow-sm mb-5 d-none d-lg-block">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-chart-area text-primary me-2"></i>Tiến Độ Hoạt Động Khâu Dây Chuyền
                </h6>
            </div>
            <div class="card-body pt-0">
                <canvas id="c-global-timeline" height="80"></canvas>
            </div>
        </div>
    @endif

    {{-- ═══ DEPARTMENT CARDS ════ --}}
    @if (empty($departmentsData))
        <div class="alert alert-warning">
            <i class="fa-solid fa-lock me-2"></i>Bạn không có quyền xem thông tin báo cáo nào.
        </div>
    @else
        <div class="row g-4">
            @foreach ($departmentsData as $dept)
                <div class="col-12 col-xl-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 pt-4 pb-0">
                            <h5 class="fw-bold mb-0" style="color: #334155;">
                                <i
                                    class="fa-solid {{ $dept['is_warehouse'] ? 'fa-warehouse text-info' : 'fa-building text-primary' }} me-2"></i>
                                {{ $dept['name'] }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($dept['metrics'] as $kpi)
                                    <div class="col-12 col-md-4">
                                        <div
                                            class="card border-2 border-{{ $kpi['color'] }} border-opacity-25 shadow-sm h-100 hover-kpi">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="bg-{{ $kpi['color'] }} bg-opacity-10 rounded-circle p-2"
                                                        style="width: 42px; height: 42px; display:flex; align-items:center; justify-content:center;">
                                                        <i class="{{ $kpi['icon'] }} text-{{ $kpi['color'] }}"></i>
                                                    </div>
                                                </div>
                                                <div class="fw-bold text-{{ $kpi['color'] }}"
                                                    style="font-size: 1.6rem; line-height: 1.1;">
                                                    {{ $kpi['value'] }}
                                                </div>
                                                <div class="fw-semibold small text-uppercase tracking-wide mt-1">
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
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
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

    .tracking-wide {
        letter-spacing: .05em;
    }
</style>

{{-- ═══════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════ --}}
<script>
    (function() {
        let globalChart = @json($globalChart ?? []);

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
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            cornerRadius: 8,
                            padding: 10
                        },
                        ...(extra.plugins ?? {}),
                    },
                    scales: {
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
                    },
                    ...extra,
                }
            });
        }

        function buildCharts() {
            if (typeof Chart === 'undefined') return;
            Chart.defaults.font.family = "'Inter','Roboto',sans-serif";
            Chart.defaults.color = '#64748b';

            if (globalChart && globalChart.labels && globalChart.datasets) {
                c('c-global-timeline', 'line', globalChart.labels, globalChart.datasets);
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

        // Livewire v3: Lắng nghe event cập nhật dữ liệu khi đổi bộ lọc
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh-charts', (payload) => {
                let data = Array.isArray(payload) ? payload[0] : payload;
                if (data && data.globalChart) {
                    globalChart = data.globalChart;
                }
                setTimeout(buildCharts, 100);
            });
        });
    })();
</script>
