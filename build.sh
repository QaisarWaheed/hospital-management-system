#!/usr/bin/env bash
# Local build: use XAMPP PHP (has openssl) when default php does not.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT"

if [ -x /c/xampp/php/php.exe ]; then
  PHP=/c/xampp/php/php.exe
elif command -v php >/dev/null 2>&1; then
  PHP=php
else
  echo "PHP not found. Install XAMPP or add php to PATH." >&2
  exit 1
fi

if [ ! -f composer.phar ]; then
  echo "Downloading Composer..."
  "$PHP" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  "$PHP" composer-setup.php --quiet
  rm -f composer-setup.php
fi

"$PHP" composer.phar install --no-interaction --prefer-dist
"$PHP" vendor/bin/phpunit
"$PHP" tests/run_all.php
