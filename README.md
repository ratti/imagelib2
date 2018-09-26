License: GPL 3.0

This is an image gallery for huge collections. Work of progress, documentation ist still missing.

Required stuff for those who know how to deal with it:
composer
brew install findutils (on macOS)
imagemagick
ffmpeg 
ufraw
exiftool
php7.2

create config.php from the DIST-version.

Create a database with charset utf8mb4 and import INIT.sql
guest user is included, others can be added. Password is bcrypt 12 encoded.

See bin/console for commands how to index the image folder, create derived images and videos. Use symfony webserver from console. 
Login is guest:guest.

While other namnes will work, best results are when 1 folder = 1 event, folder names like "2009_12_24__Christmas_at_parents_house'.


Feel free to work with this tool, however, don't expect me to deliver service, bugfixes, whatever — I am happy with works-for-me, 
since I use it mainly for myself.

But also if you fix something — feel free to send a pull request.

Jörg Roßdeutscher
joerg at mondaydevice it
