{{-- ĐỆ QUY LÊN TRÊN: TỔ TIÊN --}}
@if($type === 'parent' && $item->allParents && $item->allParents->isNotEmpty())
    @foreach($item->allParents as $olderParent)
        @include('livewire.production.partials.genealogy-node', [
            'item' => $olderParent, 
            'type' => 'parent',
            'level' => $level + 1
        ])
    @endforeach
@endif

<li class="genealogy-item is-{{ $type }}" style="margin-left: {{ $type === 'child' ? ($level * 1.5) . 'rem' : '0' }}">
    <div class="card genealogy-card is-{{ $type }} p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <span class="badge {{ $type === 'parent' ? 'bg-warning text-dark' : 'bg-success' }} mb-1">
                    <i class="fa-solid {{ $type === 'parent' ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i> 
                    {{ $type === 'parent' ? 'Nguồn gốc (Đời -'.$level.')' : 'Phát sinh (Đời +'.$level.')' }}
                </span>
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-barcode me-1"></i> {{ $item->code }}
                </h6>
            </div>
            <div class="text-end">
                <span class="badge {{ $item->status?->badge() ?? 'bg-secondary' }}">
                    {{ $item->status?->label() ?? 'Không rõ' }}
                </span>
                <div class="small text-muted mt-1"><i class="fa-regular fa-clock me-1"></i> {{ $item->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <div class="row g-2 small">
            <div class="col-md-3 col-6"><strong>Sản phẩm:</strong> {{ $item->product->name ?? '-' }}</div>
            <div class="col-md-3 col-6"><strong>Dài Gốc:</strong> {{ (float)$item->original_length }}m</div>
            <div class="col-md-3 col-6"><strong>Dài Còn:</strong> <span class="text-danger fw-bold">{{ (float)$item->length }}m</span></div>
            <div class="col-md-3 col-6"><strong>Khối lượng:</strong> {{ (float)$item->weight }}kg</div>
            
            <div class="col-md-3 col-6"><strong>Xưởng:</strong> {{ $item->department->name ?? '-' }}</div>
            <div class="col-md-3 col-6"><strong>Máy:</strong> {{ $item->machine->name ?? '-' }}</div>
            <div class="col-md-3 col-6"><strong>Người tạo:</strong> {{ $item->creator->name ?? '-' }}</div>
            <div class="col-md-3 col-6"><strong>Màu:</strong> {{ $item->color->name ?? '-' }}</div>
            
            @if(isset($item->pivot) && $item->pivot->used_length)
                <div class="col-12 mt-2">
                    <div class="p-2 border rounded bg-light text-primary">
                        <i class="fa-solid fa-scissors me-1"></i> Đã trích xuất <strong>{{ (float)$item->pivot->used_length }}m</strong> để tạo ra cây này
                        <span class="text-muted fst-italic ms-1">(Lúc: {{ \Carbon\Carbon::parse($item->pivot->created_at)->format('d/m/Y H:i') }})</span>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mt-3 text-end">
            {{-- Cho phép bấm phân tích tiếp cây con này làm Gốc --}}
            <button wire:click="traceItem({{ $item->id }})" class="btn btn-sm btn-outline-secondary me-2" title="Chuyển Nút này thành Gốc">
                <i class="fa-solid fa-expand"></i> Phân tích riêng
            </button>
            <button wire:click="reprintItems([{{ $item->id }}])" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-print me-1"></i> In Tem
            </button>
        </div>
    </div>
</li>

{{-- ĐỆ QUY XUỐNG DƯỚI: CON CHÁU --}}
@if($type === 'child' && $item->allChildren && $item->allChildren->isNotEmpty())
    @foreach($item->allChildren as $child)
        @include('livewire.production.partials.genealogy-node', [
            'item' => $child, 
            'type' => 'child',
            'level' => $level + 1
        ])
    @endforeach
@endif
