
import sys

file_path = r'resources\views\guide.blade.php'

# Sidebar target
sidebar_target = """                        <a href="#section-printstation" class="list-group-item list-group-item-action fw-bold text-info"
                            data-bs-toggle="list">
                            7. Trạm In Kiosk
                        </a>"""

sidebar_replacement = """                        <a href="#section-printstation" class="list-group-item list-group-item-action fw-bold text-info"
                            data-bs-toggle="list">
                            7. Trạm In Kiosk
                        </a>
                        <a href="#section-mobile-guide" class="list-group-item list-group-item-action fw-bold text-primary bg-light"
                            data-bs-toggle="list">
                            <i class="fa-solid fa-graduation-cap me-2"></i> 9. Tài liệu Đào tạo Mobile
                        </a>"""

# Tab content target - the last </div> of a tab-pane before admin
# Let's find section-printstation's closing div.
# Based on the previous view_file of guide_with_lines.txt:
# 319:                         </div>
# 320: 
# 321:                         @role('admin')

tab_target = """                        </div>

                        @role('admin')"""

tab_replacement = """                        </div>

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

                        @role('admin')"""

try:
    # Try reading as binary to avoid decoding issues, then decode cautiously
    with open(file_path, 'rb') as f:
        content_bytes = f.read()
    
    # Try different encodings
    encodings = ['utf-8', 'windows-1258', 'latin-1']
    content = None
    used_encoding = None
    
    for enc in encodings:
        try:
            content = content_bytes.decode(enc)
            used_encoding = enc
            print(f"Successfully decoded with {enc}")
            break
        except UnicodeDecodeError:
            continue
    
    if content is None:
        print("Failed to decode with any standard encoding. Using utf-8 with replace.")
        content = content_bytes.decode('utf-8', errors='replace')
        used_encoding = 'utf-8'

    # Do replacements
    new_content = content.replace(sidebar_target, sidebar_replacement)
    if new_content == content:
        print("Warning: Sidebar target not found!")
    
    final_content = new_content.replace(tab_target, tab_replacement)
    if final_content == new_content:
        print("Warning: Tab content target not found!")

    # Write back
    with open(file_path, 'w', encoding=used_encoding) as f:
        f.write(final_content)
    
    print(f"Successfully updated {file_path}")

except Exception as e:
    print(f"Error: {e}")
    sys.exit(1)
