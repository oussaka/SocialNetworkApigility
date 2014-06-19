#!/usr/bin/env sh
SRC_DIR="`pwd`"
cd "`dirname "$0"`"
cd "../zfcampus/zf-deploy/bin"
BIN_TARGET="`pwd`/zfdeploy.php"
cd "$SRC_DIR"
"$BIN_TARGET" "$@"
