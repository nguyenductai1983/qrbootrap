<x-app-layout> <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"> <i
                    class="fa-solid fa-book-open me-2 text-primary"></i> {{ __('Hướng Dẫn Sử Dụng Hệ Thống') }} </h2> <a
                href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"> <i
                    class="fa-solid fa-arrow-left"></i> Quay lại </a>
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
                    <div class="list-group sticky-top" style="top: 20px; z-index: 1;"> <a href="#section-login"
                            class="list-group-item list-group-item-action fw-bold active" data-bs-toggle="list"> 1. Đăng
                            nhập & Tài khoản </a> <a href="#section-print"
                            class="list-group-item list-group-item-action fw-bold" data-bs-toggle="list"> 2. Cách In Tem
                            Mã Vạch </a> <a href="#section-excel" class="list-group-item list-group-item-action fw-bold"
                            data-bs-toggle="list"> 3. Nhập liệu từ Excel </a> <a href="#section-scan"
                            class="list-group-item list-group-item-action fw-bold" data-bs-toggle="list"> 4. Quét kiểm
                            tra (Mobile) </a> <a href="#section-coating"
                            class="list-group-item list-group-item-action fw-bold" data-bs-toggle="list"> 5. Xác nhận
                            tráng (Mobile) </a> <a href="#section-warehouse"
                            class="list-group-item list-group-item-action fw-bold text-success" data-bs-toggle="list">
                            6. Nhập Kho & Vị Trí (Mobile) </a> <a href="#section-printstation"
                            class="list-group-item list-group-item-action fw-bold text-info" data-bs-toggle="list"> 7.
                            Trạm In Kiosk </a> @role('admin')
                            <a href="#section-admin" class="list-group-item list-group-item-action fw-bold text-danger"
                                data-bs-toggle="list"> 8. Dành cho Quản lý </a>
                        @endrole
                        <a href="#section-mobile-guide"
                            class="list-group-item list-group-item-action fw-bold text-primary bg-light"
                            data-bs-toggle="list"> <i class="fa-solid fa-graduation-cap me-2"></i> 9. Tài liệu Đào tạo
                            Mobile </a>
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
                                        <li class="list-group-item"> <span
                                                class="badge bg-primary rounded-pill me-2">Bước 1</span> Truy cập vào
                                            địa chỉ trang web. </li>
                                        <li class="list-group-item"> <span
                                                class="badge bg-primary rounded-pill me-2">Bước 2</span> Nhập
                                            <strong>Email</strong> và <strong>Mật khẩu</strong> của bạn.
                                        </li>
                                        <li class="list-group-item"> <span
                                                class="badge bg-primary rounded-pill me-2">Bước 3</span> Nhấn nút
                                            <strong>Đăng nhập</strong>.
                                        </li>
                                    </ul>
                                    <div class="alert alert-warning mt-3 small"> <i class="fa-solid fa-key me-1"></i>
                                        <strong>Quên mật khẩu?</strong> Hãy liên hệ với Quản lý hệ thống để được cấp lại
                                        mật khẩu mới.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="section-print">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-print me-2"></i>2. Cách
                                        In Tem Mã Vạch</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng dùng để tạo thẻ/tem thông tin dán lên từng sản phẩm (cây vải).</p>
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0 text-center"> <img
                                                src="/images/guide/ss_print.png" alt="Cách in tem mã vạch"
                                                class="img-fluid border rounded shadow-sm w-100"> </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold text-primary">Quy trình thực hiện:</h6>
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2">Trên menu trái, chọn mục <strong><i
                                                            class="fa-solid fa-print text-muted"></i> In Tem Mã
                                                        Vạch</strong>.</li>
                                                <li class="mb-2"><strong>Bước 1:</strong> Chọn Sản phẩm, Loại Mã
                                                    (BarCode hay QR Code) từ trình đơn thả xuống (Cột bên trái).</li>
                                                <li class="mb-2"><strong>Bước 2:</strong> Copy trực tiếp nội dung chi
                                                    tiết cây vải (Từ Excel) và dán (Paste) thẳng vào "Ô Nội dung".</li>
                                                <li class="mb-2"><strong>Bước 3:</strong> Ấn nút <span
                                                        class="badge bg-primary">Tạo Mới & In Ngay</span>. Dữ liệu sẽ
                                                    vào máy in.</li>
                                            </ol>
                                            <div class="alert alert-secondary mt-3 small p-2"> <i
                                                    class="fa-solid fa-lightbulb text-warning"></i>
                                                <strong>Mẹo:</strong> Thứ tự cột copy từ Excel thường là: Tên/Màu, Đơn
                                                Hàng, Loại, Khổ, Nhựa, GSM... tùy vào thiết lập quy chuẩn.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="section-excel">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-file-excel me-2"></i>3.
                                        Nhập liệu từ Excel</h5>
                                </div>
                                <div class="card-body">
                                    <p>Giúp bạn cập nhật khối lượng, số mét, thay đổi thông số đồng loạt nhanh chóng.
                                        Phù hợp cho văn phòng hoặc quản đốc làm việc trên máy tính.</p>
                                    <div class="row align-items-center mb-4">
                                        <div class="col-md-6 mb-3 mb-md-0 text-center"> <img
                                                src="/images/guide/ss_excel.png" alt="Nhập liệu bằng Excel"
                                                class="img-fluid border rounded shadow-sm w-100"> </div>
                                        <div class="col-md-6">
                                            <div class="step-guide">
                                                <div class="d-flex mb-3 align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                        style="width: 30px; height: 30px; min-width: 30px;">1</div>
                                                    <div class="ms-3"> <strong>Tải Mẫu:</strong> Nhấn "Tải file mẫu"
                                                        ở góc phải để lấy file chuẩn đã có danh sách toàn bộ các mã đang
                                                        có. </div>
                                                </div>
                                                <div class="d-flex mb-3 align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                        style="width: 30px; height: 30px; min-width: 30px;">2</div>
                                                    <div class="ms-3"> <strong>Chỉnh sửa:</strong> Nhập số cân (kg),
                                                        số mét vào Excel. <br><span class="text-danger fw-bold"><i
                                                                class="fa-solid fa-triangle-exclamation"></i> Không sửa
                                                            cột "Code" (Mã)</span>. </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                        style="width: 30px; height: 30px; min-width: 30px;">3</div>
                                                    <div class="ms-3"> <strong>Tải Lên:</strong> Chọn file vừa điền
                                                        xong và bấm "Upload dữ liệu". </div>
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
                                    <h5 class="fw-bold text-warning m-0"><i
                                            class="fa-solid fa-mobile-screen-button me-2"></i>4. Sản Xuất & Quét Kiểm
                                        Tra (Mobile)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng này được tối ưu cho giao diện điện thoại, giúp bạn dễ dàng kiểm tra
                                        thông tin chi tiết mã vạch trực tiếp tại xưởng thông qua Camera điện thoại.</p>
                                    <div class="row mb-4">
                                        <div class="col-md-5 mb-3 mb-md-0 text-center"> <img
                                                src="/images/guide/mobile_scan.png"
                                                alt="Giao diện Quét kiểm tra trên Mobile"
                                                class="img-fluid border rounded shadow-sm w-100"
                                                style="max-width: 250px;"> </div>
                                        <div class="col-md-7">
                                            <ol class="ps-3 mb-0">
                                                <li class="mb-2">Truy cập vào mục <strong>Quét Kiểm Tra</strong>
                                                    (hoặc đường dẫn <code
                                                        class="text-primary">/production/scan-mobile</code>).</li>
                                                <li class="mb-2">Hệ thống sẽ yêu cầu quyền sử dụng Camera trên điện
                                                    thoại, hãy bấm <strong>Cho phép</strong>.</li>
                                                <li class="mb-2">Đưa Camera hướng vào mã vạch/mã QR của sản phẩm (cây
                                                    vải) cần kiểm tra sao cho mã nằm gọn trong khung quét.</li>
                                                <li>Thông tin chi tiết về sản phẩm (độ dài, tình trạng, lịch sử...) sẽ
                                                    ngay lập tức được hiển thị bên dưới màn hình.</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="section-coating">
                            <div class="card shadow-sm border-0 mb-4">
                                            <div class="card-header py-3">
                                                <h5 class="fw-bold text-success m-0"><i
                                                        class="fa-solid fa-layer-group me-2"></i>5. Máy Tráng / Ghép
                                                    Kéo Mộc
                                                    (Mobile)</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>Nghiệp vụ ghép nhiều cây vải "Mộc" thành 1 cuộn "Tráng" thành phẩm.
                                                    Giao diện
                                                    được thiết kế để thao tác nhanh trên điện thoại hoặc máy tính bảng
                                                    gắn tại xưởng
                                                    (đường dẫn <code
                                                        class="text-primary">/production/coating-confirmation</code>).
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
                                                            <li class="mb-2">Vào menu <strong>Xác Nhận Tráng</strong>
                                                                và chọn Máy
                                                                thực hiện trên màn hình thiết bị.</li>
                                                            <li class="mb-2">Sử dụng chức năng quét mã qua camera
                                                                điện thoại hoặc
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
                                                        class="fa-solid fa-warehouse me-2"></i>6. Nhập Kho &amp; Quản
                                                    Lý Vị Trí
                                                    (Mobile)</h5>
                                            </div>
                                            <div class="card-body">
                                                <p>Giao diện Nhập kho mới được thiết kế theo quy trình <strong>3 bước
                                                        tối ưu cho
                                                        điện thoại</strong> (đường dẫn <code
                                                        class="text-primary">/warehouse/scan-to-location</code>). Nhân
                                                    viên có thể sử
                                                    dụng camera điện thoại hoặc súng quét Bluetooth để thao tác thuận
                                                    tiện ngay tại
                                                    kệ hàng.</p>
                                                <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                                        class="fa-solid fa-1"></i> Quy trình 3
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
                                                                <span
                                                                    class="badge bg-primary rounded-pill me-2">B1</span>
                                                                <strong>Cấu hình:</strong> Chọn Chế độ nhập và Chọn Trạm
                                                                Cân (nếu có
                                                                kết nối WiFi).
                                                            </div>
                                                            <div class="list-group-item px-0 py-2">
                                                                <span
                                                                    class="badge bg-primary rounded-pill me-2">B2</span>
                                                                <strong>Vị trí:</strong> Quét mã QR dán trên kệ hàng
                                                                (Bước này sẽ
                                                                được bỏ qua nếu ở chế độ Nhập Tạm).
                                                            </div>
                                                            <div class="list-group-item px-0 py-2">
                                                                <span
                                                                    class="badge bg-primary rounded-pill me-2">B3</span>
                                                                <strong>Quét mã:</strong> Hướng camera quét mã tem trên
                                                                cây vải. Hệ
                                                                thống sẽ tự động ghi nhận Trọng lượng (từ cân) và Vị trí
                                                                (từ B2).
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                                        class="fa-solid fa-layer-group me-2"></i> Các Chế Độ Hoạt Động
                                                </h6>
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <div class="border rounded p-3 h-100 bg-light">
                                                                    <div class="fw-bold text-secondary mb-2"><i
                                                                            class="fa-solid fa-inbox me-1"></i> Nhập
                                                                        Tạm</div>
                                                                    <small class="text-muted">Dùng khi muốn xác nhận
                                                                        hàng đã về kho
                                                                        nhanh chóng mà chưa kịp sắp xếp lên kệ. Không
                                                                        yêu cầu quét mã
                                                                        vị trí ở Bước 2.</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="border rounded p-3 h-100 bg-light">
                                                                    <div class="fw-bold text-success mb-2"><i
                                                                            class="fa-solid fa-location-dot me-1"></i>
                                                                        Nhập + Vị
                                                                        Trí</div>
                                                                    <small class="text-muted">Chế độ chuẩn: Quét kệ
                                                                        hàng trước, sau
                                                                        đó quét các cây vải. Hàng sẽ được tự động gắn
                                                                        vào vị trí kệ
                                                                        đó.</small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="border rounded p-3 h-100 bg-light">
                                                                    <div class="fw-bold text-info mb-2"><i
                                                                            class="fa-solid fa-map-pin me-1"></i> Xác
                                                                        Nhận Vị
                                                                        Trí</div>
                                                                    <small class="text-muted">Dùng khi thực hiện đảo
                                                                        kệ, chuyển vị
                                                                        trí cho các cây vải đã có sẵn trong kho. Chỉ cập
                                                                        nhật vị trí,
                                                                        không đổi trạng thái hàng.</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                                        class="fa-solid fa-weight-scale me-2"></i> Cân Real-time & Cập
                                                    nhật cân</h6>
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <p>Hệ thống tự động kết nối với Trạm Cân qua WebSocket để lấy dữ
                                                            liệu cân
                                                            thời gian thực:</p>
                                                        <ul class="mb-0">
                                                            <li class="mb-2"><strong>Tự động lấy cân:</strong> Khi số
                                                                cân ở trạng
                                                                thái <span class="badge bg-success">Ổn định</span>, hệ
                                                                thống sẽ tự
                                                                dùng số đó cho cây vải bạn vừa quét.</li>
                                                            <li class="mb-2"><strong>Tái nhập dư:</strong> Nếu bạn
                                                                quét một cây
                                                                vải đã trong kho nhưng có trọng lượng mới (đã sả bớt),
                                                                hệ thống sẽ
                                                                tự nhận diện và thực hiện nghiệp vụ "Tái nhập dư".</li>
                                                            <li><strong>Nhập tay:</strong> Luôn có ô nhập trọng lượng
                                                                tay dự phòng
                                                                nếu trạm cân mất kết nối.</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                                        class="fa-solid fa-clock-rotate-left me-2"></i> Lịch Sử Phiên
                                                    Quét</h6>
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <p>Phía dưới màn hình điện thoại luôn hiển thị danh sách 20 cây
                                                            vải vừa thực
                                                            hiện gần nhất trong phiên làm việc, giúp bạn dễ dàng đối
                                                            soát số lượng
                                                            kg và vị trí vừa quét mà không cần quay lại danh sách tổng.
                                                        </p>
                                                    </div>
                                                </div>
                                                <h6 class="fw-bold text-primary mt-4 mb-3"><i
                                                        class="fa-solid fa-3"></i> Xem Lịch Sử /
                                                    Danh Sách Nhập Kho</h6>
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <p>Ngay trên điện thoại, truy cập <strong>Danh Sách Nhập
                                                                Kho</strong>, bạn
                                                            có thể dễ dàng kiểm tra:</p>
                                                        <ul class="mb-0">
                                                            <li>Số mét, Lượng Kg, Vị trí kệ, Màu, Loại... của từng cây
                                                                vải.</li>
                                                            <li>Trích xuất dữ liệu người nào thực hiện thao tác cân nhập
                                                                kho và chốt
                                                                vào giờ nào.</li>
                                                            <li>Các công cụ lọc hỗ trợ tra cứu nhanh <strong>Mã
                                                                    barcode</strong> hoặc
                                                                <strong>Ngày nhập</strong> trên màn hình nhỏ di động.
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> {{-- SECTION: TRẠM IN --}} <div class="tab-pane fade" id="section-printstation">
                                        <div class="card shadow-sm border-0 mb-4">
                                            <div class="card-header py-3">
                                                <h5 class="fw-bold text-info m-0"><i
                                                        class="fa-solid fa-print me-2"></i>7. Trạm In Kiosk (Tự Động)
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <p>Giải pháp cho xưởng in tự động không cần người thao tác bấm máy: Máy
                                                    tính cắm máy in luôn bật sẵn, tự động nhận lệnh từ mọi nơi.</p>
                                                <div class="row align-items-center mb-4">
                                                    <div class="col-md-6 mb-3 mb-md-0 text-center"> <img
                                                            src="/images/guide/ss_printstation.png"
                                                            alt="Trạm in Kiosk tự động"
                                                            class="img-fluid border rounded shadow-sm w-100"> </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold">Cách máy in tự động hoạt động:</h6>
                                                        <ol class="ps-3 mb-0">
                                                            <li class="mb-2"><strong>Admin</strong> tạo các "Trạm In"
                                                                và gán quyền trạm in cho một nhân viên cụ thể (Tài khoản
                                                                Kiosk).</li>
                                                            <li class="mb-2">Máy tính cắm máy in đăng nhập bằng tài
                                                                khoản Kiosk đó, sau đó nhấp vào link <strong><a
                                                                        href="/production/print-station"
                                                                        target="_blank"
                                                                        class="text-decoration-none">Print Station Màn
                                                                        Hình Chờ</a></strong> và để y nguyên máy đó cả
                                                                ngày.
                                                            </li>
                                                            <li>Mỗi khi bất kỳ máy nào khác trong xưởng bấm nút phát
                                                                hành tem (Ví dụ ở máy Tráng), tín hiệu mạng ảo sẽ nhảy
                                                                tới màn
                                                                hình Kiosk và đẩy thẳng ra máy in. </li>
                                                        </ol>
                                                        <div class="alert alert-secondary mt-3 small p-2"> *Lưu ý: Bật
                                                            chế độ in âm thầm (SILENT PRINTING hoặc KIOSK
                                                            mode) trên Chrome/Edge để tắt hộp thoại hỏi cấu hình trước
                                                            khi in. </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @role('admin')
                                        <div class="tab-pane fade" id="section-admin">
                                            <div class="card shadow-sm border-0 mb-4 border-danger">
                                                <div class="card-header bg-danger text-white py-3">
                                                    <h5 class="fw-bold m-0"><i class="fa-solid fa-user-gear me-2"></i>8.
                                                        Dành cho Quản
                                                        Trị Viên</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p>Kiểm soát quy trình Cài Đặt Sản Xuất, Đơn hàng, Phân quyền hệ thống.
                                                    </p>
                                                    <div class="row align-items-center mb-4">
                                                        <div class="col-md-5 mb-3 mb-md-0 text-center"> <img
                                                                src="/images/guide/ss_admin.png" alt="Chức năng Admin"
                                                                class="img-fluid border rounded shadow-sm w-100"> </div>
                                                        <div class="col-md-7">
                                                            <h6 class="fw-bold">Thiết lập dữ liệu ban đầu:</h6>
                                                            <p>Để khởi chạy xưởng mới, bạn cần tạo thông số theou thứ tự
                                                                chuẩn sau: </p>
                                                            <ol class="ps-3 mb-0">
                                                                <li class="mb-2"><strong>Mô hình Sản xuất:</strong> Vào
                                                                    menu
                                                                    <strong>Sản phẩm</strong> tạo các Mode, sau đó qua các
                                                                    Menu
                                                                    <strong>Danh mục / Loại Tem</strong> để tinh chỉnh.
                                                                </li>
                                                                <li class="mb-2"><strong>Đơn hàng (PO):</strong> Tạo mới
                                                                    Đơn Hàng và
                                                                    Liên kết (Map) các Sản Phẩm vào đơn hàng đó (Để lúc nhân
                                                                    viên In tem
                                                                    có thể bắt chéo Model + Đơn hàng).</li>
                                                                <li class="mb-2"><strong>Người & Máy:</strong> Vào menu
                                                                    Quản lý Users
                                                                    tạo tài khoản. Phân đúng phân loại vai trò (Role). Sau
                                                                    đó gán cho họ
                                                                    phụ trách Máy Tráng, Trạm Cân, Kiosk đúng người.</li>
                                                            </ol>
                                                            <hr>
                                                            <p class="text-danger small"><i
                                                                    class="fa-solid fa-circle-exclamation me-1"></i> Nếu
                                                                nhân viên đăng
                                                                nhập thấy màn hình báo lỗi, nghĩa là tài khoản đó đang không
                                                                được gắn
                                                                bất kỳ Vai trò nào. Hãy qua menu Vai trò -> Edit -> Gán
                                                                User.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endrole

                        {{-- SECTION: MOBILE GUIDE --}}
                        <div class="tab-pane fade" id="section-mobile-guide">
                            <div class="card shadow-sm border-0 mb-4 border-primary">
                                <div class="card-header bg-primary text-white py-3">
                                    <h5 class="fw-bold m-0"><i class="fa-solid fa-mobile-screen me-2"></i>9. Tài liệu
                                        Đào tạo Mobile (Slide Deck)</h5>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <i class="fa-solid fa-file-powerpoint fa-4x text-danger opacity-25"></i>
                                        <i class="fa-solid fa-arrow-right fa-2x mx-3 text-muted"></i>
                                        <i class="fa-solid fa-mobile-screen-button fa-5x text-primary"></i>
                                    </div>
                                    <h4 class="fw-bold mb-3">Hướng dẫn từng bước dành cho Nhân viên Sản xuất</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 600px;">
                                        Đây là bộ tài liệu được thiết kế tối ưu cho di động, giúp bạn dễ dàng theo dõi
                                        từng bước thực hiện các thao tác tại xưởng (Scan, Tráng, Nhập kho).
                                    </p>
                                    <div class="d-grid gap-3 col-md-6 mx-auto">
                                        <a href="/mobile_guide.html" target="_blank"
                                            class="btn btn-primary btn-lg shadow">
                                            <i class="fa-solid fa-up-right-from-square me-2"></i> Mở Tài liệu Đào tạo
                                            (Toàn màn hình)
                                        </a>
                                    </div>
                                    <div class="alert alert-info mt-5 d-inline-block text-start shadow-sm border-0">
                                        <h6 class="fw-bold mb-2"><i
                                                class="fa-solid fa-lightbulb text-warning me-2"></i>Mẹo sử dụng:</h6>
                                        <ul class="small mb-0">
                                            <li>Sử dụng phím mũi tên <strong>Trái/Phải</strong> hoặc <strong>Vuốt màn
                                                    hình</strong> để chuyển Slide.</li>
                                            <li>Nhấn phím <strong>F</strong> để xem toàn màn hình.</li>
                                            <li>Nhấn phím <strong>O</strong> để xem chế độ tổng quan (Overview).</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
