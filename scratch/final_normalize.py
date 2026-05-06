
import os
import re

file_path = r'resources\views\guide.blade.php'

def final_normalize():
    with open(file_path, 'rb') as f:
        content_bytes = f.read()
    
    try:
        content = content_bytes.decode('utf-8')
        encoding = 'utf-8'
    except UnicodeDecodeError:
        content = content_bytes.decode('cp1258')
        encoding = 'cp1258'

    # Normalize newlines: replace 3 or more newlines with just 2
    # Also handle the whitespace between them
    content = re.sub(r'\n\s*\n\s*\n+', '\n\n', content)
    
    # Remove leading/trailing whitespace from the whole file
    content = content.strip() + '\n'

    with open(file_path, 'w', encoding=encoding) as f:
        f.write(content)
    
    print("Final Normalization Success")

final_normalize()
