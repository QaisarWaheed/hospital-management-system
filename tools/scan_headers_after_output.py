#!/usr/bin/env python3
import os
import re

root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
hits = []
for dirpath, _, files in os.walk(root):
    if any(x in dirpath for x in ["node_modules", ".git", "vendor", "tools/scan"]):
        continue
    for fn in files:
        if not fn.endswith(".php"):
            continue
        path = os.path.join(dirpath, fn)
        try:
            text = open(path, encoding="utf-8", errors="ignore").read()
        except OSError:
            continue
        if "header(" not in text:
            continue
        for m in re.finditer(r"header\s*\(", text):
            before = text[: m.start()]
            close_tags = list(re.finditer(r"\?>", before))
            if not close_tags:
                continue
            last_close = close_tags[-1].start()
            between = before[last_close + 2 :].strip()
            if between:
                hits.append(path)
                break

for p in sorted(set(hits)):
    print(p)
print("TOTAL:", len(set(hits)))
