InmareliberoFirestarterBundle
=============================

FirestarterBundles helps to start a Symfony2 project, avoiding the repetitive tasks performed on every fresh installation.


## How to install

Add the following line to your `composer.json`:

```
{
    "require": {
        "inmarelibero/firestarter-bundle": "dev-master"
    }
}
```

or, in the console:

    php composer.phar require inmarelibero/firestarter-bundle:dev-master

Add the following line to `app/AppKernel.php`

    new Inmarelibero\FirestarterBundle\InmareliberoFirestarterBundle()

## Use

In the console, give:

    app/console inmarelibero_firestarter:start

and follow the interactive procedure.