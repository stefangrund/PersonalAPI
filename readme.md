#Personal API

Personal API collects your social media and quantified self data from external services to archive, use and *really* own it. It's a full featured RESTful API, which supports all CRUD operations with simple HTTP requests.

I wrote my bachelor thesis at the University of Cologne about the concept of such an API, which originates from [Naveen Selvadurai's personal API](http://x.naveen.com/post/51808692792/a-personal-api). I think a personal API is a great way to collect and interact with every aspect of your digital self, e.g. your tweets, check-ins, bookmarks, tracked steps, weight, or sleep duration. Everything will be saved in your own database so that you don't have to rely on different platforms, companys and device manufacturers anymore. **[You can read my thesis or download the PDF or eBook here](http://stefangrund.de/personalapi/)** (but obviously it's in German ;)).

<a href="http://api.stefangrund.de/"><img src="http://stefangrund.de/personalapi/img/github_frontpage.gif" alt=""></a>

If you want to play around with a Personal API see mine at [api.stefangrund.de](http://api.stefangrund.de/).

## Table of Contents

* [Getting Started](#getting-started)
    * [System requirements](#system-requirements)
    * [Installation](#installation)
    * [Configuring modules](#configuring-modules)
    * [Updating your database](#updating-your-database)
* [Documentation](#documentation)
    * [Authentication](#authentication)
    * [Resources / URL design](#resources--url-design)
        * [Parameters](#parameters)
        * [Available resources](#available-resources)
    * [Requests](#requests)
        * [Example API call](#example-api-call)
    * [Error handling](#error-handling)


## Getting Started

The first Personal API prototype is working, but has a lot of issues, e.g. the module system is very *hacky* and there's only support for a few services (see [list of modules](https://github.com/stefangrund/PersonalAPI/wiki/Modules)). I highly recommend not to use it in production environments right now, unless you know what you're doing.

###System Requirements

The Personal API runs under PHP and MySQL. I tested it with PHP 5.5.3 and MySQL 5.5.33. The steps module requires the PECL-extension [oauth](http://pecl.php.net/package/oauth). Also it's recommended to run a cron job several times a day to fetch data from external services (see [Updating your database](#updating-your-database)). 

###Installation

1. Again: Be sure you know what you're doing. Personal API isn't finished yet and it's not recommended to use it in production environments right now!
2. Download a [(pre-)release](https://github.com/stefangrund/PersonalAPI/releases).
3.  Edit `inc/config.php` and add your database information.
4. Upload the Personal API files to the desired location on your web server. It's recommended to install your Personal API under a subdomain like _api.yourname.com_. If you do so, you're almost done. Otherwise you'll have to edit the Personal API paths in `config.php` and `.htaccess`.
5. Run the installation script by accessing the Personal API's URL in a web browser and follow the instructions.

That's it, you're done! :)

###Configuring Modules

After you've successfully installed your Personal API, you'll need to configure the modules in order to get data from external services into the API's database. You'll find instructions for every service in the module selection.

###Updating your Database

After you've entered the required API keys, usernames, etc. into the modules' settings, you'll need to update your database. This will load your data from the external services and add it to your API's database. You can update your database manually by visiting this page or automatically by creating a cron job for this URL:

`http://api.yourname.com/update.php?token=MASTERTOKEN`

Run this cron job daily or several times a day for best results.

##Documentation

Here you'll find a full documentation for your APIs functions.

###Authentication

Access tokens are required for any interaction with your API. While the API supports all CRUD operations, only reading data is allowed for the public. You can create new tokens and give them different permissions but two tokens are already generated after you've installed your API:

* A **public token** which is displayed on the public front page and enables anyone to read data from your database.
* And a **master token** which enables you (and only you - keep it secret!) to perform any action on your database like creating, reading, updating and deleting data.

Just append the `token` parameter to your requested URL like this (otherwise you'll get an response with HTTP status code 401 "Unauthorized"):

`http://api.yourname.com/v1/places?token=TOKEN`

You can see the number of API requests of each token on the token page under "Usage".

###Resources / URL Design

The API is modeled around the different resources. A resource is a data type controlled by a module. Every resource has two types of URLs:

`http://api.yourname.com/v1/places`

`http://api.yourname.com/v1/places/1234`

The first URL represents the whole collection, the second one is specific for one element in this collection. Therefore `/places/1234` represents the 1234th element in the resource/collection _Places_.

####Parameters

Use the parameter `date=YYYY-MM-DD` to limit the timespan of your request. Use `format` to determine the format of the response (you can choose between the default `json` or `xml`) and `count` to determine the number of items in the response (default is 25, maximum is 200).

####Available Resources

Right now [these resources](https://github.com/stefangrund/PersonalAPI/wiki/Modules) can be used within the Personal API.

###Requests

To operate on the resources you can use the HTTP verbs POST, GET, PUT and DELETE which match the four CRUD operations (Create, Read, Update, Delete). Not every request method is allowed with every resource, e.g. you can't delete a whole collection for security reasons. Use the request methods like this:

Resource | POST | GET | PUT | DELETE
---|---|---|---|---
/statuses | Creates new element | Shows all elements | - | -
/statuses/123 | - | Shows element 123 | Updates element 123 | Deletes element 123

####Example API call

A complete GET request for `/v1/statuses` with all parameters will look like this:

`http://api.yourname.com/v1/statuses?date=2014-07-18&count=5&format=xml&token=TOKEN`

To request single items from a resource just add `/id to a call:

`http://api.yourname.com/v1/steps/35?token=TOKEN`

###Error Handling

If there is an error or unauthorized behaviour the API will answer with a following status codes and a error message. These status codes are supported:

Code | Message | Description
--- | --- | ---
200 | OK | Will be sent with every working request.
201 | Created | Resource successfully created.
304 | Not Modified | Resource couldn't be updated.
400 | Bad Request | Something is wrong with the request.
401 | Unauthorized | No permission for this action.
404 | Not found | Resource couldn't be found.
500 | Internal Server Error | Something wrent wrong within the API.

The API error message response (in JSON) will look like this:

```json
{
     "code": 401,
     "message": "Unauthorized",
     "description": "Your token is missing or not valid."
}
```
