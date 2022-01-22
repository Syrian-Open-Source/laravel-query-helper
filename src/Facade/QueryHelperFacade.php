<?php

namespace SOS\QueryHelper\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \SOS\QueryHelper\Classes\UpdateHelper updateInOneQueryInstance()
 * @method static \SOS\QueryHelper\Classes\DeleteHelper deleteInstance()
 * @method static \SOS\QueryHelper\Classes\UpdateHelper updateInstance()
 * @method static \SOS\QueryHelper\Classes\InsertHelper insertInstance()
 * @method static \SOS\QueryHelper\Classes\JoinHelper joinInstance()
 */
class QueryHelperFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'QueryHelperFacade';
    }
}
