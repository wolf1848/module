<?php

namespace Wolf1848\Module\Traits;

trait GetTableName{
    public static function getName(){
        return with(new static)->getTable();
    }
}
