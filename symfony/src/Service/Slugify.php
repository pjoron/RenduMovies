<?php 

namespace App\Service;

use Cocur\Slugify\Slugify as CocurSlugify;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Slugify
{
    public function slugify($stringtosugifly)
    {
        $slugify = new CocurSlugify();
        return $slugify->slugify($stringtosugifly);
    }
}
