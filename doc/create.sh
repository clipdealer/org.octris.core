#
# This file is part of the 'org.octris.core' package.
#
# (c) Harald Lapp <harald@octris.org>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#

#**
# Create HTML and PDF documentation from markdown files.
#
# @octdoc       h:doc/create.sh
# @copyright    copyright (c) 2011 by Harald Lapp
# @author       Harald Lapp <harald@octris.org>
#**

function usage {
    echo "usage: create.sh manual pdf|html"
    exit 1
}

if [[ "$1" = "" ]] || [[ ! -d "$1" ]]; then
    usage
fi

if [[ "$2" = "" ]] || [[ "$2" != "pdf" && "$2" != "html" ]]; then
    usage
fi

OUT="/tmp/$1.$2"

if [[ "$2" = "pdf" ]]; then
    CMD="markdown2pdf -o $OUT"
else
    CMD="pandoc -f markdown -t html -o $OUT"
fi

for i in `find manual/* -type f -name "[0-9]*-*.markdown"`; do
    cat $i
done | $CMD
