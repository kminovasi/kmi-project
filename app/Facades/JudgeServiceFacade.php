<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class JudgeServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'JudgeService'; // Nama dari service
    }
}
