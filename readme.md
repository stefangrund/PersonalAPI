#Personal API

Personal API collects your social media and quantified self data from external services to archive, use and *really* own it. It's a full featured RESTful API, which supports all CRUD operations with simple HTTP requests.

I wrote my bachelor thesis at the University of Cologne about the concept of such an API, which originates from [Naveen Selvadurai's personal API](http://x.naveen.com/post/51808692792/a-personal-api). I think a personal API is a great way to collect and interact with every aspect of your digital self, e.g. your tweets, check-ins, bookmarks, tracked steps, weight, or sleep duration. Everything will be saved in your own database so that you don't have to rely on different platforms, companys and device manufacturers anymore. **[You can read my thesis or download the PDF or eBook here](http://stefangrund.de/personalapi/)** (but obviously it's in German ;)).

<a href="http://api.stefangrund.de/"><img src="http://stefangrund.de/personalapi/img/screenshot_api.gif" alt="" style="border: 1px solid #000;"></a>
If you want to play around with a Personal API see mine at [api.stefangrund.de](http://api.stefangrund.de/).

## Getting started

The first Personal API prototype is working, but has a lot of issues, e.g. the module system is very *hacky* and there's only support for a few services (see [list of modules](https://github.com/stefangrund/PersonalAPI/wiki/Modules)). I highly recommend not to use it in production environments right now, unless you know what you're doing.

Now that I've warned you, here's a [guide on how to set up your Personal API](https://github.com/stefangrund/PersonalAPI/wiki/How-to-set-up-Personal-API%3F).

##Documentation

The Personal API has a simple token based authentication system and outputs it's contents in the JSON or XML format. There are different endpoints, resources and parameters available. You can read about it in this [guide on how to use your Personal API](https://github.com/stefangrund/PersonalAPI/wiki/How-to-use-Personal-API%3F).
