#!/bin/bash

TEMPDIR='silverstripe-compare'
SSDIRS="sapphire cms jsparty"
DIFFFILE="silverstripe-$(date '+%y-%m-%d').diff"
EXAMPLE="Example usage: ./silverstripe-compare \"2.3.3\" \"/path/to/silverstripe/root\" [http://svn.silverstripe.com/open/phpinstaller/tags/]"

echo ${1:?$EXAMPLE}
echo ${2:?$EXAMPLE}

cd ~

if [ -d ./$TEMPDIR ]
then
    echo "Temp directory ~/$TEMPDIR/ exists"
else
    echo "Creating temp directory ~/'$TEMPDIR'"
    mkdir $TEMPDIR
fi

cd ~/$TEMPDIR

if [ -d ./$1 ]
then
    echo "Temp directory ~/$TEMPDIR/$1/ exists"
else

	if [ $3 ]
	then
		TAG="$3$1"
	else
		TAG="http://svn.silverstripe.com/open/phpinstaller/tags/$1"
	fi
    echo "Exporting from silverstripe: $TAG"
    svn export $TAG ./$1
fi

if [ -f $DIFFFILE ]
then
    echo "Deleting ~/$TEMPDIR/$DIFFFILE"
    rm ./$DIFFFILE
fi

echo "Creating ~/$TEMPDIR/$DIFFFILE"
touch ./$DIFFFILE

for DIR in $SSDIRS
do
    echo "Comparing ./$1/$DIR/ $2/$DIR/"
    diff -ruN -x .svn ./$1/$DIR/ $2/$DIR/ >> $DIFFFILE
done

echo "Copying diff file to $2/$DIFFFILE"
cp $DIFFFILE $2/$DIFFFILE