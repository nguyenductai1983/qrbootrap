---
marp: true
paginate: true
theme: default
backgroundColor: "#f8faff"
style: |

  @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap');

  section {
    font-family: 'Be Vietnam Pro', 'Segoe UI', Arial, sans-serif;
    font-size: 22px;
    padding: 40px 50px;
    color: #1e293b;
    background: #f8faff;
  }

  /* COVER slide */
  section.cover {
    background: linear-gradient(145deg, #1e3a8a 0%, #1d4ed8 50%, #0ea5e9 100%) !important;
    color: white !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
  }
  section.cover h1 { color: white !important; font-size: 2.2em; margin: 0 0 16px; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
  section.cover p { color: rgba(255,255,255,0.95) !important; font-size: 1.1em; }
  section.cover .badge {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 50px;
    padding: 8px 24px;
    margin-top: 24px;
    font-size: 0.9em;
  }

  /* SECTION divider */
  section.divider {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
  }
  section.divider.scan   { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important; }
  section.divider.coat   { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%) !important; }
  section.divider.update { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%) !important; }
  section.divider.wh     { background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%) !important; }

  section.divider .tag {
    display: inline-block;
    border-radius: 50px;
    padding: 10px 28px;
    font-size: 1.15em;
    font-weight: 700;
    margin-bottom: 20px;
    width: fit-content;
  }
  .tag.yellow { background: #f59e0b; color: white; }
  .tag.green  { background: #059669; color: white; }
  .tag.blue   { background: #2563eb; color: white; }
  .tag.teal   { background: #0d9488; color: white; }

  section.divider h2 { font-size: 2em; margin: 0 0 12px; }
  section.divider p { font-size: 1.1em; color: #475569; margin: 0; }

  /* TWO-COLUMN layout slides */
  h1 {
    font-size: 1.4em;
    color: #1e3a8a;
    border-left: 5px solid #2563eb;
    padding-left: 14px;
    margin-bottom: 20px;
    line-height: 1.3;
  }
  h1.green  { border-color: #059669; color: #065f46; }
  h1.orange { border-color: #f59e0b; color: #92400e; }
  h1.teal   { border-color: #0d9488; color: #134e4a; }

  .layout {
    display: flex;
    gap: 28px;
    align-items: flex-start;
  }
  .left { flex: 1 1 52%; }
  .right {
    flex: 0 0 42%;
    text-align: center;
  }
  .right img {
    border: 3px solid #cbd5e1;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    max-height: 460px;
    max-width: 100%;
    object-fit: cover;
    object-position: top;
  }

  /* Step items */
  .steps { list-style: none; padding: 0; margin: 0; }
  .steps li {
    position: relative;
    padding: 8px 0 8px 42px;
    border-bottom: 1px solid #e2e8f0;
    line-height: 1.5;
    min-height: 34px;
  }
  .steps li:last-child { border-bottom: none; }
  .num {
    position: absolute;
    left: 0;
    top: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-weight: 700;
    font-size: 0.85em;
    color: white;
  }
  .num.blue   { background: #2563eb; }
  .num.yellow { background: #d97706; }
  .num.green  { background: #059669; }
  .num.teal   { background: #0d9488; }

  .tip {
    background: #fef9c3;
    border-left: 4px solid #ca8a04;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 14px;
    font-size: 0.88em;
    line-height: 1.5;
  }
  .ok {
    background: #dcfce7;
    border-left: 4px solid #16a34a;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 14px;
    font-size: 0.88em;
  }
  .warn {
    background: #fee2e2;
    border-left: 4px solid #dc2626;
    border-radius: 8px;
    padding: 10px 14px;
    margin-top: 14px;
    font-size: 0.88em;
  }

  /* Button highlight */
  .btn-demo {
    display: inline-block;
    background: #2563eb;
    color: white;
    border-radius: 8px;
    padding: 4px 12px;
    font-weight: 600;
    font-size: 0.9em;
  }
  .btn-demo.green { background: #059669; }
  .btn-demo.yellow { background: #d97706; }

  /* Table of contents */
  .toc-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-top: 28px;
  }
  .toc-card {
    border-radius: 16px;
    padding: 22px;
    text-align: center;
  }
  .toc-card a {
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .toc-card h3 { font-size: 1em; margin: 8px 0 4px; }
  .toc-card p { font-size: 0.82em; color: #64748b; margin: 0; }
  .toc-card.y { background: #fffbeb; border: 2px solid #fbbf24; }
  .toc-card.g { background: #f0fdf4; border: 2px solid #6ee7b7; }
  .toc-card.b { background: #eff6ff; border: 2px solid #93c5fd; }
  .toc-card.t { background: #f0fdfa; border: 2px solid #5eead4; }
  .toc-icon { font-size: 2.2em; }

  /* Caption below phone image */
  .caption { font-size: 0.78em; color: #64748b; margin-top: 8px; font-style: italic; }

  /* END slide */
  section.end {
    background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 100%) !important;
    color: white !important;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
  }
  section.end h1 { color: white !important; border: none; font-size: 2em; }
  section.end p { color: rgba(255,255,255,0.9) !important; }

---
<!-- _class: cover -->

# 📱 Hướng Dẫn Sử Dụng<br>Hệ Thống QR Mobile

**Dành cho công nhân sản xuất**

<div class="badge">Phiên bản 2026 · Thao tác trên điện thoại</div>

---

<!-- _class: split -->

# 💡 Giới Thiệu


<div class="row">
<div class="left">

### 1. Bối cảnh & Thách thức
- 🌐 **Thị trường:** 4.0, AI, Dữ liệu số.
- 🎯 **Khách hàng:** Đòi hỏi minh bạch, **truy xuất nguồn gốc**.
- 🏭 **Nội bộ:** Giải quyết bài toán **sản lượng công nhân**.

### 2. Quyền lợi Công nhân
- 🛡️ **Bảo vệ quyền lợi:** Minh bạch, theo thời gian thực.
- 💯 **Công bằng:** Rõ ràng 100%, **không lo mất sản lượng**.

### 3. Ứng dụng phần mềm theo PDCA
- 📝 **Plan (Chuẩn bị):** Thiết lập lệnh sản xuất, quy trình và định mức trên hệ thống.
- ⚡ **Do (Thực hiện):** Công nhân thao tác số hóa, quét mã ghi nhận dữ liệu tại chuyền.
- 🔍 **Check (Kiểm tra):** QC đối chiếu dữ liệu, phần mềm tự động cảnh báo lỗi.
- 📈 **Act (Cải tiến):** Phân tích báo cáo từ hệ thống để tối ưu và tinh gọn sản xuất.

<div class="ok">
✅ <strong>Thông điệp:</strong> <br>
<em>"Phần mềm sẽ công bằng cho anh chị em, đồng thời là chìa khóa chinh phục khách hàng khó tính nhất."</em>
</div>

</div>
</div>

---

# Mục Lục — 4 Chức Năng Chính

<div class="toc-grid">
<div class="toc-card y">
  <a href="#6">
    <div class="toc-icon">🟡</div>
    <h3>Quét Cây Vải</h3>
    <p>Xác nhận sản xuất bằng camera hoặc mã thủ công</p>
  </a>
</div>
<div class="toc-card g">
  <a href="#10">
    <div class="toc-icon">🟢</div>
    <h3>Xác Nhận Tráng</h3>
    <p>Quét vải gốc & khai báo thành phẩm tráng mới</p>
  </a>
</div>
<div class="toc-card b">
  <a href="#14">
    <div class="toc-icon">🔵</div>
    <h3>Cập Nhật Tráng</h3>
    <p>Quét mã tráng và nhập số mét thực tế</p>
  </a>
</div>
<div class="toc-card t">
  <a href="#17">
    <div class="toc-icon">🏭</div>
    <h3>Nhập Kho</h3>
    <p>Chọn hình thức nhập và quét mã hàng</p>
  </a>
</div>
</div>

---

# Bước 1: Đăng Nhập Hệ Thống

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num blue">1</span> Mở trình duyệt trên điện thoại</li>
  <li><span class="num blue">2</span> Tại ô <strong>"Email hoặc Username"</strong> — gõ tên đăng nhập của bạn.</li>
  <li><span class="num blue">3</span> Tại ô <strong>"Password"</strong> — gõ mật khẩu.</li>
  <li><span class="num blue">4</span> Nhấn nút <span class="btn-demo">Đăng nhập</span> màu xanh to bên dưới.</li>
  <li><span class="num blue">5</span> Hệ thống sẽ chuyển sang màn hình <strong>Màn hình chính</strong> chính.</li>
</ul>
<div class="tip">
💡 <strong>Nếu quên mật khẩu:</strong> Nhấn vào link <strong>"Quên mật khẩu?"</strong> ở góc phải phía dưới ô mật khẩu, hoặc liên hệ bộ phận IT.
</div>
</div>
<div class="right">
<img src="screenshots/r00_login.png" alt="Màn hình đăng nhập" />
<div class="caption">Màn hình Đăng Nhập</div>
</div>
</div>

---

# Bước 2: Màn Hình Chính

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num blue">1</span> Sau khi đăng nhập, bạn thấy màn hình <strong>Bảng Điều Khiển</strong>.</li>
  <li><span class="num blue">2</span> Cuộn xuống để xem các <strong>thẻ chức năng</strong> của bạn.</li>
  <li><span class="num blue">3</span> Mỗi thẻ có tên chức năng và nút <strong>"Truy cập →"</strong> màu xanh bên dưới.</li>
  <li><span class="num blue">4</span> Nhấn vào nút <strong>"Truy cập →"</strong> để vào chức năng tương ứng.</li>
</ul>
<div class="ok">
✅ <strong>Bạn chỉ thấy các chức năng được cấp quyền.</strong> Nếu thiếu chức năng, liên hệ quản lý để được cấp thêm quyền.
</div>
</div>
<div class="right">
<img src="screenshots/g00_dash_all_cards.png" alt="T&#7845;t c&#7843; th&#7867; ch&#7913;c n&#259;ng tr&#234;n Màn hình chính" />
<div class="caption">Các thẻ chức năng trên Màn hình chính</div>
</div>
</div>

---

<!-- _class: divider scan -->

<div class="tag yellow">I. Quét Cây Vải</div>

## Xác nhận sản xuất cây vải

**Mục đích:** Khi công nhân hoàn thành gia công một cây vải trên máy, dùng chức năng này để hệ thống ghi nhận.

**Ai sử dụng:** Công nhân tổ sản xuất.

---

# I — Quét Cây Vải: Vị Trí Trên Màn hình chính

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num yellow">1</span> Trên Màn hình chính cuộn xuống.</li>
  <li><span class="num yellow">2</span> Tìm thẻ <strong>"Quét Cây Vải"</strong> — có biểu tượng mã vạch màu vàng.</li>
  <li><span class="num yellow">3</span> Nhấn nút <span class="btn-demo yellow">Truy cập →</span> màu vàng bên dưới thẻ.</li>
</ul>
<div class="tip">
💡 Nếu không thấy thẻ này, liên hệ quản lý để được cấp quyền <strong>"Quét Cây Vải"</strong>.
</div>
</div>
<div class="right">
<img src="screenshots/g01_dash_production_cards.png" alt="Thẻ Quét Cây Vải trên Màn hình chính" />
<div class="caption">Thẻ "Quét Cây Vải" trong nhóm Tác Vụ Sản Xuất</div>
</div>
</div>

---

# I — Quét Cây Vải: Thiết Lập Ban Đầu

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num yellow">4</span> Phần <strong>"Thiết lập quét"</strong> xuất hiện ở đầu trang.</li>
  <li><span class="num yellow">5</span> Nhấn vào ô <strong>"Chọn Máy Thực Hiện"</strong> → chọn máy bạn đang sử dụng.</li>
  <div class="tip">
   Ô <strong>"Gán Đơn Hàng (PO)"</strong> và <strong>"Gán Model"</strong>: có thể để mặc định hoặc chọn theo ca trưởng.
💡 <br>
<strong>Thiết lập một lần mỗi ca:</strong> Hệ thống sẽ nhớ cho các lần quét tiếp theo.
</div>
</div>
<div class="right">
<img src="screenshots/g03_scan_01_setup.png" alt="Chọn máy thực hiện" />
<div class="caption">Bước thiết lập: Chọn máy và đơn hàng</div>
</div>
</div>

---

# I — Quét Cây Vải: Quét Mã & Kết Quả

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num yellow">6</span> Tại mục <strong>"QUÉT SẢN PHẨM"</strong>, nhấn <span class="btn-demo yellow">📷 BẬT CAMERA</span> hoặc nhập mã tem vào ô <strong>"NHẬP MÃ THỦ CÔNG"</strong> rồi nhấn <strong>▶</strong>.</li>
  <li><span class="num yellow">7</span> Hệ thống tự nhận diện — hiển thị <strong>"ĐÃ XÁC NHẬN"</strong> màu xanh lá kèm thông tin: Mã Vải, Màu, Thông số.</li>
  <li><span class="num yellow">8</span> Nhấn <span class="btn-demo yellow"> 🖨️ In Lại</span> nếu cần in lại tem.</li>
  <li><span class="num yellow">9</span> Nhập <span class="btn-demo yellow">Số mét thực tế</span></li>
  <li><span class="num yellow">10</span> Ghi chú nếu cần, và nhấn <span class="btn-demo yellow">Lưu thông tin </span> để hoàn thành.</li>
</ul>
<div class="ok">
✅ <strong>Thành công:</strong> Xuất hiện thông báo "Lưu thành công".
</div>
</div>
<div class="right">
<img src="screenshots/g04_scan_03_result.png" alt="Kết quả quét mã vải" />
<div class="caption">Màn hình sau khi quét thành công</div>
</div>
</div>

---

<!-- _class: divider coat -->

<div class="tag green">II. Xác Nhận Tráng</div>

## Tạo mã tráng mới

**Mục đích:** Sau khi tráng phủ xong một cuộn vải, dùng để khai báo thành phẩm tráng mới vào hệ thống.

**Ai sử dụng:** Công nhân tổ tráng vải.

---

# II — Xác Nhận Tráng: Vị Trí Trên Màn hình chính

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num green">1</span>Trên Màn hình chính cuộn xuống.</li>
  <li><span class="num green">2</span> Tìm thẻ <strong>"Xác Nhận Tráng"</strong> — có biểu tượng checklist màu xanh.</li>
  <li><span class="num green">3</span> Nhấn nút <span class="btn-demo green">Truy cập →</span> bên dưới thẻ.</li>
</ul>
<div class="tip">
💡 Nếu không thấy thẻ này, liên hệ quản lý để được cấp quyền <strong>"Xác Nhận Tráng"</strong>.
</div>
</div>
<div class="right">
<img src="screenshots/g01_dash_coating_cards.png" alt="Màn hình chính sản xuất" />
<div class="caption">Cuộn xuống tìm thẻ "Xác Nhận Tráng"</div>
</div>
</div>

---

# II — Xác Nhận Tráng: Thiết Lập

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num green">4</span> Phần <strong>"Thiết lập quét"</strong> xuất hiện ở trên cùng.</li>
  <li><span class="num green">5</span> Chọn <strong>"Máy Thực Hiện"</strong> — chọn máy tráng bạn đang dùng.</li>  
   <li><span class="num green">6</span> Tại mục <strong>"QUÉT VẢI ĐỂ TRÁNG"</strong> → nhấn <span class="btn-demo green">📷 BẬT CAMERA</span> hoặc nhập mã thủ công.</li>
</ul>
<div class="tip">
💡 <strong>Trạm in</strong> là máy in tem. Hỏi quản lý nếu có nhiều máy không biết chọn cái nào.<br>
Chọn Thành phẩm ở ô đầu tiên (ví dụ: V – Vải).
</div>
</div>
<div class="right">
<img src="screenshots/g06_coat_01_setup.png" alt="Thiết lập xác nhận tráng" />
<div class="caption">Chọn thành phẩm, máy và trạm in</div>
</div>
</div>

---

# II — Xác Nhận Tráng: Quét & Kết Quả

<div class="layout">
<div class="left">
<ul class="steps">  
<li><span class="num green">7</span> Nhập <strong>"Số mét vải dùng (m):"</strong>.</li>
  <li><span class="num green">8</span> Nhập <strong>"Dài tráng thành phẩm thu được"</strong>.</li>
  <li><span class="num green">9</span> Nhập <strong>"Tổng GSM Thành phẩm (Vải + Lami)"</strong>.</li>
  <li><span class="num green">10</span> Chọn <strong>"Tùy chọn xử lý Khổ Màng"</strong>.</li>
  <ul class="steps" style="margin-left: 20px;"> 
    Giữ nguyên khổ | Xén biên (Nhập khổ mới) | Chia đôi (Tạo 2 cuộn mới)
    </ul> 
  <li><span class="num green">11</span> Nhấn nút <span class="btn-demo green">TẠO MÃ TEM TRÁNG MỚI</span> ở dưới cùng.</li>
</ul>
<div class="tip">
Chọn <strong>"Đơn Hàng"</strong> sử dụng tùy chọn.<br>
💡 Khi chọn Chia đôi, hệ thống sẽ tự động tạo một tem mới cho phần vải dư ra ngoài (nếu có), giúp bạn quản lý tồn kho chính xác.<br>
Tự động thu hồi phần biên dư (Sinh mã Mộc mới cất kho cho dải dư khi xén / lệch khổ)
</div>
<div class="ok">
✅ <strong>Thành công:</strong> Hệ thống tạo tem tráng mới và in tự động tại trạm in đã chọn.
</div>
</div>
<div class="right">
<img src="screenshots/g08_coat_03_declare_form.png" alt="Kết quả xác nhận tráng" />
<div class="caption">Màn hình sau khi tạo mã tem tráng thành công</div>
</div>
</div>

---

<!-- _class: divider update -->

<div class="tag blue">III. Cập Nhật Tráng</div>

## Cập nhật số mét thực tế sau tráng

**Mục đích:** Sau khi cuộn vải tráng được đo lại, dùng chức năng này để cập nhật số mét thực tế vào hệ thống.

**Ai sử dụng:** Công nhân tổ tráng vải.

---

# III — Cập Nhật Tráng: Vị Trí Trên Màn hình chính

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num blue">1</span> Trên Màn hình chính cuộn xuống.</li>
  <li><span class="num blue">2</span> Tìm thẻ <strong>"Cập Nhật Tráng"</strong> — có biểu tượng bút chì màu xanh dương.</li>
  <li><span class="num blue">3</span> Nhấn nút <span class="btn-demo">Truy cập →</span> bên dưới thẻ.</li>
</ul>
<div class="tip">
💡 Nếu không thấy thẻ này, liên hệ quản lý để được cấp quyền <strong>"Cập Nhật Tráng"</strong>.
</div>
</div>
<div class="right">
<img src="screenshots/g01_dash_update_coating_cards.png" alt="Màn hình chính sản xuất" />
<div class="caption">Cuộn xuống tìm thẻ "Cập Nhật Tráng"</div>
</div>
</div>

---

# III — Cập Nhật Tráng: Quét Mã & Cập Nhật

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num blue">4</span> Tại mục "QUÉT MÃ CẬP NHẬT" → nhấn 📷 BẬT CAMERA hoặc nhập mã thủ công</li>
  <li><span class="num blue">5</span> Kiểm tra thông tin chi tiết của mã tráng.</li>
    <li><span class="num blue">6</span> Cập nhật lại <strong>GSM Thành phẩm</strong>.</li>  
  <li><span class="num blue">7</span> Nhấn nút <span class="btn-demo blue">CẬP NHẬT</span> ở dưới cùng.</li>
</ul>
<div class="tip">
💡 Chọn lại Đơn hàng cần cập nhật (nếu cần).
</div>
<div class="ok">
✅ <strong>Thành công:</strong> Thông tin số mét và GSM được cập nhật vào hệ thống.
</div>
</div>
<div class="right">
<img src="screenshots/g10_update_02_list.png" alt="Danh sách cuộn tráng" />
<div class="caption">Màn hình cập nhật thông tin cuộn tráng</div>
</div>
</div>


---

<!-- _class: divider wh -->

<div class="tag teal">IV. Nhập Kho</div>

## Quét mã hàng và gán vị trí kho

**Mục đích:** Ghi nhận hàng hóa (cuộn vải tráng) vào kho, gán vị trí kệ cụ thể.

**Ai sử dụng:** Nhân viên kho.

---

# IV — Nhập Kho: Vị Trí Trên Màn hình chính

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num teal">1</span> Trên Màn hình chính cuộn xuống.</li>
  <li><span class="num teal">2</span> Tìm thẻ <strong>"Nhập Kho"</strong> có biểu tượng kho màu xanh ngọc.</li>
  <li><span class="num teal">3</span> Nhấn nút <span class="btn-demo" style="background:#0d9488;">Truy cập →</span> bên dưới thẻ.</li>
</ul>
<div class="tip">
💡 Thẻ "Nhập Kho" thường nằm ở nhóm <strong>Kho Hàng</strong> phía dưới Màn hình chính.
</div>
</div>
<div class="right">
<img src="screenshots/g02_dash_warehouse_card.png" alt="Thẻ Nhập Kho trên Màn hình chính" />
<div class="caption">Thẻ "Nhập Kho" trong nhóm Kho Hàng</div>
</div>
</div>

---

# IV — Nhập Kho: Chọn Hình Thức Nhập

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num teal">4</span> Màn hình <strong>Cấu Hình</strong> xuất hiện với 3 lựa chọn:</li>
</ul>
<br>
<table style="width:100%;border-collapse:collapse;font-size:0.88em;">
  <tr style="background:#0d9488;color:white;">
    <th style="padding:8px;">Lựa chọn</th>
    <th style="padding:8px;">Khi nào dùng?</th>
  </tr>
  <tr style="background:#f0fdfa;">
    <td style="padding:8px;font-weight:700;">📦 Nhập Tạm</td>
    <td style="padding:8px;">Nhập nhanh, chưa gán kệ</td>
  </tr>
  <tr style="background:white;">
    <td style="padding:8px;font-weight:700;">📍 Nhập + Vị Trí</td>
    <td style="padding:8px;">Nhập và gán kệ ngay</td>
  </tr>
  <tr style="background:#f0fdfa;">
    <td style="padding:8px;font-weight:700;">🔑 Xác Nhận Vị Trí</td>
    <td style="padding:8px;">Gán kệ cho hàng đã nhập "Tạm"</td>
  </tr>
</table>
<div class="tip">
💡 Chọn một hình thức rồi nhấn <strong>TIẾP THEO →</strong>
</div>
</div>
<div class="right">
<img src="screenshots/g12_wh_01_options.png" alt="Chọn hình thức nhập kho" />
<div class="caption">Bước 1: Chọn hình thức nhập kho</div>
</div>
</div>

---

# IV — Nhập Kho: Cách 1 — Nhập Tạm

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num teal">1</span> Chọn <strong>"📦 Nhập Tạm"</strong> và nhấn <strong>TIẾP THEO →</strong>.</li>
  <li><span class="num teal">2</span> Tại màn hình Quét Mã, nhấn <span class="btn-demo" style="background:#0d9488;">📷 BẬT CAMERA</span> để quét.</li>
  <li><span class="num teal">3</span> Hoặc gõ mã: <code>Code...</code> rồi nhấn <strong>▶</strong>.</li>
  <li><span class="num teal">4</span> Hệ thống báo <strong>"ĐÃ NHẬP KHO"</strong> (Trạng thái Tạm).</li>
</ul>
<div class="tip">
💡 Hệ thống ghi nhận hàng vào kho nhưng chưa gán vị trí cụ thể.
</div>
</div>
<div class="right">
<img src="screenshots/g13_wh_02_scan_step.png" alt="Quét mã Nhập Tạm" />
<div class="caption">Màn hình quét mã Nhập Tạm</div>
</div>
</div>

---

# IV — Nhập Kho: Cách 2 — Nhập + Vị Trí

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num teal">1</span> Chọn <strong>"📍 Nhập + Vị Trí"</strong> và nhấn <strong>TIẾP THEO →</strong>.</li>
  <li><span class="num teal">2</span> <strong>Nhập vị trí:</strong> Gõ <code>Code vị trí...</code> rồi nhấn <strong>TIẾP THEO →</strong>.</li>
  <li><span class="num teal">3</span> Quét hoặc gõ mã: <code>Code...</code> rồi nhấn <strong>▶</strong>.</li>
  <li><span class="num teal">4</span> Hệ thống báo xác nhận đã nhập vào đúng vị trí kệ.</li>
</ul>
<div class="ok">
✅ <strong>Thành công:</strong> Hàng được gán vào kệ <code>Code...</code> ngay lập tức.
</div>
</div>
<div class="right">
<img src="screenshots/g13_wh_02_scan_step.png" alt="Quét mã kèm vị trí" />
<div class="caption">Quét mã sau khi đã gán vị trí Code...</div>
</div>
</div>

---

# IV — Nhập Kho: Cách 3 — Xác Nhận Vị Trí

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num teal">1</span> Chọn <strong>"🔑 Xác Nhận Vị Trí"</strong> và nhấn <strong>TIẾP THEO →</strong>.</li>
  <li><span class="num teal">2</span> <strong>Chọn vị trí:</strong> Gõ kệ đích (Ví dụ: <code>Code vị trí...</code>).</li>
  <li><span class="num teal">3</span> Quét mã hàng đang ở trạng thái "Tạm": <code>Code...</code>.</li>
  <li><span class="num teal">4</span> Nhấn <strong>Hoàn thành</strong> để kết thúc.</li>
</ul>
<div class="ok">
✅ <strong>Kết quả:</strong> Kiện hàng được chuyển từ kho Tạm sang vị trí Kệ chính thức.
</div>
</div>
<div class="right">
<img src="screenshots/g13_wh_02_scan_step.png" alt="Xác nhận vị trí" />
<div class="caption">Cập nhật vị trí kệ cho hàng tồn tạm</div>
</div>
</div>



---

<!-- _class: split -->

# 🤝 Lắng Nghe & Cải Tiến
**Phần mềm được thiết kế để phục vụ chính công việc của anh chị!**

<div class="row">
<div class="left">

### Góc trao đổi:
- Thao tác bấm/quét mã trên điện thoại có **chậm hay khó dùng** không?
- Chữ và nút bấm trên màn hình có bị **nhỏ quá** không?
- Anh chị có hay bị **mất mạng, xoay vòng vòng** khi đang đứng ở chuyền không?
- Có thao tác nào làm **mất nhiều thời gian** hơn so với ghi sổ tay cũ không?

### 💡 Tinh thần hợp tác
- **Sửa lỗi:** Hệ thống mới chắc chắn cần thời gian làm quen, mong anh chị em cứ phản hồi ngay nếu thấy vướng mắc.
- **Làm chủ công nghệ:** Mỗi ý kiến khen/chê của anh chị đều giúp đội IT điều chỉnh app "mượt" và sát thực tế hơn.

<div class="ok">
✅ <strong>Tiếp nhận phản hồi:</strong> Mọi vướng mắc xin báo lại cho Quản đốc hoặc nhắn thẳng vào nhóm Zalo Hỗ Trợ IT!
</div>

</div>
</div>

---

<!-- _class: end -->

# 🙏 Hoàn Thành!
**Trao đổi hỏi đáp ghi nhận ý kiến đóng góp**
**Chúc bạn thao tác thuận lợi.**

<br>

📞 **Liên hệ IT hỗ trợ: 0906 585 600**

💬 **Zalo nhóm hỗ trợ:** Hỗ Trợ Hệ Thống QR

