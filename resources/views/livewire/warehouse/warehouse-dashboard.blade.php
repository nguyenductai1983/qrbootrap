<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">
                <i class="fa-solid fa-boxes-stacked me-2"></i>Bảng Điều Khiển Kho (Dashboard)
            </h4>
            <small class="text-muted">Tổng quan tình hình số lượng tồn kho theo thời gian thực</small>
        </div>
        <div>
            <button wire:click="loadData" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-rotate-right me-1"></i>Tải dữ liệu mới nhất
            </button>
        </div>
    </div>

    {{-- Thẻ KPI --}}
    <div class="row g-4 mb-4">
        {{-- KPI 1 --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-up" style="border-left: 5px solid #0d6efd !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase tracking-wide fw-bold mb-1">Tổng Số Sản Phẩm
                            </div>
                            <h2 class="fw-bold mb-0 text-primary">{{ number_format($totalItems) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center"
                            style="width: 54px; height: 54px;">
                            <i class="fa-solid fa-tags text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2 --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-up" style="border-left: 5px solid #198754 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase tracking-wide fw-bold mb-1">Tổng Độ Dài (m)
                            </div>
                            <h2 class="fw-bold mb-0 text-success">{{ number_format($totalLength, 2) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center"
                            style="width: 54px; height: 54px;">
                            <i class="fa-solid fa-ruler-horizontal text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3 --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-up" style="border-left: 5px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase tracking-wide fw-bold mb-1">Tổng Trọng Lượng
                                (kg)</div>
                            <h2 class="fw-bold mb-0 text-warning">{{ number_format($totalWeight, 2) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center"
                            style="width: 54px; height: 54px;">
                            <i class="fa-solid fa-weight-hanging text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bộ Lọc và Biểu Đồ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-info me-2"></i>Biểu Đồ Theo Thời Gian</h6>
            <div class="d-flex align-items-center">
                <span class="text-muted small me-2">Lọc:</span>
                <div class="btn-group" role="group">
                    <button wire:click="$set('period','7')"
                        class="btn btn-sm {{ $period === '7' ? 'btn-info' : 'btn-outline-secondary' }}">
                        7 ngày
                    </button>
                    <button wire:click="$set('period','30')"
                        class="btn btn-sm {{ $period === '30' ? 'btn-info' : 'btn-outline-secondary' }}">
                        30 ngày
                    </button>
                    <button wire:click="$set('period','90')"
                        class="btn btn-sm {{ $period === '90' ? 'btn-info ' : 'btn-outline-secondary' }}">
                        90 ngày
                    </button>
                </div>
                <span wire:loading wire:target="period" class="spinner-border spinner-border-sm text-info ms-2"
                    role="status"></span>
            </div>
        </div>
        <div class="card-body pt-3 pb-3">
            <canvas id="c-timeline-chart" height="70"></canvas>
        </div>
    </div>

    {{-- Bảng chi tiết --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-bottom py-3 d-flex align-items-center">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-pie text-muted me-2"></i>Chi Tiết Tồn Kho
                Theo Sản Phẩm</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="text-muted small text-uppercase tracking-wide">
                        <tr>
                            <th class="ps-4 py-3">Sản Phẩm</th>
                            <th class="text-center py-3">Mã Code</th>
                            <th class="text-end py-3">Số Lượng Sản Phẩm</th>
                            <th class="text-end pe-4 py-3">Tổng Khối Lượng (m)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockByProduct as $stat)
                            <tr>
                                <td class="ps-4 fw-medium">
                                    {{ optional($stat->product)->name ?? 'Không xác định' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">
                                        {{ optional($stat->product)->code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold  fs-5">
                                    {{ number_format($stat->total_items) }}
                                </td>
                                <td class="text-end pe-4 text-muted">
                                    {{ number_format($stat->total_length, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fs-1 text-light mb-3 d-block"></i>
                                    Hiện tại chứa có dữ liệu trong kho.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-up {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hover-up:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }

    .tracking-wide {
        letter-spacing: 0.05em;
    }
</style>

<script>
    (function() {
        let timelineChartData = @json($timelineChart ?? []);
        const _inst = {};

        function initChart(id, type, labels, datasets) {
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
                        }
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
                        }
                    }
                }
            });
        }

        function buildCharts() {
            if (typeof Chart === 'undefined') return;
            Chart.defaults.font.family = "'Inter','Roboto',sans-serif";
            Chart.defaults.color = '#64748b';

            if (timelineChartData && timelineChartData.labels) {
                initChart('c-timeline-chart', 'line', timelineChartData.labels, timelineChartData.datasets);
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

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh-charts', (payload) => {
                let data = Array.isArray(payload) ? payload[0] : payload;
                if (data && data.timelineChart) {
                    timelineChartData = data.timelineChart;
                }
                setTimeout(buildCharts, 100);
            });
        });
    })();
</script>
