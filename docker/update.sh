#!/bin/sh
set -eu

cd "$(dirname "$0")/.."
if [ -n "$(git status --porcelain)" ]; then
    printf '%s\n' 'Ada perubahan lokal. Simpan perubahan sebelum memperbarui SI-CAPAT.' >&2
    exit 1
fi

previous_revision=$(git rev-parse HEAD)
git pull --ff-only
printf 'Revisi sebelumnya: %s\n' "$previous_revision"
docker compose config --quiet
docker compose up -d --build --wait --wait-timeout 180
git rev-parse --short HEAD
