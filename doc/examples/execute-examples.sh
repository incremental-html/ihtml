#!/usr/bin/env bash
#
# add ihtml to $PATH
#
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
export PATH="$PATH:$SCRIPT_DIR/../.."
set -o xtrace

cd doc/examples/ || exit
#
# project example
#
ihtml -p example-website/example-website-project/ \
      -o example-website/example-website-generated/
#
# single file example
#
ihtml example-static-template/html5up-stellar/generic.html \
      ccs/white-space.ccs \
      -o ccs/examples/generated.html
# to see results:
# diff -w example-static-template/html5up-stellar/generic.html ccs/examples/generated.html
