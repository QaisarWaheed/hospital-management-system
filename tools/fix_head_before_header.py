#!/usr/bin/env python3
"""Remove redundant session header() after head.php when connect.php already authenticates."""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# connect.php already redirects unauthenticated users
REDUNDANT_PATTERNS = [
    re.compile(
        r"<\?php include 'includes/connect\.php'; \?>\s*"
        r"<\?php include 'includes/head\.php'; \s*"
        r"if\s*\(\s*!isset\(\$_SESSION\['fr_id'\]\)\s*\)\s*\{\s*"
        r"header\s*\(\s*['\"]location:\s*logout\.php['\"]\s*\);\s*"
        r"\}\s*\?>",
        re.MULTILINE,
    ),
    re.compile(
        r"<\?php include 'includes/connect\.php'; \?>\s*"
        r"<\?php include 'includes/head\.php';\s*"
        r"if\s*\(\s*empty\(\$_SESSION\['fr_id'\]\)\s*\)\s*\{\s*"
        r"header\s*\(\s*['\"]Location:\s*logout\.php['\"]\s*\);\s*"
        r"exit;\s*"
        r"\}\s*",
        re.MULTILINE,
    ),
]

REPLACEMENTS = [
    (
        re.compile(
            r"(<\?php include 'includes/connect\.php'; \?>)\s*"
            r"(<\?php include 'includes/head\.php';)\s*"
            r"if\s*\(\s*!isset\(\$_SESSION\['fr_id'\]\)\s*\)\s*\{\s*"
            r"header\s*\(\s*['\"]location:\s*logout\.php['\"]\s*\);\s*"
            r"\}\s*",
            re.MULTILINE,
        ),
        r"\1\n\2\n",
    ),
    (
        re.compile(
            r"(<\?php include 'includes/connect\.php'; \?>)\s*"
            r"(<\?php include 'includes/head\.php';)\s*"
            r"if\s*\(\s*empty\(\$_SESSION\['fr_id'\]\)\s*\)\s*\{\s*"
            r"header\s*\(\s*['\"]Location:\s*logout\.php['\"]\s*\);\s*"
            r"exit;\s*"
            r"\}\s*",
            re.MULTILINE,
        ),
        r"\1\n\2\n",
    ),
    (
        re.compile(
            r"(<\?php include 'includes/connect\.php'; \?>)\s*"
            r"(<\?php include 'includes/head\.php';)\s*"
            r"if\s*\(\s*!isset\(\$_SESSION\['ao_id'\]\)\s*\)\s*\{\s*"
            r"header\s*\(\s*['\"]location:\s*logout\.php['\"]\s*\);\s*"
            r"\}\s*",
            re.MULTILINE,
        ),
        r"\1\n\2\n",
    ),
]

def process_file(path):
    with open(path, encoding="utf-8", errors="ignore") as f:
        text = f.read()
    orig = text
    for pat, repl in REPLACEMENTS:
        text = pat.sub(repl, text)
    if text != orig:
        with open(path, "w", encoding="utf-8") as f:
            f.write(text)
        return True
    return False

def main():
    changed = []
    for module in ("fr", "ao"):
        for dirpath, _, files in os.walk(os.path.join(ROOT, module)):
            for fn in files:
                if not fn.endswith(".php"):
                    continue
                p = os.path.join(dirpath, fn)
                if process_file(p):
                    changed.append(p)
    print("\n".join(changed))
    print("CHANGED:", len(changed))

if __name__ == "__main__":
    main()
