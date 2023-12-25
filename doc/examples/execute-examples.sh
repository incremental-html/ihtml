#!/usr/bin/env bash
#
# add ihtml to $PATH
#
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
export PATH="$PATH:$SCRIPT_DIR/../.."
set -o xtrace

cd doc/examples/projects/ || exit
#
# project example
#
ihtml -p example-website/example-website-project/ \
      -o example-website/example-website-generated/
#
# single file example
#
ihtml ../static-html-bundles/html5up-stellar/generic.html \
      ccs/white-space.ccs \
      -o ccs/examples/generated.html
# to see results:
# diff -w static-html-bundles/html5up-stellar/generic.html ccs/examples/generated.html

ihtml ../static-html-bundles/html5up-stellar/generic.html \
      ccs/white-space.ccs \
      -o ccs/examples/generated.html
# to see results:
# diff -w static-html-bundles/html5up-stellar/generic.html ccs/examples/generated.html
