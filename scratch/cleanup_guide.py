
import os
import re

file_path = r'resources\views\guide.blade.php'

def cleanup():
    with open(file_path, 'rb') as f:
        content_bytes = f.read()
    
    try:
        content = content_bytes.decode('utf-8')
        encoding = 'utf-8'
    except UnicodeDecodeError:
        content = content_bytes.decode('cp1258')
        encoding = 'cp1258'

    # 1. Remove double/triple newlines that might have been introduced
    # But be careful not to break HTML structure. 
    # Actually, the view_file tool showed line numbers with gaps.
    # Let's just normalize multiple newlines to double newlines at most.
    content = re.sub(r'\n\s*\n\s*\n+', '\n\n', content)
    
    # 2. Fix Section 9 duplication
    # Find all occurrences of 'id="section-mobile-guide"'
    occurrences = [m.start() for m in re.finditer('id="section-mobile-guide"', content)]
    if len(occurrences) > 1:
        # Keep only the LAST one (which is usually in tab-content)
        # But wait, let's look at the context.
        # The first one is at line 679 (in the 888-line file), the second at 755.
        # Both are in the tab-content area. I should remove the first one.
        
        # Find the start of the first one (search backwards for {{-- SECTION: MOBILE GUIDE --}})
        first_occ = occurrences[0]
        start_search = content.rfind('{{-- SECTION: MOBILE GUIDE --}}', 0, first_occ)
        if start_search == -1:
            start_search = content.rfind('<div class="tab-pane', 0, first_occ)
        
        # Find the end of the first one (search for the next </div>\n\n                        </div> or something)
        # Section 9 has 3 nested divs: tab-pane, card, card-body.
        # So it ends with 3 </div> tags.
        search_from = first_occ
        for _ in range(3):
            search_from = content.find('</div>', search_from + 1)
        
        end_of_first = content.find('</div>', search_from + 1)
        if end_of_first != -1:
            end_of_first += 6
            # Remove it
            content = content[:start_search] + content[end_of_first:]

    # 3. Final check on Sidebar
    # Make sure we don't have multiple Item 9 in sidebar
    sidebar_items = re.findall(r'href="#section-mobile-guide"', content[:5000])
    if len(sidebar_items) > 1:
        # Remove the first one
        occ = content.find('href="#section-mobile-guide"')
        # Find the start of the <a> tag
        tag_start = content.rfind('<a', 0, occ)
        tag_end = content.find('</a>', occ) + 4
        content = content[:tag_start] + content[tag_end:]

    with open(file_path, 'w', encoding=encoding) as f:
        f.write(content)
    
    print("Cleanup Success")

cleanup()
