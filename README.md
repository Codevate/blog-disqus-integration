# Disqus Blog

A companion project for our blog post on integrating the [Disqus](https://disqus.com/) comment system.

![Example Screenshot](/web/img/post-sample-image.jpg?raw=true "Example Screenshot")

## Getting started

Install dependencies:

```
composer install
```

Create the database, setup the schema, and load the fixtures:

```
php app/console doctrine:database:create --if-not-exists
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load
```

Start the server:

```
php app/console server:run
```

You can now log in with the username and password `test` at http://127.0.0.1:8000/login

## Troubleshooting

* Make sure you've entered values for all of the disqus parameters in `parameters.yml`
* Check that SSO is enabled for your account (use this [contact form](https://disqus.com/support/?article=Integrating%20Single%20Sign-On))

## Credits

- [Clean Blog template](https://startbootstrap.com/template-overviews/clean-blog/) provided by [David Miller](http://davidmiller.io/).

### About Codevate
Codevate is a specialist [app development company](https://www.codevate.com/) that builds cloud-connected software. This repository was created for a blog post about a [custom web application development](https://www.codevate.com/services/web-development) project and was written by Chris Lush.
