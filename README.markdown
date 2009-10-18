nzbVR
=====

*nzbVR is currently in alpha, some functionality does not exist (movies + full iphone ui)*

A fast automatic downloader for usenet reports, handles scheduled tv series downloads, movies and generic searches through Newzbin. nzbVR also provides a simple interface for searching Newzbin and sending reports straight to SABnzbd. nzbVR is written with PHP5 and requires a web server (like Apache) with PHP5 installed and configured.

Like other episode downloaders like hellaVCR or EpisodeButler, nzbVR allows you to easily schedule watchers for new episodes (it can even help downloading the missing ones in your collection). 

nzbVR uses web API services from Newzbin, TVRage and TVDB, as well as consuming API's through your local network from applications like SABnzbd and XBMC.

Screenies
---------

### Default main skin

![nzbVR 0.2 - Series](http://farm3.static.flickr.com/2702/4017631907_b54ed0a1ae.jpg)
![nzbVR 0.2 - Searches](http://farm3.static.flickr.com/2695/4018393476_191289bc87.jpg)
![nzbVR 0.2 - Watchers](http://farm3.static.flickr.com/2449/4017631463_933c4fea81.jpg)

### iPhone Skin

![nzbVR 0.2 - iPhone Skin - Dashboard](http://farm3.static.flickr.com/2640/4017595775_36f10725b8.jpg)
![nzbVR 0.2 - iPhone Skin - Series Info](http://farm3.static.flickr.com/2482/4018362390_6885365558.jpg)

For more screenshots see the project page on [sixones][].

[sixones]: http://sixones.com/projects/nzbvr "sixones.com"

Features
--------

* Scheduled episode downloads
* Automatic searches for movies
* Generic automatic searches
* Newzbin search interface
* XBMC notification

Todo
----

* Automatic execution of `git pull` from web ui *in progress*
* Movie support with integration for meta data from third-parties (need an external db service) *in progress*
* <del>Scheduling generic searches</del>
* <del>Sending search result to SABnzbd+</del>
* Support for downloading nzb to a watched folder
* NZBMatrix search support
* iPhone web interface *in progress*
* XBMC notification *in progress*

Requirements
------------

* Git (required for install and auto-update)
* SABnzbd+ (0.5+)
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

Everything can be configured through the nzbVR web interface.

If you plan on installing nzbVR in a sub-directory rather than through a virtual host you will need to visit your installation so a `settings.xml` file is generated. You can then manually edit `data/settings.xml` and change the `<base_url>` tag value to `/url/to/nzbvr/`. This also applies if you have no mod-rewrite available but you will need to change the `<base_url>` tag value to `/url/to/nzbvr/index.php/` (make sure to include the trailing slash). The base url setting can be changed from the web interface but on first launch the default url will be incorrect and the dynamic content will fail to load.

Watchers
--------

nzbVR uses a collection of generic watchers to define a Newzbin search, a watcher can set a name, format, language, source and category this is then used to automatically create Newzbin search queries using the v3 API. You can use the web interface to create `watchers` to monitor for anything on Newzbin <del>or NZBMatrix</del>.

By default nzbVR defines 2 special types of watchers;

### Series Watchers

Series's watcher's can be setup to watch for new episodes for a particular tv series, it works with daily shows (as long as your scheduling nzbVR to check at least once every 24 hours). <del>nzbVR also pulls in extra data for a tv series so that you can browse the episode lists and download missing episodes easily.</del> When you first add a series, nzbVR will check for new episodes that are being aired after the creation date of the series in nzbVR. 

### Movie Watchers

Movie watcher's are used to check for upcoming movies automatically, since watchers allow the setting of format and source you can create movie watchers to automatically download movies in various formats as they become available).

Scheduling
----------

You can schedule nzbVR to automatically check and update `watchers` through cron or launchctl (on OSX only). This allows nzbVR to automatically check and download new episodes and monitored searches without any input. 

To create a cron task schedule the command below (do not set the execution to lower than every 15 minutes);

	php /path/to/nzvbr/check.php >> /path/to/nzvbr/data/nzbvr.log
	
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