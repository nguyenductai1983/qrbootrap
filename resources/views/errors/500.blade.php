<x-error-layout>
    <div class="container d-flex flex-column justify-content-center align-items-center text-center">

        {{-- Biểu tượng máy chủ --}}
        <div class="text-secondary mb-3">
            <i class="fa-solid fa-server fa-5x"></i>
        </div>

        {{-- Tiêu đề 500 --}}
        <h1 class="display-1 fw-bold text-secondary mb-0" style="font-size: 6rem;">500</h1>

        {{-- Lời nhắn --}}
        <h3 class="fw-bold text-dark mt-3 mb-2">Lỗi hệ thống!</h3>
        <p class="text-muted mb-4 fs-5" >
            Rất xin lỗi, máy chủ của chúng tôi đang gặp sự cố kỹ thuật hoặc quá tải xử lý. Đội ngũ IT đã nhận được thông
            báo và đang tiến hành khắc phục. Vui lòng thử lại sau ít phút.
        </p>
        @if ($exception->getMessage())
            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 mb-4 px-4 py-2" >
                <small class="fw-semibold">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Chi tiết: {{ $exception->getMessage() }}
                </small>
            </div>
        @endif
        {{-- Các nút điều hướng --}}
        <div class="d-flex justify-content-center gap-3">
            {{-- Nút Tải lại trang (Thử lại) --}}
            <button onclick="window.location.reload()" class="btn btn-outline-secondary btn-lg shadow-sm px-4">
                <i class="fa-solid fa-arrow-rotate-right me-2"></i> Thử lại ngay
            </button>

            {{-- Nút Về Trang chủ --}}
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg shadow-sm px-4" wire:navigate>
                <i class="fa-solid fa-house me-2"></i> Về Trang chủ
            </a>
        </div>

    </div>
</x-error-layout>
