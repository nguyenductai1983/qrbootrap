<div class="container-fluid py-4">
    <style>
        .genealogy-timeline {
            position: relative;
            padding-left: 2rem;
            list-style: none;
        }
        .genealogy-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            bottom: 20px;
            left: 0.75rem;
            width: 3px;
            background: #cbd5e1;
            border-radius: 3px;
        }
        .genealogy-item {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .genealogy-item::before {
            content: '';
            position: absolute;
            left: -1.7rem;
            top: 1rem;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #64748b;
            z-index: 1;
        }
        .genealogy-item.is-root::before {
            border-color: #3b82f6;
            width: 20px;
            height: 20px;
            left: -1.8rem;
            top: 0.8rem;
        }
        .genealogy-card {
            border-left: 4px solid #64748b;
            transition: all 0.2s ease;
        }
        .genealogy-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
        .genealogy-card.is-root {
            border-left: 5px solid #3b82f6;
            background-color: #eff6ff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .genealogy-card.is-child {
            border-left: 4px solid #10b981;
        }
        .genealogy-item.is-child::before {
            border-color: #10b981;
        }
        .genealogy-card.is-parent {
            border-left: 4px solid #f59e0b;
        }
        .genealogy-item.is-parent::before {
            border-color: #f59e0b;
        }
    </style>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa-solid fa-code-merge me-2"></i> Phân tích Nguồn gốc Phả hệ
            </h5>
            <a href="{{ route('manager.items') }}" class="btn btn-sm btn-light fw-bold text-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
        <div class="card-body bg-light">
            
            <ul class="genealogy-timeline m-0">

                {{-- NGƯỢC DÒNG: TỔ TIÊN (PARENTS) --}}
                @if($rootItem->allParents->isNotEmpty())
                    @foreach($rootItem->allParents as $parent)
                        @include('livewire.production.partials.genealogy-node', [
                            'item' => $parent, 
                            'type' => 'parent',
                            'level' => 1
                        ])
                    @endforeach
                @endif

                {{-- NÚT GỐC (ROOT) --}}
                <li class="genealogy-item is-root">
                    <div class="card genealogy-card is-root p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-primary mb-1"><i class="fa-solid fa-crosshairs me-1"></i> Gốc hiện tại</span>
                                <h5 class="mb-0 fw-bold text-primary">
                                    <i class="fa-solid fa-barcode me-1"></i> {{ $rootItem->code }}
                                </h5>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $rootItem->status?->badge() ?? 'bg-secondary' }}">
                                    {{ $rootItem->status?->label() ?? 'Không rõ' }}
                                </span>
                                <div class="small text-muted mt-1"><i class="fa-regular fa-clock me-1"></i> {{ $rootItem->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-md-3 col-6"><strong>Sản phẩm:</strong> {{ $rootItem->product->name ?? '-' }}</div>
                            <div class="col-md-3 col-6"><strong>Dài Gốc:</strong> {{ (float)$rootItem->original_length }}m</div>
                            <div class="col-md-3 col-6"><strong>Dài Còn:</strong> <span class="text-danger fw-bold">{{ (float)$rootItem->length }}m</span></div>
                            <div class="col-md-3 col-6"><strong>Khối lượng:</strong> {{ (float)$rootItem->weight }}kg</div>
                            
                            <div class="col-md-3 col-6"><strong>Xưởng:</strong> {{ $rootItem->department->name ?? '-' }}</div>
                            <div class="col-md-3 col-6"><strong>Máy:</strong> {{ $rootItem->machine->name ?? '-' }}</div>
                            <div class="col-md-3 col-6"><strong>Người tạo:</strong> {{ $rootItem->creator->name ?? '-' }}</div>
                            <div class="col-md-3 col-6"><strong>Màu:</strong> {{ $rootItem->color->name ?? '-' }}</div>
                            
                            @if(is_array($rootItem->properties))
                                @if(isset($rootItem->properties['LAMI']) && $rootItem->properties['LAMI'] > 0)
                                    <div class="col-12 mt-2"><span class="badge bg-dark">Lami: {{ $rootItem->properties['LAMI'] }}</span></div>
                                @endif
                            @endif
                        </div>
                        <div class="mt-3 text-end">
                            <button wire:click="reprintItems([{{ $rootItem->id }}])" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-print me-1"></i> In Tem</button>
                        </div>
                    </div>
                </li>

                {{-- XUÔI DÒNG: CON CHÁU (CHILDREN) --}}
                @if($rootItem->allChildren->isNotEmpty())
                    @foreach($rootItem->allChildren as $child)
                        @include('livewire.production.partials.genealogy-node', [
                            'item' => $child, 
                            'type' => 'child',
                            'level' => 1
                        ])
                    @endforeach
                @else
                    <li class="genealogy-item">
                        <div class="text-muted fst-italic px-3 py-2">
                            <i class="fa-solid fa-level-down-alt me-2 text-secondary"></i>
                            Cây vải này chưa được cắt hoặc tráng thành cây con nào.
                        </div>
                    </li>
                @endif
            </ul>

        </div>
    </div>
</div>
