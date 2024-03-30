<?php

namespace App\Service;

class ListService
{
    private static $linkTypeService = ["Soi", "Conjoint", "Familial", "Professionnel"];

    public static function getLinkTypeService(){
        return self::$linkTypeService;
    }

}
