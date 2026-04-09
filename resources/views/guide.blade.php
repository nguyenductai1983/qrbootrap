<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fa-solid fa-book-open me-2 text-primary"></i> {{ __('Hướng Dẫn Sử Dụng Hệ Thống') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="alert alert-info shadow-sm border-0 mb-4">
                <h5 class="alert-heading fw-bold"><i class="fa-solid fa-circle-info"></i> Xin chào!</h5>
                <p class="mb-0">Chào mừng bạn đến với hệ thống Quản lý Sản xuất & Tem Mã Vạch. Tài liệu này sẽ giúp
                    bạn làm quen với các chức năng một cách nhanh nhất. Hãy chọn mục phù hợp với công việc của bạn ở bên
                    dưới.</p>
            </div>

            <div class="row g-4">

                <div class="col-md-3">
                    <div class="list-group sticky-top" style="top: 20px; z-index: 1;">
                        <a href="#section-login" class="list-group-item list-group-item-action fw-bold active"
                            data-bs-toggle="list">
                            1. Đăng nhập & Tài khoản
                        </a>
                        <a href="#section-print" class="list-group-item list-group-item-action fw-bold"
                            data-bs-toggle="list">
                            2. Cách In Tem Mã Vạch
                        </a>
                        <a href="#section-excel" class="list-group-item list-group-item-action fw-bold"
                            data-bs-toggle="list">
                            3. Nhập liệu từ Excel
                        </a>
                        <a href="#section-scan" class="list-group-item list-group-item-action fw-bold"
                            data-bs-toggle="list">
                            4. Quét kiểm tra (Mobile)
                        </a>
                        <a href="#section-coating" class="list-group-item list-group-item-action fw-bold"
                            data-bs-toggle="list">
                            5. Xác nhận tráng (Mobile)
                        </a>
                        <a href="#section-warehouse" class="list-group-item list-group-item-action fw-bold text-success"
                            data-bs-toggle="list">
                            6. Nhập Kho & Vị Trí (Mobile)
                        </a>
                        <a href="#section-printstation" class="list-group-item list-group-item-action fw-bold text-info"
                            data-bs-toggle="list">
                            7. Trạm In Kiosk
                        </a>
                        @role('admin')
                            <a href="#section-admin" class="list-group-item list-group-item-action fw-bold text-danger"
                                data-bs-toggle="list">
                                8. Dành cho Quản lý
                            </a>
                        @endrole
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="section-login">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-primary m-0"><i class="fa-solid fa-user-shield me-2"></i>1.
                                        Đăng nhập hệ thống</h5>
                                </div>
                                <div class="card-body">
                                    <p>Để bắt đầu làm việc, bạn cần có tài khoản do quản lý cấp.</p>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <span class="badge bg-primary rounded-pill me-2">Bước 1</span> Truy cập vào
                                            địa chỉ trang web.
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge bg-primary rounded-pill me-2">Bước 2</span> Nhập
                                            <strong>Email</strong> và <strong>Mật khẩu</strong> của bạn.
                                        </li>
                                        <li class="list-group-item">
                                            <span class="badge bg-primary rounded-pill me-2">Bước 3</span> Nhấn nút
                                            <strong>Đăng nhập</strong>.
                                        </li>
                                    </ul>
                                    <div class="alert alert-warning mt-3 small">
                                        <i class="fa-solid fa-key me-1"></i> <strong>Quên mật khẩu?</strong> Hãy liên hệ
                                        với Quản lý hệ thống để được cấp lại mật khẩu mới.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-print">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-print me-2"></i>2. Cách In Tem Mã Vạch</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng dùng để tạo thẻ/tem thông tin dán lên từng sản phẩm (cây vải).</p>
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/ss_print.png" alt="Cách in tem mã vạch" class="img-fluid border rounded shadow-sm w-100">
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-primary">Quy trình thực hiện:</h6>
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2">Trên menu trái, chọn mục <strong><i class="fa-solid fa-print text-muted"></i> In Tem Mã Vạch</strong>.</li>
                                                <li class="mb-2"><strong>Bước 1:</strong> Chọn Sản phẩm, Loại Mã (BarCode hay QR Code) từ trình đơn thả xuống (Cột bên trái).</li>
                                                <li class="mb-2"><strong>Bước 2:</strong> Copy trực tiếp nội dung chi tiết cây vải (Từ Excel) và dán (Paste) thẳng vào "Ô Nội dung".</li>
                                                <li class="mb-2"><strong>Bước 3:</strong> Ấn nút <span class="badge bg-primary">Tạo Mới & In Ngay</span>. Dữ liệu sẽ vào máy in.</li>
                                            </ol>
                                            <div class="alert alert-secondary mt-3 small p-2">
                                                <i class="fa-solid fa-lightbulb text-warning"></i> <strong>Mẹo:</strong> Thứ tự cột copy từ Excel thường là: Tên/Màu, Đơn Hàng, Loại, Khổ, Nhựa, GSM... tùy vào thiết lập quy chuẩn.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-excel">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-file-excel me-2"></i>3. Nhập liệu từ Excel</h5>
                                </div>
                                <div class="card-body">
                                    <p>Giúp bạn cập nhật khối lượng, số mét, thay đổi thông số đồng loạt nhanh chóng. Phù hợp cho văn phòng hoặc quản đốc làm việc trên máy tính.</p>
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/ss_excel.png" alt="Nhập liệu bằng Excel" class="img-fluid border rounded shadow-sm w-100">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="step-guide">
                                                <div class="d-flex mb-3 align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; min-width: 30px;">1</div>
                                                    <div class="ms-3">
                                                        <strong>Tải Mẫu:</strong> Nhấn "Tải file mẫu" ở góc phải để lấy file chuẩn đã có danh sách toàn bộ các mã đang có.
                                                    </div>
                                                </div>
                                                <div class="d-flex mb-3 align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; min-width: 30px;">2</div>
                                                    <div class="ms-3">
                                                        <strong>Chỉnh sửa:</strong> Nhập số cân (kg), số mét vào Excel. <br><span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Không sửa cột "Code" (Mã)</span>.
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; min-width: 30px;">3</div>
                                                    <div class="ms-3">
                                                        <strong>Tải Lên:</strong> Chọn file vừa điền xong và bấm "Upload dữ liệu". 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-scan">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-warning m-0"><i class="fa-solid fa-mobile-screen-button me-2"></i>4. Sản Xuất & Quét Kiểm Tra (Mobile)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng này được tối ưu cho giao diện điện thoại, giúp bạn dễ dàng kiểm tra thông tin chi tiết mã vạch trực tiếp tại xưởng thông qua Camera điện thoại.</p>
                                    <div class="row mb-4">
                                        <div class="col-md-5 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/mobile_scan.png" alt="Giao diện Quét kiểm tra trên Mobile" class="img-fluid border rounded shadow-sm w-100" style="max-width: 250px;">
                                        </div>
                                        <div class="col-md-7">
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2">Truy cập vào mục <strong>Quét Kiểm Tra</strong> (hoặc đường dẫn <code class="text-primary">/production/scan-mobile</code>).</li>
                                                <li class="mb-2">Hệ thống sẽ yêu cầu quyền sử dụng Camera trên điện thoại, hãy bấm <strong>Cho phép</strong>.</li>
                                                <li class="mb-2">Đưa Camera hướng vào mã vạch/mã QR của sản phẩm (cây vải) cần kiểm tra sao cho mã nằm gọn trong khung quét.</li>
                                                <li>Thông tin chi tiết về sản phẩm (độ dài, tình trạng, lịch sử...) sẽ ngay lập tức được hiển thị bên dưới màn hình.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-coating">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-layer-group me-2"></i>5. Máy Tráng / Ghép Kéo Mộc (Mobile)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Nghiệp vụ ghép nhiều cây vải "Mộc" thành 1 cuộn "Tráng" thành phẩm. Giao diện được thiết kế để thao tác nhanh trên điện thoại hoặc máy tính bảng gắn tại xưởng (đường dẫn <code class="text-primary">/production/coating-confirmation</code>).</p>
                                    <div class="row mb-4">
                                        <div class="col-md-5 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/mobile_coating.png" alt="Giao diện Xác nhận Tráng trên Mobile" class="img-fluid border rounded shadow-sm w-100" style="max-width: 250px;">
                                        </div>
                                        <div class="col-md-7">
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2">Vào menu <strong>Xác Nhận Tráng</strong> và chọn Máy thực hiện trên màn hình thiết bị.</li>
                                                <li class="mb-2">Sử dụng chức năng quét mã qua camera điện thoại hoặc súng quét Bluetooth để quét các cuộn vải "Mộc" được đưa lên máy.</li>
                                                <li class="mb-2">Nhập trực tiếp <strong>số mét xuất</strong> cho mỗi cuộn mộc đã dùng ngay trên giao diện cảm ứng.</li>
                                                <li class="mb-2">Ghi <strong>số mét Thu Được</strong> cho cây Tráng đầu ra.</li>
                                                <li>Nhấn nút <span class="badge bg-success">TẠO MÃ & IN TEM</span>. Lệnh in sẽ tự động đẩy xuống máy Kiosk để in tem Tráng mới dán lên cuộn vừa làm ra.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-warehouse">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-warehouse me-2"></i>6. Nhập Kho &amp; Quản Lý Vị Trí (Mobile)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng kho được thiết kế đặc biệt tối ưu cho <strong>điện thoại di động</strong> (đường dẫn <code class="text-primary">/warehouse/scan-to-location</code>), giúp công nhân trực tiếp sử dụng điện thoại cá nhân lấy nét qua camera để nhập kho và sắp xếp kệ hàng tại xưởng.</p>
                                    
                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-1"></i> Các chế độ nhập kho</h6>
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-md-5 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/mobile_warehouse.png" alt="Giao diện Nhập kho trên Mobile" class="img-fluid border rounded shadow-sm w-100" style="max-width: 250px;">
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="list-group list-group-flush mb-0">
                                                <li class="list-group-item">
                                                    <span class="badge bg-secondary me-2">Nhập Tạm</span> Quét nhanh mã qua camera điện thoại để xác nhận cây vải đã về kho (không ghi nhận xếp lên kệ nào).
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="badge bg-success me-2">Nhập + Vị Trí</span> Hướng camera quét mã QR dán trên kệ hàng TRƯỚC, sau đó lần lượt quét mã các cây vải. Các cây vải này sẽ tự động gắn vào hệ thống thuộc về kệ vừa quét.
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="badge bg-info text-dark me-2">Xác Nhận Vị Trí</span> Phù hợp khi thao tác chuyển kệ trên điện thoại cho các hàng hoá đã trong kho.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-2"></i> Cân điện tử & Nhập trọng lượng</h6>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <p>Nếu xưởng của bạn có trang bị cân điện tử kết nối qua WiFi với hệ thống:</p>
                                            <ol class="mb-0">
                                                <li class="mb-2">Giao diện điện thoại sẽ có khu vực hiển thị số kg truyền trực tiếp (real-time) từ cân điện tử. Khi có trạng thái <span class="badge bg-success">Ổn định</span>, bạn có thể tự động chốt số liệu.</li>
                                                <li class="mb-2">Cũng có màn hình nhập tay dự phòng (trong trường hợp cân rớt mạng hoặc gặp trục trặc).</li>
                                                <li><strong>Cách làm siêu tốc trên mobile:</strong> Đặt cuộn vải lên cân -> Số nảy trên điện thoại -> Quét mã QR qua camera điện thoại -> Lưu thành công!</li>
                                            </ol>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-3"></i> Xem Lịch Sử / Danh Sách Nhập Kho</h6>
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <p>Ngay trên điện thoại, truy cập <strong>Danh Sách Nhập Kho</strong>, bạn có thể dễ dàng kiểm tra:</p>
                                            <ul class="mb-0">
                                                <li>Số mét, Lượng Kg, Vị trí kệ, Màu, Loại... của từng cây vải.</li>
                                                <li>Trích xuất dữ liệu người nào thực hiện thao tác cân nhập kho và chốt vào giờ nào.</li>
                                                <li>Các công cụ lọc hỗ trợ tra cứu nhanh <strong>Mã barcode</strong> hoặc <strong>Ngày nhập</strong> trên màn hình nhỏ di động.</li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: TRẠM IN --}}
                        <div class="tab-pane fade" id="section-printstation">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-info m-0"><i class="fa-solid fa-print me-2"></i>7. Trạm In Kiosk (Tự Động)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Giải pháp cho xưởng in tự động không cần người thao tác bấm máy: Máy tính cắm máy in luôn bật sẵn, tự động nhận lệnh từ mọi nơi.</p>
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0 text-center">
                                            <img src="/images/guide/ss_printstation.png" alt="Trạm in Kiosk tự động" class="img-fluid border rounded shadow-sm w-100">
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold">Cách máy in tự động hoạt động:</h6>
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2"><strong>Admin</strong> tạo các "Trạm In" và gán quyền trạm in cho một nhân viên cụ thể (Tài khoản Kiosk).</li>
                                                <li class="mb-2">Máy tính cắm máy in đăng nhập bằng tài khoản Kiosk đó, sau đó nhấp vào link <strong><a href="/production/print-station" target="_blank" class="text-decoration-none">Print Station Màn Hình Chờ</a></strong> và để y nguyên máy đó cả ngày.</li>
                                                <li>Mỗi khi bất kỳ máy nào khác trong xưởng bấm nút phát hành tem (Ví dụ ở máy Tráng), tín hiệu mạng ảo sẽ nhảy tới màn hình Kiosk và đẩy thẳng ra máy in. </li>
                                            </ol>
                                            <div class="alert alert-secondary mt-3 small p-2">
                                                *Lưu ý: Bật chế độ in âm thầm (SILENT PRINTING hoặc KIOSK mode) trên Chrome/Edge để tắt hộp thoại hỏi cấu hình trước khi in.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @role('admin')
                            <div class="tab-pane fade" id="section-admin">
                                <div class="card shadow-sm border-0 mb-4 border-danger">
                                    <div class="card-header bg-danger text-white py-3">
                                        <h5 class="fw-bold m-0"><i class="fa-solid fa-user-gear me-2"></i>8. Dành cho Quản Trị Viên</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Kiểm soát quy trình Cài Đặt Sản Xuất, Đơn hàng, Phân quyền hệ thống.</p>
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-5 mb-3 mb-md-0 text-center">
                                                <img src="/images/guide/ss_admin.png" alt="Chức năng Admin" class="img-fluid border rounded shadow-sm w-100">
                                            </div>
                                            <div class="col-md-7">
                                                <h6 class="fw-bold">Thiết lập dữ liệu ban đầu:</h6>
                                                <p>Để khởi chạy xưởng mới, bạn cần tạo thông số theou thứ tự chuẩn sau:</p>
                                                <ol class="ps-3 mb-0">
                                                    <li class="mb-2"><strong>Mô hình Sản xuất:</strong> Vào menu <strong>Sản phẩm</strong> tạo các Mode, sau đó qua các Menu <strong>Danh mục / Loại Tem</strong> để tinh chỉnh.</li>
                                                    <li class="mb-2"><strong>Đơn hàng (PO):</strong> Tạo mới Đơn Hàng và Liên kết (Map) các Sản Phẩm vào đơn hàng đó (Để lúc nhân viên In tem có thể bắt chéo Model + Đơn hàng).</li>
                                                    <li class="mb-2"><strong>Người & Máy:</strong> Vào menu Quản lý Users tạo tài khoản. Phân đúng phân loại vai trò (Role). Sau đó gán cho họ phụ trách Máy Tráng, Trạm Cân, Kiosk đúng người.</li>
                                                </ol>
                                                <hr>
                                                <p class="text-danger small"><i class="fa-solid fa-circle-exclamation me-1"></i> Nếu nhân viên đăng nhập thấy màn hình báo lỗi, nghĩa là tài khoản đó đang không được gắn bất kỳ Vai trò nào. Hãy qua menu Vai trò -> Edit -> Gán User.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endrole

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
