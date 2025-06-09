#!/usr/bin/env sh

for file in $(basename -s .jpg ./*.jpg)
do
    # get icc profiles
    docker run -v .:/imgs dpokidov/imagemagick "$file.jpg" "$file.icc"

    # convert to avif, strip exif and restore icc
    docker run -v .:/imgs dpokidov/imagemagick "$file.jpg" -strip -quality 65 -profile "$file.icc" "$file.avif"
done
