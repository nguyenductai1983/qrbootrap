{{-- resources/views/livewire/department-form.blade.php --}}
<div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="h5 card-title mb-4">{{ __($title) }}</h3>

            <form wire:submit.prevent="saveDepartment">
                {{-- @csrf --}}

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Tên Bộ phận') }}</label>
                    <input wire:model="name" type="text" id="name"
                        class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="code" class="form-label">{{ __('Mã Bộ phận') }}</label>
                    <input wire:model="code" type="text" id="code"
                        class="form-control @error('code') is-invalid @enderror" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <-- Trường gán Sản phẩm --> --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('Sản phẩm phụ trách') }}</label>
                    <div class="row">
                        @foreach ($allProducts as $productOption)
                            <div class="col-md-4 col-sm-6 col-12">
                                <div class="form-check">
                                    <input wire:model="selectedProducts" class="form-check-input" type="checkbox"
                                        value="{{ $productOption->id }}" id="product-{{ $productOption->id }}">
                                    <label class="form-check-label" for="product-{{ $productOption->id }}">
                                        {{ $productOption->code }} - {{ $productOption->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedProducts')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                    @error('selectedProducts.*')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary me-2">
                        {{ __('Hủy') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Lưu') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
