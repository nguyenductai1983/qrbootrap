import re

with open('public/mobile_guide.md', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace everything from the first '# III — Cập Nhật Tráng: Danh Sách & Chọn Cuộn' down to just before '<!-- _class: divider wh -->'
pattern = re.compile(r'# III — Cập Nhật Tráng: Danh Sách & Chọn Cuộn.*?---.*?(?=<!-- _class: divider wh -->)', re.DOTALL)

replacement = """# III — Cập Nhật Tráng: Danh Sách & Chọn Cuộn

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num blue">4</span> Trang hiển thị danh sách <strong>"VỮA TRÁNG XONG (MOBILE)"</strong> ở phía dưới — các cuộn đang chờ cập nhật.</li>
  <li><span class="num blue">5</span> Số bên phải mỗi dòng (225.5, 222.5…) là số mét hiện tại cần cập nhật.</li>
  <li><span class="num blue">6</span> Nhấn vào cuộn cần sửa trong danh sách.</li>
  <li><span class="num blue">7</span> Hoặc quét/gõ mã cuộn vào ô <strong>"Quét mã tem..."</strong> rồi nhấn <strong>▶</strong> để tìm nhanh.</li>
</ul>
<div class="tip">
💡 Danh sách chỉ hiển thị các cuộn vừa tráng xong hôm nay.
</div>
</div>
<div class="right">
<img src="screenshots/g10_update_02_list.png" alt="Danh sách cuộn tráng" />
<div class="caption">Danh sách cuộn tráng chờ cập nhật số mét</div>
</div>
</div>

---

# III — Cập Nhật Tráng: Nhập Số Mét

<div class="layout">
<div class="left">
<ul class="steps">
  <li><span class="num blue">8</span> Form cập nhật hiện ra với thông tin cuộn vải: mã tráng, loại vải, số mét cũ.</li>
  <li><span class="num blue">9</span> Nhấn vào ô <strong>"Số mét thực tế"</strong> và gõ số mét chính xác.</li>
  <li><span class="num blue">10</span> Nhấn nút <span class="btn-demo">Lưu</span> để xác nhận cập nhật.</li>
</ul>
<div class="ok">
✅ Số mét được cập nhật ngay trong hệ thống.
</div>
</div>
<div class="right">
<img src="screenshots/g11_update_03_edit_form.png" alt="Form cập nhật số mét" />
<div class="caption">Form nhập số mét thực tế</div>
</div>
</div>

---

"""

new_content, count = pattern.subn(replacement, content)
print(f'Replaced {count} times')

with open('public/mobile_guide.md', 'w', encoding='utf-8') as f:
    f.write(new_content)
