nzbVR
=====

A fast automatic downloader for usenet reports, handles scheduled tv series downloads, movies and generic searches through Newzbin. nzbVR also provides a simple interface for searching Newzbin and sending reports straight to SABnzbd.

Like other episode downloaders like hellaVCR or EpisodeButler, nzbVR allows you to easily schedule watchers for new episodes (it can even help downloading the missing ones in your collection). 

nzbVR uses web API services from Newzbin, TVRage and TVDB, as well as consuming API's through your local network from applications like SABnzbd and XBMC.

Features
--------

* Scheduled episode downloads
* Automatic searches for movies
* Generic automatic searches
* Newzbin search interface
* XBMC notification

Todo
----

* Automatic execution of `git pull` from web ui
* Movie support with integration for meta data from third-parties
* Scheduling generic searches
* Sending search result to SABnzbd+
* Support for downloading nzb to a watched folder
* NZBMatrix search support
* iPhone web interface

Requirements
------------

* Git (required for install and auto-update)
* SABnzbd
* Newzbin account
* Apache + PHP5 (mod-rewrite + virtual host recommended but not required)
* XBMC (optional)
	
Installing
----------

	# Get the source via Git
	git clone git://github.com/sixones/nzbvr.git
	
	cd nzbvr
	
	# Make the `data` directory writable
	chmod 777 data/ data/series data/movies
	
	# Get the latest picnic framework source (see http://github.com/sixones/picnic)
	git clone git://github.com/sixones/picnic.git picnic
	
	# Go to your web browser and open up the virtual host or folder through your web server
	
Upgrading
---------

	cd nzbvr
	
	# Update via Git
	git pull origin master
	
	cd picnic
	
	# Update picnic via Git
	git pull origin master
	
Configuration
-------------

No need to edit files, all the settings can be configured through the nzbVR web interface.
	
Checking
--------

Setup a scheduled cron that will execute the command below;

	php /path/to/nzvbr/check.php >> /path/to/nzvbr/nzbvr.log
	
Under OS X the recommended method for running the scheduler is through launchd, there is a default plist in the `extras` directory that can be easily modified and enabled;

	cd nzbvr
	
	# Edit `extras/org.sixones.nzbvr.plist` (you need to set the abolsolute path to `nzbvr/check.php` and the username)
	mate extras/org.sixones.nzbvr.plist
	
	# Copy to `/Library/LaunchDaemons/org.sixones.nzbvr.plist`
	sudo cp extras/org.sixones.nzbvr.plist /Library/LaunchDaemons/org.sixones.nzbvr.plist
	
	# Enable
	sudo launchctl load /Library/LaunchDaemons/org.sixones.nzbvr.plist
	
The default plist sets the task to schedule every 15 minutes (900 seconds), its not recommended to set below 15 minutes as nzbVR doesnt include any validation or security for the data being written (it will in the future).
	
[Picnic]: http://github.com/sixones/picnic "Picnic"

License
-------

The MIT License

Copyright (c) 2009 Adam Livesley (sixones)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.