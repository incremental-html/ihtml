#!/usr/bin/env bash
#
# add ihtml to $PATH
#
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
export PATH="$PATH:$SCRIPT_DIR/../.."
# set -o xtrace

cd docs/examples/ || exit

#
# project example
#
ihtml -p example-project-website/project/ \
      -o example-project-website/generated/ -v

#
# single file example
#
ihtml static-html-bundles/html5up-stellar/generic.html \
      example-file-whitespace/white-space.ccs \
      -o example-file-whitespace/generated.html -v
diff -w static-html-bundles/html5up-stellar/generic.html \
        example-file-whitespace/generated.html \
        > example-file-whitespace/report.txt

#
# inheritance analysis example
#
ihtml -e example-file-inheritance/ccs/hierarchy.ccs -v \
      > example-file-inheritance/report.txt

#
# Classes example
#
ihtml static-html-bundles/html5up-stellar/generic.html \
      example-file-class/class.ccs \
      -o example-file-class/generated.html -v
diff -w static-html-bundles/html5up-stellar/generic.html \
     example-file-class/generated.html \
     > example-file-class/report.txt

#
# example of url() inside a code snippet
#
ihtml static-html-bundles/html5up-stellar/generic.html \
      -r '#header p { content: "Lorem ipsum: " url(example-file-urlincode/snippet.ihtml) }' \
      -o example-file-urlincode/generated.html -v
