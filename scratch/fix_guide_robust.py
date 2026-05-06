
import os

file_path = r'resources\views\guide.blade.php'

def fix_file():
    with open(file_path, 'rb') as f:
        content_bytes = f.read()
    
    try:
        content = content_bytes.decode('utf-8')
        encoding = 'utf-8'
    except UnicodeDecodeError:
        content = content_bytes.decode('cp1258')
        encoding = 'cp1258'

    # --- 1. Fix Sidebar ---
    # We want: Item 7, then Item 9, then Role Admin (Item 8)
    sidebar_item_7 = """                        <a href="#section-printstation" class="list-group-item list-group-item-action fw-bold text-info"
                            data-bs-toggle="list">
                            7. Trạm In Kiosk
                        </a>"""
    
    sidebar_item_9 = """
                        <a href="#section-mobile-guide" class="list-group-item list-group-item-action fw-bold text-primary bg-light"
                            data-bs-toggle="list">
                            <i class="fa-solid fa-graduation-cap me-2"></i> 9. Tài liệu Đào tạo Mobile
                        </a>"""

    # Remove misplaced tab-pane from sidebar area
    # Misplaced part starts with {{-- SECTION: MOBILE GUIDE --}} and ends with </div> just before @role('admin')
    misplaced_start = "{{-- SECTION: MOBILE GUIDE --}}"
    misplaced_end = "@role('admin')"
    
    start_idx = content.find(misplaced_start)
    end_idx = content.find(misplaced_end, start_idx)
    
    if start_idx != -1 and end_idx != -1:
        # Check if it's the one in the sidebar (should be early in the file)
        if start_idx < 10000: # Sidebar is usually at the beginning
            content = content[:start_idx] + content[end_idx:]

    # Ensure Sidebar Item 9 is present
    if "#section-mobile-guide" not in content[:5000]:
        idx = content.find(sidebar_item_7)
        if idx != -1:
            end_of_7 = idx + len(sidebar_item_7)
            content = content[:end_of_7] + sidebar_item_9 + content[end_of_7:]

    # --- 2. Fix Sections 5 & 6 ---
    # Marker: end of section 4
    section_4_end = """                            </div>
                        </div>""" # This is very generic, let's be more specific
    
    # Let's find section-scan's closing div
    scan_start = content.find('id="section-scan"')
    if scan_start != -1:
        # Find the end of this tab-pane
        # It's usually the next </div>\n                        </div>
        search_from = scan_start
        for _ in range(3): # card-body, card, tab-pane
             search_from = content.find('</div>', search_from + 1)
        
        # The next one should be the end of tab-pane
        pane_end = content.find('</div>', search_from + 1)
        
        if pane_end != -1:
            pane_end += 6 # include </div>
            
            # Now find where section 7 starts
            section_7_start = content.find('{{-- SECTION: TRẠM IN --}}')
            
            if section_7_start != -1:
                # Replace everything between pane_end and section_7_start with clean sections 5 and 6
                new_sections_5_6 = """
                        <div class="tab-pane fade" id="section-coating">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i
                                            class="fa-solid fa-layer-group me-2"></i>5. Máy Tráng / Ghép Kéo Mộc
                                        (Mobile)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Nghiệp vụ ghép nhiều cây vải "Mộc" thành 1 cuộn "Tráng" thành phẩm. Giao diện
                                        được thiết kế để thao tác nhanh trên điện thoại hoặc máy tính bảng gắn tại xưởng
                                        (đường dẫn <code class="text-primary">/production/coating-confirmation</code>).
                                    </p>
                                    <div class="row mb-4">
                                        <div class="col-md-5 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/mobile_coating.png"
                                                alt="Giao diện Xác nhận Tráng trên Mobile"
                                                class="img-fluid border rounded shadow-sm w-100"
                                                style="max-width: 250px;">
                                        </div>
                                        <div class="col-md-7">
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2">Vào menu <strong>Xác Nhận Tráng</strong> và chọn Máy
                                                    thực hiện trên màn hình thiết bị.</li>
                                                <li class="mb-2">Sử dụng chức năng quét mã qua camera điện thoại hoặc
                                                    súng quét Bluetooth để quét các cuộn vải.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-warehouse">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i
                                            class="fa-solid fa-warehouse me-2"></i>6. Nhập Kho &amp; Quản Lý Vị Trí
                                        (Mobile)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Giao diện Nhập kho mới được thiết kế theo quy trình <strong>3 bước tối ưu cho
                                            điện thoại</strong> (đường dẫn <code
                                            class="text-primary">/warehouse/scan-to-location</code>). Nhân viên có thể sử
                                        dụng camera điện thoại hoặc súng quét Bluetooth để thao tác thuận tiện ngay tại
                                        kệ hàng.</p>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-1"></i> Quy trình 3
                                        Bước Siêu Tốc</h6>
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-md-5 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/mobile_warehouse.png"
                                                alt="Giao diện Nhập kho trên Mobile"
                                                class="img-fluid border rounded shadow-sm w-100"
                                                style="max-width: 250px;">
                                        </div>
                                        <div class="col-md-7">
                                            <div class="list-group list-group-flush mb-0">
                                                <div class="list-group-item px-0 py-2">
                                                    <span class="badge bg-primary rounded-pill me-2">B1</span>
                                                    <strong>Cấu hình:</strong> Chọn Chế độ nhập và Chọn Trạm Cân (nếu có
                                                    kết nối WiFi).
                                                </div>
                                                <div class="list-group-item px-0 py-2">
                                                    <span class="badge bg-primary rounded-pill me-2">B2</span>
                                                    <strong>Vị trí:</strong> Quét mã QR dán trên kệ hàng (Bước này sẽ
                                                    được bỏ qua nếu ở chế độ Nhập Tạm).
                                                </div>
                                                <div class="list-group-item px-0 py-2">
                                                    <span class="badge bg-primary rounded-pill me-2">B3</span>
                                                    <strong>Quét mã:</strong> Hướng camera quét mã tem trên cây vải. Hệ
                                                    thống sẽ tự động ghi nhận Trọng lượng (từ cân) và Vị trí (từ B2).
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                            class="fa-solid fa-layer-group me-2"></i> Các Chế Độ Hoạt Động</h6>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="border rounded p-3 h-100 bg-light">
                                                        <div class="fw-bold text-secondary mb-2"><i
                                                                class="fa-solid fa-inbox me-1"></i> Nhập Tạm</div>
                                                        <small class="text-muted">Dùng khi muốn xác nhận hàng đã về kho
                                                            nhanh chóng mà chưa kịp sắp xếp lên kệ. Không yêu cầu quét mã
                                                            vị trí ở Bước 2.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border rounded p-3 h-100 bg-light">
                                                        <div class="fw-bold text-success mb-2"><i
                                                                class="fa-solid fa-location-dot me-1"></i> Nhập + Vị
                                                            Trí</div>
                                                        <small class="text-muted">Chế độ chuẩn: Quét kệ hàng trước, sau
                                                            đó quét các cây vải. Hàng sẽ được tự động gắn vào vị trí kệ
                                                            đó.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border rounded p-3 h-100 bg-light">
                                                        <div class="fw-bold text-info mb-2"><i
                                                                class="fa-solid fa-map-pin me-1"></i> Xác Nhận Vị
                                                            Trí</div>
                                                        <small class="text-muted">Dùng khi thực hiện đảo kệ, chuyển vị
                                                            trí cho các cây vải đã có sẵn trong kho. Chỉ cập nhật vị trí,
                                                            không đổi trạng thái hàng.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                            class="fa-solid fa-weight-scale me-2"></i> Cân Real-time & Cập nhật cân</h6>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <p>Hệ thống tự động kết nối với Trạm Cân qua WebSocket để lấy dữ liệu cân
                                                thời gian thực:</p>
                                            <ul class="mb-0">
                                                <li class="mb-2"><strong>Tự động lấy cân:</strong> Khi số cân ở trạng
                                                    thái <span class="badge bg-success">Ổn định</span>, hệ thống sẽ tự
                                                    dùng số đó cho cây vải bạn vừa quét.</li>
                                                <li class="mb-2"><strong>Tái nhập dư:</strong> Nếu bạn quét một cây
                                                    vải đã trong kho nhưng có trọng lượng mới (đã sả bớt), hệ thống sẽ
                                                    tự nhận diện và thực hiện nghiệp vụ "Tái nhập dư".</li>
                                                <li><strong>Nhập tay:</strong> Luôn có ô nhập trọng lượng tay dự phòng
                                                    nếu trạm cân mất kết nối.</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                            class="fa-solid fa-clock-rotate-left me-2"></i> Lịch Sử Phiên Quét</h6>
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <p>Phía dưới màn hình điện thoại luôn hiển thị danh sách 20 cây vải vừa thực
                                                hiện gần nhất trong phiên làm việc, giúp bạn dễ dàng đối soát số lượng
                                                kg và vị trí vừa quét mà không cần quay lại danh sách tổng.</p>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-3"></i> Xem Lịch Sử /
                                        Danh Sách Nhập Kho</h6>
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <p>Ngay trên điện thoại, truy cập <strong>Danh Sách Nhập Kho</strong>, bạn
                                                có thể dễ dàng kiểm tra:</p>
                                            <ul class="mb-0">
                                                <li>Số mét, Lượng Kg, Vị trí kệ, Màu, Loại... của từng cây vải.</li>
                                                <li>Trích xuất dữ liệu người nào thực hiện thao tác cân nhập kho và chốt
                                                    vào giờ nào.</li>
                                                <li>Các công cụ lọc hỗ trợ tra cứu nhanh <strong>Mã barcode</strong> hoặc
                                                    <strong>Ngày nhập</strong> trên màn hình nhỏ di động.</li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        """
                content = content[:pane_end] + new_sections_5_6 + content[section_7_start:]

    # --- 3. Ensure Section 9 Tab Pane is at the end of tab-content ---
    # Marker: just before @role('admin') at the end
    last_admin = content.rfind("@role('admin')")
    if last_admin != -1:
        # Check if mobile-guide is already there
        if 'id="section-mobile-guide"' not in content[last_admin-2000:]:
             mobile_guide_pane = """
                        {{-- SECTION: MOBILE GUIDE --}}
                        <div class="tab-pane fade" id="section-mobile-guide">
                            <div class="card shadow-sm border-0 mb-4 border-primary">
                                <div class="card-header bg-primary text-white py-3">
                                    <h5 class="fw-bold m-0"><i class="fa-solid fa-mobile-screen me-2"></i>9. Tài liệu Đào tạo Mobile (Slide Deck)</h5>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <i class="fa-solid fa-file-powerpoint fa-4x text-danger opacity-25"></i>
                                        <i class="fa-solid fa-arrow-right fa-2x mx-3 text-muted"></i>
                                        <i class="fa-solid fa-mobile-screen-button fa-5x text-primary"></i>
                                    </div>
                                    <h4 class="fw-bold mb-3">Hướng dẫn từng bước dành cho Nhân viên Sản xuất</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">
                                        Đây là bộ tài liệu được thiết kế tối ưu cho di động, giúp bạn dễ dàng theo dõi từng bước thực hiện các thao tác tại xưởng (Scan, Tráng, Nhập kho).
                                    </p>
                                    
                                    <div class="d-grid gap-3 col-md-6 mx-auto">
                                        <a href="/mobile_guide.html" target="_blank" class="btn btn-primary btn-lg shadow">
                                            <i class="fa-solid fa-up-right-from-square me-2"></i> Mở Tài liệu Đào tạo (Toàn màn hình)
                                        </a>
                                        <a href="/mobile_guide.md" target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="fa-solid fa-file-code me-2"></i> Xem mã nguồn Markdown
                                        </a>
                                    </div>

                                    <div class="alert alert-info mt-5 d-inline-block text-start shadow-sm border-0">
                                        <h6 class="fw-bold mb-2"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Mẹo sử dụng:</h6>
                                        <ul class="small mb-0">
                                            <li>Sử dụng phím mũi tên <strong>Trái/Phải</strong> hoặc <strong>Vuốt màn hình</strong> để chuyển Slide.</li>
                                            <li>Nhấn phím <strong>F</strong> để xem toàn màn hình.</li>
                                            <li>Nhấn phím <strong>O</strong> để xem chế độ tổng quan (Overview).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        """
             content = content[:last_admin] + mobile_guide_pane + content[last_admin:]

    with open(file_path, 'w', encoding=encoding) as f:
        f.write(content)
    
    print("Success")

fix_file()
