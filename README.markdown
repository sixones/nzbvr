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

* Automatic update through git

Requirements
------------

* Git (required for install and auto-update)
* SABnzbd
* Newzbin account
* Apache + PHP5 (mod-rewrite + virtual host recommended but not required)
* XBMC (optional)
	
Installing
----------

Installing 

	# Get the source via Git
	git clone git://github.com/sixones/nzbvr.git
	
	cd nzbvr
	
	# Make the `data` directory writable
	chmod 777 data/
	
	# Get the latest picnic framework source (see http://github.com/sixones/picnic)
	git clone git://github.com/sixones/picnic.git picnic
	
	# Go to your web browser and open up the virtual host or folder through your web server
	
Configuration
-------------

No need to edit files, all the settings can be configured through the nzbVR web interface.
	
Checking
--------

Setup a scheduled cron that will execute the command below;

	php /path/to/nzvbr/check.php >> /path/to/nzvbr/nzbvr.log
	
[Picnic]: http://github.com/sixones/picnic "Picnic"