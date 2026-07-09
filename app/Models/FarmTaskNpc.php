<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmTaskNpc extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'farm_task_npc';
    
}
