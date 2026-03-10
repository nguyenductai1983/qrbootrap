<x-error-layout>
    <div class="container d-flex flex-column justify-content-center align-items-center text-center">

        {{-- Biểu tượng và Tiêu đề 403 --}}
        <div class="text-danger">
            <i class="fa-solid fa-shield-halved fa-5x"></i>
        </div>
        <h1 class="display-1 fw-bold text-danger mb-0" style="font-size: 6rem;">403</h1>

        {{-- Lời nhắn --}}
        <h3 class="fw-bold text-dark mt-3 mb-2">Truy cập bị từ chối!</h3>
        <p class="text-muted mb-4 fs-5" style="max-width: 550px;">
            Xin lỗi, bạn không có đủ quyền hạn (Permission) để xem trang này hoặc thực hiện thao tác này. Vui lòng liên hệ Quản trị viên nếu đây là một sự nhầm lẫn.
        </p>

        {{-- Cảnh báo bổ sung (Tùy chọn) --}}
        @if($exception->getMessage())
            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 mb-4 px-4 py-2" style="max-width: 550px;">
                <small class="fw-semibold">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Chi tiết: {{ $exception->getMessage() }}
                </small>
            </div>
        @endif

        {{-- Các nút điều hướng --}}
        <div class="d-flex justify-content-center gap-3">
            {{-- Nút Quay lại trang trước --}}
            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg shadow-sm px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
            </button>

            {{-- Nút Về Dashboard --}}
            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg shadow-sm px-4" wire:navigate>
                <i class="fa-solid fa-gauge me-2"></i> Về Dashboard
            </a>
        </div>

    </div>
</x-error-layout>
