# Web Page Scraper

The first step is to clone the repo from git
```
git clone https://github.com/shaneexley/scraper.git
```

Head into the repository

```
cd scraper
```

Assuming you have composer installed already run the below command for composer to load the dependencies

```
composer update
```

Now run the console application with the sainsburies product URL; this will give use the expected JSON results

```
app/console scrape http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html

```

# PHP Unit Test

From the same location run the below command to run the PHP Unit Suite

```
vendor/bin/phpunit -c phpunit.xml
```
