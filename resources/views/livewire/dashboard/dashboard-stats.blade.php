<div class="mb-5">

    {{-- ══════════════════════════════════════════
         BỘ LỌC THỜI GIAN
    ══════════════════════════════════════════ --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 border-start border-4 border-primary ps-3">
                <i class="fa-solid fa-chart-line me-2 text-primary"></i>Bảng Số Liệu Tổng Quan
            </h4>
            <small class="text-muted ps-3">Cập nhật theo thời gian thực · dữ liệu từ hệ thống sản xuất</small>
        </div>
        <div class="btn-group" role="group">
            <button wire:click="$set('period', '7')"
                class="btn btn-sm {{ $period === '7' ? 'btn-primary' : 'btn-outline-primary' }}">7 ngày</button>
            <button wire:click="$set('period', '30')"
                class="btn btn-sm {{ $period === '30' ? 'btn-primary' : 'btn-outline-primary' }}">30 ngày</button>
            <button wire:click="$set('period', '90')"
                class="btn btn-sm {{ $period === '90' ? 'btn-primary' : 'btn-outline-primary' }}">90 ngày</button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        @foreach ($kpis as $kpi)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 kpi-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="bg-{{ $kpi['color'] }} bg-opacity-10 rounded p-2">
                                <i class="{{ $kpi['icon'] }} text-{{ $kpi['color'] }} fa-lg"></i>
                            </div>
                            @if ($kpi['change'] && $kpi['change']['pct'] !== null)
                                <span class="badge {{ $kpi['change']['up'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill small">
                                    <i class="fa-solid {{ $kpi['change']['up'] ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                                    {{ $kpi['change']['pct'] }}%
                                </span>
                            @endif
                        </div>
                        <div class="kpi-value fw-bold text-{{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
                        <div class="kpi-label text-muted small mt-1">{{ $kpi['title'] }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">trong {{ $period }} ngày qua · {{ $kpi['unit'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════
         ROW 1: Xu hướng sản xuất + Phân bổ trạng thái
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">
        {{-- Line Chart: Tem tạo mỗi ngày --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>Xu Hướng Sản Xuất
                    </h6>
                    <small class="text-muted">Số tem tạo mỗi ngày</small>
                </div>
                <div class="card-body">
                    <canvas id="chart-production-trend" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Doughnut: Trạng thái Item --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="fa-solid fa-circle-half-stroke text-warning me-2"></i>Phân Bổ Trạng Thái
                    </h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chart-status-dist" height="220" style="max-height: 220px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         ROW 2: Theo sản phẩm + Tồn kho
    ══════════════════════════════════════════ --}}
    <div class="row g-3 mb-3">
        {{-- Bar Chart: Theo sản phẩm --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="fa-solid fa-layer-group text-success me-2"></i>Sản Xuất Theo Model
                    </h6>
                    <small class="text-muted">Top 8 model</small>
                </div>
                <div class="card-body">
                    <canvas id="chart-by-product" height="160"></canvas>
                </div>
            </div>
        </div>

        {{-- Bar Chart: Tồn kho theo vị trí --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="fa-solid fa-map-location-dot text-info me-2"></i>Tồn Kho Theo Vị Trí
                    </h6>
                    <small class="text-muted">Top 10 kệ hàng</small>
                </div>
                <div class="card-body">
                    <canvas id="chart-warehouse" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         ROW 3: Xu hướng Tráng
    ══════════════════════════════════════════ --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="fa-solid fa-fire text-danger me-2"></i>Xu Hướng Ca Tráng
                    </h6>
                    <small class="text-muted">Số ca xác nhận tráng mỗi ngày</small>
                </div>
                <div class="card-body">
                    <canvas id="chart-coating-trend" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     STYLES
══════════════════════════════════════════ --}}
<style>
.kpi-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1) !important;
}
.kpi-value {
    font-size: 1.75rem;
    line-height: 1;
}
.kpi-label {
    font-size: 0.82rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>

{{-- ══════════════════════════════════════════
     SCRIPTS — Chart.js (CDN) + init
══════════════════════════════════════════ --}}
<script>
(function () {
    // Palette màu đẹp
    const PALETTE = ['#4361ee','#3bc99a','#f59e0b','#06b6d4','#8b5cf6','#ef4444','#10b981','#f97316'];
    const PALETTE_PASTEL = PALETTE.map(c => c + '33'); // 20% opacity

    // Register plugin nếu cần
    if (typeof Chart === 'undefined') {
        const s = document.createElement('script');
        s.src = '{{ asset("js/chart.umd.min.js") }}';
        s.onload = () => initCharts();
        document.head.appendChild(s);
    } else {
        initCharts();
    }

    function initCharts() {
        Chart.defaults.font.family = "'Inter', 'Roboto', sans-serif";
        Chart.defaults.color = '#6b7280';

        const productionData = @json(json_decode($productionTrendJson));
        const byProductData  = @json(json_decode($byProductJson));
        const statusData     = @json(json_decode($statusDistJson));
        const warehouseData  = @json(json_decode($warehouseJson));
        const coatingData    = @json(json_decode($coatingTrendJson));

        // 1. Line — Xu hướng sản xuất
        makeChart('chart-production-trend', 'line', productionData.labels, [{
            label: 'Tem tạo',
            data: productionData.data,
            borderColor: '#4361ee',
            backgroundColor: 'rgba(67,97,238,0.08)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointHoverRadius: 6,
        }]);

        // 2. Doughnut — Trạng thái
        makeChart('chart-status-dist', 'doughnut', statusData.labels, [{
            data: statusData.data,
            backgroundColor: ['#94a3b8','#4361ee','#3bc99a'],
            borderWidth: 2,
            borderColor: '#fff',
        }], { plugins: { legend: { position: 'bottom' } } });

        // 3. Bar — Theo sản phẩm
        makeChart('chart-by-product', 'bar', byProductData.labels, [{
            label: 'Số tem',
            data: byProductData.data,
            backgroundColor: byProductData.labels?.map((_, i) => PALETTE[i % PALETTE.length] + 'cc'),
            borderRadius: 6,
        }]);

        // 4. Bar — Tồn kho
        makeChart('chart-warehouse', 'bar', warehouseData.labels, [{
            label: 'Số cây',
            data: warehouseData.data,
            backgroundColor: '#3bc99acc',
            borderRadius: 6,
        }]);

        // 5. Line — Coating trend
        makeChart('chart-coating-trend', 'line', coatingData.labels, [{
            label: 'Ca tráng',
            data: coatingData.data,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239,68,68,0.07)',
            tension: 0.4,
            fill: true,
            pointRadius: 3,
        }]);
    }

    const _instances = {};
    function makeChart(id, type, labels, datasets, extraOpts = {}) {
        const ctx = document.getElementById(id);
        if (!ctx) return;
        if (_instances[id]) { _instances[id].destroy(); }
        _instances[id] = new Chart(ctx, {
            type,
            data: { labels: labels ?? [], datasets },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: datasets.length > 1 || type === 'doughnut' },
                    tooltip: { cornerRadius: 8 },
                    ...extraOpts.plugins,
                },
                scales: type !== 'doughnut' ? {
                    x: { grid: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, beginAtZero: true },
                } : {},
                ...extraOpts,
            },
        });
    }

    // Re-init khi Livewire cập nhật (period đổi)
    document.addEventListener('livewire:updated', initCharts);
})();
</script>
