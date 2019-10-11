<?php

namespace Yamilovs\Bundle\SypexGeoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yamilovs\Bundle\SypexGeoBundle\DependencyInjection\SypexGeoExtension;

class SypexGeoBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SypexGeoExtension();
    }
}
