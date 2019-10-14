[![Build Status](https://travis-ci.org/yamilovs/SypexGeoBundle.svg?branch=master)](https://travis-ci.org/yamilovs/SypexGeoBundle)

SypexGeoBundle
==============

This is an adaptation of [Sypex Geo Library](https://github.com/yamilovs/SypexGeo) for Symfony.
 
Installation
------------

### Step 1: Download SypexGeoBundle using composer

Add SypexGeoBundle by running the command:

```bash
$ composer require yamilovs/sypex-geo-bundle:^2.0
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Yamilovs\Bundle\SypexGeoBundle\SypexGeoBundle(),
    );
}
```

### Step 3: Add some configurations

```yaml
# app/config/config.yml

yamilovs_sypex_geo:
    mode: FILE # FILE (default) | BATCH | MEMORY
    database_path: "%kernel.root_dir%/../var/SypexGeoDatabase/SxGeoCity.dat"
```

If you need the proxy configuration for database update, you can add:
```yaml
yamilovs_sypex_geo:
    ......
    connection:
        proxy:
            host: 'xxx.xxx.xxx.xxx'
            port: # port number
            
            # You can enable user credentials if you have them
            auth:
                user: 'your username'
                password: 'your password'
```


### Step 4: Download necessary databases

Download necessary databases to `database_path`. 
- You can run `php bin/console yamilovs:sypex-geo:update-database-file`
- Or download it manually from [Sypex Geo City](https://sypexgeo.net/files/SxGeoCity_utf8.zip)

Usage
-----

### In your controller
```php
<?php
// src/Controller/FooController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Yamilovs\SypexGeo\SypexGeo;

class FooController extends Controller
{
    public function fooAction(Request $request, SypexGeo $sypexGeo)
    {
        $userIp = $request->getClientIp();
        $testIp = '88.86.218.24';

        $city = $sypexGeo->getCity($testIp, true);

        dump($city);
    }
}
```

**Note:**

> Your local ip address is 127.0.0.1 and Sypex Geo cant get your city or country!

### If you want to check data from specific IP address
You can run `php bin/console yamilovs:sypex-geo:get-ip-data aa.bb.cc.dd`
