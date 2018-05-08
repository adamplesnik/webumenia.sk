# Welcome

[Web umenia](http://www.webumenia.sk) is an open platform to explore digitized art collections from public galleries and museums. 

Web umenia is developed by [lab.SNG](http://lab.sng.sk/): the digital R&D lab of the Slovak National Gallery. We are working with public art organisations to make their art accessible and explorable online so curious people around the world can learn more about works of art and the context they've been created in.

Web umenia offers a user-friendly way to search and explore art from collections of multiple galleries and museums.

Digital reproductions of public domain artworks from several collections are available for download in high resolution for both personal and commercial use

Art organisation with digitized artworks organised in a Collection Management System can easily publish their art via CSV imports or OAI-PMH harvests. If you would like to know more about using Web umenia in your organisation, contact us via [lab@sng.sk](mailto:lab@sng.sk)

Developers interested to build applications on top of Web umenia can use our API. See our [wiki on GitHub](https://github.com/SlovakNationalGallery/web-umenia-2/wiki/ElasticSearch-Public-API) for more info.

# Contributing

We greatly encourage others to get involved! See [our contributing guidelines](CONTRIBUTING.md) for more info about different ways to contribute to Web umenia.

We are committed to providing a welcoming and inspiring community for all and expect others who participate in the project to honour our [code of conduct](CODE_OF_CONDUCT.md).

# Tech setup

## Requirements

This software is built with the [Laravel5 framework](http://laravel.com/).

It requires
* PHP 5.5.9+
* MySQL
* Elasticsearch

## Local Installation

Here are the steps for installation on a local machine.

1. Clone this repository.
    ```
    git@github.com:SlovakNationalGallery/web-umenia-2.git webumenia/
    cd webumenia/
    ```
2. setup database in your favourite database editor. set:
    * db name
    * username
    * password
3. configure access to your database in `/app/config/database.php` 
4. set `.env` file. you can copy values from `.env.example`
5. Run `composer install` to fulfil required libraries. 
6. Make sure elasticsearch is running. you can set the index name in `app/config/app.php`
7. Run migrations to setup the database with `php artisan migrate --seed` 

## Local Installation with Docker

This requires docker-compose
these steps will set up a linked system of 3 containers -
	* php / apache webserver that contains our application code
	* database container
	* elasticsearch container
that will communicate internally with one another

1. Clone this repository.
    ```
    git@github.com:SlovakNationalGallery/web-umenia-2.git webumenia/
    cd webumenia/
    ```
2. create a .env file (you can use the included env.example as a base)
3. build the whole stack (mysql, elasticsearch, laravel php app + apache server)
with docker-compose:
	```
	docker-compose build
	```
the first time you do this it will take a while, a lot of different components
need to be fetched from remote servers.  
Be patient, subsequent builds won't take nearly as long.    
3. start the app  
	```
	docker-compose up
	```
	or  
	```
	docker-compose up -d
	``` 
	to run it in the background.
	(In this case you can watch the output of a component like this: `docker-compose logs -f web`)  
4. run migrations  
	```
	docker-compose exec web php artisan migrate --seed
	```  
5. setup elasticsearch  
	```
	docker-compose exec web php artisan es:setup
	```  
6. visit http://localhost:8080 in your browser to have a look  

to stop the dockerized application: `docker-compose down`  


### Harvesting Data

You can now fill artworks by adding them manually or importing using the "spice harvester" (harvests using OAI-PMH protocol) using `php artisan oai-pmh:harvest` and choosing to harvest `1 [item] Europeana SNG`. Or login to admin at `http://yourlocalhost/admin` using default credentials `admin`/`admin` and go to `Spice Harvester` -> 'Spustit'.

### IIPImage

This application uses [IIPImage server](http://iipimage.sourceforge.net/) for zoomable (and downloadable) images.

IIPImage must be seen locally, what can be achieved using a ProxyForwarding.

To enable image server for default oai set (Europeana), put this lines of code into your virtualhost setup:

```
ProxyPass /fcgi-bin/iipsrv.fcgi http://imi.sng.cust.eea.sk/publicIS/fcgi-bin/iipsrv.fcgi

ProxyPassReverse /fcgi-bin/iipsrv.fcgi http://imi.sng.cust.eea.sk/publicIS/fcgi-bin/iipsrv.fcgi
```

### Updating Elastic Search

* necessary steps are specified in the file `ES2_migration_steps.txt`
* command to generate ES2 compatible index:
`php artisan es:setup`
* command to reindex data to the index
`php artisan es:reindex`

## Maintainer

This project is maintained by [lab.SNG](http://lab.sng.sk). If you have any questions please don't hesitate to ask them by creating an issue or email us at [lab@sng.sk](mailto:lab@sng.sk).

## License

Source code in this repository is licensed under the MIT license. Please see the [License File](LICENSE) for more information.
