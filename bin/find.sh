#!/bin/sh

# parse command line
if [ $# != 1 ]; then
  echo "usage: find.sh path pattern"
  exit 1
fi

grep -rnw . -e $1
