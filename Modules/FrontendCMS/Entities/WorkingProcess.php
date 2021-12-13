<?php

namespace Modules\FrontendCMS\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkingProcess extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected static function newFactory()
    {
        return \Modules\FrontendCMS\Database\factories\WorkingProcessFactory::new();
    }
}
