# Kế Hoạch Xây Dựng Tài Liệu Hướng Dẫn Sử Dụng (PowerPoint)

## Mục Tiêu
Tạo một tài liệu hướng dẫn sử dụng trực quan bằng PowerPoint, thiết kế tối ưu để hiển thị các ảnh chụp màn hình (screenshot) giao diện mobile. Tài liệu sẽ hướng dẫn chi tiết từng bước (step-by-step) cho các chức năng cụ thể của ứng dụng, giúp người dùng cuối dễ dàng theo dõi và thực hành chính xác.

## Danh Sách Chức Năng Cần Hướng Dẫn (Chỉ dành cho Mobile)

Tài liệu này sẽ bỏ qua các chức năng trên Desktop và chỉ tập trung vào các thao tác thực địa trên Mobile (chủ yếu là quét mã, xác nhận và cập nhật). Cụ thể bao gồm:

**1. Nhóm Sản Xuất (Vải)**
*   **Xác nhận (Scan):** Thao tác quét mã vạch để xác nhận cây vải.

**2. Nhóm Quản lý Tráng (Coating)**
*   **Xác Nhận Tráng:** Quét mã vạch để xác nhận cuộn hàng đã tráng.
*   **Cập Nhật Tráng:** Quét/tìm kiếm để cập nhật trạng thái lớp phủ.

**3. Nhóm Quản lý Kho (Warehouse)**
*   **Nhập Kho:** Quét mã sản phẩm và quét mã vị trí để tiến hành nhập kho.

---

## Kịch Bản Chụp Ảnh Màn Hình (Screenshot Plan)
Vì bạn chưa có hình ảnh, dưới đây là danh sách chi tiết các màn hình bạn cần dùng điện thoại để chụp lại. Hãy mở ứng dụng trên điện thoại và chụp theo trình tự sau:

### Chức năng 1: Xác nhận Vải (Scan)
*   **Ảnh 1.1:** Màn hình chính khi mới vào menu "Xác nhận". (Thấy ô nhập/quét mã).
*   **Ảnh 1.2:** Màn hình hiển thị thông tin cây vải sau khi quét mã thành công.
*   **Ảnh 1.3:** (Nếu có) Màn hình thông báo thành công hoặc lỗi sau khi nhấn nút xác nhận cuối cùng.

### Chức năng 2: Xác Nhận Tráng
*   **Ảnh 2.1:** Màn hình giao diện "Xác nhận tráng" lúc mới mở.
*   **Ảnh 2.2:** Màn hình sau khi quét cuộn hàng.
*   **Ảnh 2.3:** Màn hình hiển thị kết quả xử lý/xác nhận thành công.

### Chức năng 3: Cập Nhật Tráng
*   **Ảnh 3.1:** Màn hình giao diện "Cập nhật tráng" (form tìm kiếm hoặc ô quét mã).
*   **Ảnh 3.2:** Màn hình chi tiết thông tin sau khi quét, lúc đang điền/chọn các thông tin cập nhật (VD: chọn trạng thái lỗi, ghi chú,...).
*   **Ảnh 3.3:** Màn hình thông báo sau khi bấm Lưu/Cập nhật.

### Chức năng 4: Nhập Kho (Scan to location)
*   **Ảnh 4.1:** Màn hình giao diện "Nhập Kho" (sẽ thấy ô quét vị trí / ô quét tem).
*   **Ảnh 4.2:** Màn hình sau khi quét mã vị trí (hiển thị vị trí đã chọn).
*   **Ảnh 4.3:** Màn hình sau khi quét tem hàng hóa (hiển thị thông tin hàng sẽ nhập vào vị trí đó).
*   **Ảnh 4.4:** (Nếu có) Thông báo hoàn tất nhập kho.

> [!TIP]
> **Lưu ý khi chụp ảnh:**
> - Nên để điện thoại ở chế độ màn hình sáng rõ.
> - Có thể dùng dữ liệu test để quét và chụp ảnh.
> - Bạn có thể nén các ảnh này thành file `.zip` rồi gửi lên đây, hoặc tải lên Google Drive rồi gửi link cho mình. Mình sẽ dựa vào đó để lên khung trình bày và viết text vào PowerPoint.

## Open Questions
> [!NOTE]
> Bạn thấy kịch bản chụp ảnh như trên đã hợp lý với luồng thao tác thực tế của hệ thống chưa? Nếu Ok, bạn có thể tiến hành chụp ảnh theo kịch bản này nhé.

## Cấu Trúc File PowerPoint Đề Xuất

### Phần 1: Giới thiệu chung (1-2 Slide)
*   **Slide 1: Trang bìa**
    *   Tên ứng dụng / Phân hệ phần mềm.
    *   Tiêu đề tài liệu: "Hướng Dẫn Sử Dụng Ứng Dụng (Phiên bản Mobile)".
    *   Logo công ty / Logo ứng dụng.
*   **Slide 2: Mục lục**
    *   Liệt kê các chức năng chính sẽ được hướng dẫn. (Nên gắn Hyperlink đến từng phần trong PPT để người xem dễ điều hướng khi trình chiếu).

### Phần 2: Hướng dẫn chi tiết từng chức năng (Lặp lại cho mỗi chức năng)
Mỗi chức năng sẽ được trình bày theo một flow chuẩn thống nhất:

*   **Slide Mở đầu chức năng:**
    *   Tên chức năng (VD: Chức năng Cập nhật trạng thái Lớp phủ, Chức năng Quét mã,...).
    *   Mục đích: Chức năng này dùng để làm gì? Ai nên sử dụng? Điều kiện cần là gì?
*   **Slide Hướng dẫn từng bước (Step-by-step):**
    *   **Bố cục:** Chia slide làm 2 phần rõ rệt (Trái: Cột Text giải thích các bước, Phải: Hình ảnh Mobile mockup).
    *   **Hình ảnh:** Ảnh chụp màn hình giao diện mobile thực tế. Đặt ảnh vào trong một khung điện thoại (phone mockup frame) để tạo cảm giác thực tế và chuyên nghiệp.
    *   **Đánh dấu (Callouts/Annotations):** Sử dụng các hình khối (mũi tên, vòng tròn đỏ/cam, viền vuông) trỏ trực tiếp vào nút bấm, ô nhập liệu, hoặc vùng thông tin trên ảnh màn hình.
    *   **Text hướng dẫn:** Đánh số thứ tự các bước tương ứng với số thứ tự đánh dấu trên hình (Ví dụ: **1.** Nhấn vào nút "Thêm"; **2.** Quét mã vạch). Sử dụng ngôn từ ngắn gọn, tập trung vào hành động.
    *   *(Lưu ý: Nếu một chức năng có quá nhiều bước, hãy chia nhỏ ra thành nhiều slide, mỗi slide chỉ nên từ 1-3 bước để tránh rối mắt).*
*   **Slide Lưu ý / Xử lý lỗi (Tùy chọn):**
    *   Các thông báo lỗi thường gặp khi thao tác sai và cách khắc phục.
    *   Các mẹo hoặc lưu ý quan trọng.

### Phần 3: Kết luận & Hỗ trợ (1 Slide)
*   Thông tin liên hệ khi người dùng gặp khó khăn (Email, Số điện thoại hỗ trợ kỹ thuật, Zalo...).
*   Lời cảm ơn.

## Các Bước Thực Hiện (Execution Plan)

1.  **Thu thập tài nguyên:**
    *   Xác định danh sách tính năng.
    *   Chụp ảnh màn hình (Screenshot) toàn bộ các màn hình mobile theo luồng thao tác của các tính năng đó.
2.  **Thiết kế Template PowerPoint:**
    *   Tạo Master Slide với màu sắc nhận diện thương hiệu.
    *   Tạo sẵn các Layout mẫu chứa khung hình điện thoại và khu vực để text.
3.  **Lắp ghép nội dung:**
    *   Đưa ảnh chụp màn hình vào các Layout.
    *   Thêm mũi tên, khung đỏ đánh dấu.
    *   Viết text hướng dẫn cho từng bước tương ứng.
4.  **Review và Xuất bản:**
    *   Chạy trình chiếu kiểm tra lại luồng thao tác.
    *   Xuất bản file dưới dạng PDF (Khuyên dùng) để gửi cho người dùng, giúp họ có thể xem trực tiếp và rõ nét trên điện thoại của họ mà không bị lỗi font.

## Open Questions
> [!NOTE]
> Để bắt đầu chi tiết hơn, bạn có thể cho tôi biết:
> 1. Bạn đã có sẵn hình ảnh chụp màn hình các chức năng chưa?
> 2. Bạn muốn hướng dẫn cho hệ thống/ứng dụng nào? (Ví dụ: Các module trong hệ thống quản lý sản xuất, quản lý kho gần đây của bạn?)
> 3. Bạn có muốn tôi thiết kế một cấu trúc text mẫu cho 1 chức năng cụ thể để bạn xem thử không?
