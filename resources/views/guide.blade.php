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
                            4. Quét kiểm tra hàng
                        </a>
                        <a href="#section-coating" class="list-group-item list-group-item-action fw-bold"
                            data-bs-toggle="list">
                            5. Xác nhận tráng
                        </a>
                        <a href="#section-warehouse" class="list-group-item list-group-item-action fw-bold text-success"
                            data-bs-toggle="list">
                            6. Nhập Kho & Vị Trí
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
                                <div class="card-header  py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-print me-2"></i>2. Cách
                                        In Tem Mã Vạch</h5>
                                </div>
                                <div class="card-body">
                                    <p>Dùng để tạo tem dán lên sản phẩm mới.</p>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold">Quy trình thực hiện:</h6>
                                                <ol class="ps-3 mb-0">
                                                    <li class="mb-2">Trên menu trái, chọn mục <br><strong><i
                                                                class="fa-solid fa-print text-muted"></i> In Tem Mã
                                                            Vạch</strong> (hoặc <strong>In Tem Excel</strong>).</li>
                                                    <li class="mb-2">Cột bên trái: Chọn <strong>Sản phẩm</strong> và
                                                        <strong>Loại Tem</strong>.
                                                    </li>
                                                    <li class="mb-2">Cột bên phải: Chọn ý nghĩa cột đầu tiên
                                                        (<strong>Số lượng tem</strong> hoặc <strong>Số thứ tự
                                                            tem</strong>).</li>
                                                    <li class="mb-2">Copy dữ liệu từ Excel và dán vào ô nội dung.
                                                        <br><small class="text-muted">(Thứ tự cột: Cột 1, Mã Đơn Hàng,
                                                            Màu, Loại, Khổ, Loại Nhựa, GSM, Chiều dài, Số máy)</small>
                                                    </li>
                                                    <li class="mb-2">Tùy chỉnh <strong>Định dạng mã in</strong> (QR
                                                        Code/Barcode) và các thông số khác.</li>
                                                    <li>Nhấn nút <strong>Tạo Mới & In Ngay</strong>.</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold text-danger">Lưu ý quan trọng:</h6>
                                                <ul class="ps-3 mb-0 small">
                                                    <li class="mb-2">Hãy kiểm tra kỹ giấy in trong máy in trước khi
                                                        bấm nút.</li>
                                                    <li>Nếu trình duyệt chặn cửa sổ bật lên (Popup), hãy cho phép nó để
                                                        hộp thoại in xuất hiện.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-excel">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header  py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-file-excel me-2"></i>3.
                                        Nhập liệu từ Excel</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng này giúp bạn cập nhật thông tin (số mét, trọng lượng...) cho hàng loạt
                                        tem cùng lúc.</p>

                                    <div class="step-guide">
                                        <div class="d-flex mb-3">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; min-width: 30px;">1</div>
                                            <div class="ms-3">
                                                <strong>Tải file mẫu:</strong> Vào mục Nhập/Xuất Excel -> Nhấn "Tải file
                                                mẫu".
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; min-width: 30px;">2</div>
                                            <div class="ms-3">
                                                <strong>Nhập số liệu:</strong> Mở file Excel vừa tải, nhập số liệu vào
                                                các cột tương ứng. <span class="text-danger fw-bold">Tuyệt đối không sửa
                                                    cột Code</span>.
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; min-width: 30px;">3</div>
                                            <div class="ms-3">
                                                <strong>Tải lên hệ thống:</strong> Chọn file Excel đã nhập -> Nhấn nút
                                                "Nhập dữ liệu (Import)".
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-scan">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header  py-3">
                                    <h5 class="fw-bold text-warning m-0"><i class="fa-solid fa-barcode me-2"></i>4.
                                        Quét
                                        kiểm tra hàng</h5>
                                </div>
                                <div class="card-body">
                                    <p>Sử dụng máy quét cầm tay hoặc camera điện thoại.</p>
                                    <ol>
                                        <li class="mb-2">Vào mục <strong>Quét Sản Phẩm</strong>.</li>
                                        <li class="mb-2">Đặt con trỏ chuột vào ô nhập liệu trắng.</li>
                                        <li>Cầm máy quét, bấm quét mã vạch trên tem sản phẩm. Thông tin chi tiết sẽ hiện
                                            ra ngay bên dưới.</li>
                                    </ol>
                                    <div class="alert alert-secondary small">
                                        Nếu dùng điện thoại, hãy bấm nút <strong>"Bật Camera"</strong> để quét.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="section-coating">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header  py-3">
                                    <h5 class="fw-bold text-success m-0"><i
                                            class="fa-solid fa-layer-group me-2"></i>5. Xác
                                        nhận tráng</h5>
                                </div>
                                <div class="card-body">
                                    <p>Sử dụng khi bạn cần ghép nhiều cây Mộc thành một cây Tráng mới.</p>
                                    <ol>
                                        <li class="mb-2">Vào mục <strong>Xác Nhận Tráng</strong> trên bảng điều khiển
                                            hoặc menu.</li>
                                        <li class="mb-2">Chọn <strong>Thành phẩm</strong> và <strong>Máy thực
                                                hiện</strong>.</li>
                                        <li class="mb-2">Quét lần lượt các mã tem Mộc. Có thể dùng súng quét, nhập
                                            tay, hoặc dùng nút Bật Camera.</li>
                                        <li class="mb-2">Nhập số mét xuất dùng tương ứng cho từng cây Mộc đã quét.
                                        </li>
                                        <li class="mb-2">Ở mục <strong>Thành phẩm đầu ra</strong>, nhập chiều dài
                                            thực tế thu được của cây Tráng. Hệ thống sẽ tự tính tỉ lệ.</li>
                                        <li>Nhấn nút <strong>XÁC NHẬN TẠO MÃ & IN TEM</strong>. Một mã sản phẩm Mới sẽ
                                            tự động được sinh ra và gửi tín hiệu In.</li>
                                    </ol>
                                    <div class="alert alert-secondary small mt-3">
                                        <i class="fa-solid fa-lightbulb text-warning me-1"></i> <strong>Lưu ý:</strong>
                                        Đảm bảo <strong>Độ dài xuất dùng</strong> không vượt quá độ dài hiện tại của cây
                                        mộc. Nếu nhập dư, hệ thống sẽ cảnh báo đỏ.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: NHẬP KHO --}}
                        <div class="tab-pane fade" id="section-warehouse">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-success m-0"><i class="fa-solid fa-warehouse me-2"></i>6.
                                        Nhập Kho &amp; Quản Lý Vị Trí</h5>
                                </div>
                                <div class="card-body">
                                    <p>Chức năng kho giúp theo dõi hàng hóa bán thành phẩm theo vị trí thực tế trong
                                        kho.</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold">Nhập kho đơn giản (Không vị trí):</h6>
                                                <ol class="ps-3 mb-0">
                                                    <li class="mb-2">Vào menu <strong>Nhập Kho</strong>, chọn chế độ
                                                        <span class="badge bg-secondary">Nhập Tạm</span>.
                                                    </li>
                                                    <li class="mb-2">Quét lần lượt mã cây vải cần nhập kho.</li>
                                                    <li>Hệ thống sẽ ghi nhận và cập nhật trạng thái người dùng nhao.
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold">Nhập kho có vị trí:</h6>
                                                <ol class="ps-3 mb-0">
                                                    <li class="mb-2">Chọn chế độ <span class="badge bg-success">Nhập
                                                            + Vị Trí</span>.</li>
                                                    <li class="mb-2">Quét mã QR <strong>kệ hàng</strong> trước để
                                                        chọn vị trí.</li>
                                                    <li>Tiếp tục quét các mã cây vải. Mỗi cây sẽ tự gán vào kệ đó.</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info small mt-3">
                                        <i class="fa-solid fa-circle-info me-1"></i> Admin có thể vào <strong>Quản Lý
                                            Vị Trí Kho</strong> để thêm/sửa kệ, và xuất báo cáo tồn kho Excel bất kỳ lúc
                                        nào.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: TRẠM IN --}}
                        <div class="tab-pane fade" id="section-printstation">
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header py-3">
                                    <h5 class="fw-bold text-info m-0"><i class="fa-solid fa-print me-2"></i>7. Trạm In
                                        Kiosk (Tự Động)</h5>
                                </div>
                                <div class="card-body">
                                    <p>Hệ thống in tự động cho phép máy tính Kiosk nhận lệnh in thông qua mạng nội bộ và
                                        tự in không cần bấm gì thêm.</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold">Thiết lập trạm in (Admin):</h6>
                                                <ol class="ps-3 mb-0">
                                                    <li class="mb-2">Vào <strong>Quản Lý Trạm In</strong>, tạo mới
                                                        trạm với mã (code) dậy nhớ.</li>
                                                    <li class="mb-2">Vào <strong>Phân Công Trạm In</strong>, gán trạm
                                                        cho đúng nhân viên.</li>
                                                    <li>Nhân viên mở trình duyệt với tham số --kiosk-printing <a
                                                            href="{{ route('production.print-station') }}"
                                                            target="_blank"><strong>Print Station</strong></a> trên máy
                                                        Kiosk và để yên.</li>
                                                    <li> Các máy in khác khai báo /01 /02 /03 ...</li>
                                                </ol>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="fw-bold text-info">Quy trình in tự động:</h6>
                                                <ol class="ps-3 mb-0 small">
                                                    <li class="mb-2">Nhân viên thực hiện <strong>Xác Nhận
                                                            Tráng</strong> ở máy tính khác.</li>
                                                    <li class="mb-2">Hệ thống gửi tín hiệu qua mạng đến đúng trạm in
                                                        được phân công.</li>
                                                    <li>Máy Kiosk tự in tem không cần ai bấm gì.</li>
                                                </ol>
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
                                        <h5 class="fw-bold m-0"><i class="fa-solid fa-user-gear me-2"></i>6. Dành cho Quản
                                            Trị Viên</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="fw-bold">Thiết lập dữ liệu ban đầu:</h6>
                                        <p>Trước khi nhân viên có thể in tem, bạn cần tạo dữ liệu theo thứ tự sau:</p>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Thứ tự</th>
                                                        <th>Mục cần tạo</th>
                                                        <th>Giải thích</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td><a href="{{ route('manager.products') }}">Model Sản Phẩm</a>
                                                        </td>
                                                        <td>Tạo danh sách các mã hàng (Ví dụ: Áo thun, Vải Cotton...)</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td><a href="{{ route('manager.orders') }}">Đơn Hàng (PO)</a></td>
                                                        <td>Tạo đơn hàng và gán các Model vào đơn hàng đó.</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td><a href="{{ route('users.index') }}">Người dùng</a></td>
                                                        <td>Tạo tài khoản cho nhân viên và phân quyền (Roles).</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <hr>
                                        <h6 class="fw-bold text-danger">Khắc phục sự cố:</h6>
                                        <ul>
                                            <li><strong>User không thấy menu?</strong> -> Kiểm tra xem họ đã được gán Vai
                                                trò (Role) hay chưa.</li>
                                            <li><strong>Lỗi khi nhập Excel?</strong> -> Đảm bảo tiêu đề cột trong file Excel
                                                không bị đổi tên.</li>
                                        </ul>
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
