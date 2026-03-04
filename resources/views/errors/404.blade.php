<x-error-layout>
    <div class="container d-flex flex-column justify-content-center align-items-center text-center">

        {{-- Tiêu đề 404 siêu to --}}
        <h1 class="display-1 fw-bold text-primary" style="font-size: 8rem;">404</h1>

        {{-- Lời nhắn --}}
        <h3 class="fw-semibold text-dark">Ôi hỏng! Lạc đường rồi.</h3>
        <p class="text-muted fs-5" style="max-width: 500px;">
            Trang bạn đang tìm kiếm không tồn tại, đã bị xóa, hoặc bạn đã gõ sai đường dẫn. Hãy kiểm tra lại nhé!
        </p>

        {{-- Các nút điều hướng --}}
        <div class="d-flex justify-content-center gap-3">
            {{-- Nút Quay lại trang trước --}}
            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg shadow-sm px-4">
                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
            </button>

            {{-- Nút Về trang chủ / Dashboard --}}
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg shadow-sm px-4" wire:navigate>
                <i class="fa-solid fa-house me-2"></i> Về Trang chủ
            </a>
        </div>

    </div>
</x-error-layout>
