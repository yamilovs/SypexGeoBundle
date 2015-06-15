SypexGeoBundle
==============

This is an adaptation of [Sypex Geo Library](https://sypexgeo.net/) for Symfony2.
 
Installation
------------

### Step 1: Download FOSUserBundle using composer

Add YamilovSSypexGeoBundle by running the command:

``` bash
$ php composer.phar require yamilovs/sypex-geo-bundle dev-master
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new YamilovS\SypexGeoBundle\SypexGeoBundle(),
    );
}
```

### Step 3: Download necessary databases

Download necessary databases to `src/YourCompanyName/YourBundle/SypexGeoDatabase` (or any other path of your choice). 
- [Sypex Geo City](https://sypexgeo.net/files/SxGeoCity_utf8.zip)

### Step 4: Add some configurations

``` yaml
# app/config/config.yml

sypex_geo:
    city_database_path: YourCompanyName\YourBundle\SypexGeoDatabase\SxGeoCity.dat
```

### Step 5: In your controller
``` php
<?php
// src/Acme/FooBundle/Controller/BarController.php
namespace Acme\FooBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use YamilovS\SypexGeoBundle\Manager\SypexGeoManager;

class BarController extends Controller
{
    public function indexAction(Request $request)
    {
        /* @var $sypex_geo SypexGeoManager */
        $sypex_geo = $this->get('sypex_geo.manager');
        $user_ip = $request->getClientIp();
        $test_ip = '8.8.8.8';

        $city_data = $sypex_geo->getCity($test_ip);
        dump($city_data);
    }
}
```

**Note:**

> Your local ip address is 127.0.0.1 and Sypex Geo cant get your city or country!
