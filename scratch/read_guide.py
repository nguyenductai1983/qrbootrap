
import sys
import os

file_path = r'resources\views\guide.blade.php'
output_path = r'guide_with_lines.txt'

try:
    with open(file_path, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()
except Exception as e:
    print(f"Error: {e}")
    sys.exit(1)

with open(output_path, 'w', encoding='utf-8') as f:
    for i, line in enumerate(lines, 1):
        f.write(f"{i}: {line}")

print(f"Done. Wrote {len(lines)} lines to {output_path}")
