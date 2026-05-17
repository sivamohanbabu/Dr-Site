import os
from pypdf import PdfReader

pdf_files = [
    "GitHub-Deployment-Hosting-PageSpeed-Performance-Report.pdf",
    "Mobile-PageSpeed-Performance-Fixes-Report.pdf",
    "PageSpeed-SEO-Challenges-and-Fixes-Report.pdf",
    "Skanda-Design-Studio-feature-change-report.pdf",
    "Dr-Ranjith-Optimization-Report.pdf",
    "Dr-Ranjith-Deployment-History-Report.pdf"
]

for pdf_file in pdf_files:
    if os.path.exists(pdf_file):
        print(f"--- SUMMARY OF {pdf_file} ---")
        try:
            reader = PdfReader(pdf_file)
            text = ""
            for i in range(min(3, len(reader.pages))): # Get first 3 pages
                text += reader.pages[i].extract_text()
            # print first 500 chars to get the gist
            print(text[:500].replace('\n', ' ').strip())
            print("\n")
        except Exception as e:
            print(f"Error reading {pdf_file}: {e}")
