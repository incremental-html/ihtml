#!/usr/bin/env bash
#
# add ihtml to $PATH
#
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
export PATH="$PATH:$SCRIPT_DIR/../.."
set -o xtrace
#
# website example
#
ihtml -p doc/examples/example-website/example-website-project/ -o doc/examples/example-website/example-website-generated/