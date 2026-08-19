import re
import base64
import os

md_path = r"C:\Users\hp\.gemini\antigravity-ide\brain\7149bee2-a84f-42f0-986f-d7de30972392\project_documentation.md"
doc_path = r"C:\Users\hp\OneDrive\Documents\GitHub\Hybrid block chain project\Project_Documentation.doc"

with open(md_path, 'r', encoding='utf-8') as f:
    md_content = f.read()

def process_mermaid(match):
    mermaid_code = match.group(1).strip()
    # base64 encode for mermaid.ink
    b64 = base64.urlsafe_b64encode(mermaid_code.encode('utf-8')).decode('utf-8')
    img_url = f"https://mermaid.ink/img/{b64}"
    return f'<br><img src="{img_url}" alt="Diagram" /><br>'

# Replace mermaid blocks
html_content = re.sub(r'```mermaid(.*?)```', process_mermaid, md_content, flags=re.DOTALL)

# Simple Markdown to HTML conversions
html_content = re.sub(r'^### (.*?)$', r'<h3>\1</h3>', html_content, flags=re.MULTILINE)
html_content = re.sub(r'^## (.*?)$', r'<h2>\1</h2>', html_content, flags=re.MULTILINE)
html_content = re.sub(r'^# (.*?)$', r'<h1>\1</h1>', html_content, flags=re.MULTILINE)
html_content = re.sub(r'\*\*(.*?)\*\*', r'<b>\1</b>', html_content)
html_content = re.sub(r'\*(.*?)\*', r'<i>\1</i>', html_content)
html_content = html_content.replace('---', '<hr>')

# Handle newlines
html_content = html_content.replace('\n', '<br>\n')

# Wrap in Word-friendly HTML headers
word_html = f"""<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head><title>Project Documentation</title>
<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom><w:DoNotOptimizeForBrowser/></w:WordDocument></xml><![endif]-->
<style>
body {{ font-family: 'Calibri', sans-serif; font-size: 11pt; }}
h1 {{ color: #2E74B5; font-size: 24pt; }}
h2 {{ color: #2E74B5; font-size: 18pt; border-bottom: 1px solid #ccc; }}
h3 {{ color: #1F4D78; font-size: 14pt; }}
</style>
</head>
<body>
{html_content}
</body>
</html>
"""

with open(doc_path, 'w', encoding='utf-8') as f:
    f.write(word_html)

print("Created " + doc_path)
