SypexGeoBundle
==============

This is an adaptation of [Sypex Geo Library](https://sypexgeo.net/) for Symfony2.
 
Installation
------------

### Step 1: Download YamilovsSypexGeoBundle using composer

Add YamilovsSypexGeoBundle by running the command:

```bash
$ php composer.phar require yamilovs/sypex-geo-bundle ^1.2
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
        new YamilovS\SypexGeoBundle\YamilovsSypexGeoBundle(),
    );
}
```

### Step 3: Add some configurations

```yaml
# app/config/config.yml

yamilovs_sypex_geo:
    mode: SXGEO_FILE # SXGEO_FILE (default) | SXGEO_BATCH | SXGEO_MEMORY
    database_path: "%kernel.root_dir%/../var/SypexGeoDatabase/SxGeoCity.dat"
```
> Don't forget to create @YourBundle/SypexGeoDatabase folder

If you need proxy configuration for database update, you can add:
```yaml
yamilovs_sypex_geo:
    ......
    connection:
        proxy:
            host: 'xxx.xxx.xxx.xxx:port'
            auth: 'ITLM_DOMEN\user:password'
```


### Step 4: Download necessary databases

Download necessary databases to `database_path`. 
- You can run `php app/console yamilovs:sypex-geo:update-database-file`
- Or download it manually from [Sypex Geo City](https://sypexgeo.net/files/SxGeoCity_utf8.zip)

Usage
-----

### In your controller
```php
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
        $sypex_geo = $this->get('yamilovs.sypex_geo.manager');
        $user_ip = $request->getClientIp();
        $test_ip = '8.8.8.8';

        $city_data = $sypex_geo->getCity($test_ip);
        dump($city_data);
    }
}
```

**Note:**

> Your local ip address is 127.0.0.1 and Sypex Geo cant get your city or country!

### If you want to check data from specific IP address
You can run `php app/console yamilovs:sypex-geo:get-ip-data aa.bb.cc.dd`
