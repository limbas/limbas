#! /bin/sh
XML=$1
DTD=$2

xmllint --noout --dtdvalid $DTD $XML 2>&1

ret=$?

if [ $ret -eq 0 ] ; then
	echo Document $XML does validate against $DTD
fi

exit $ret

