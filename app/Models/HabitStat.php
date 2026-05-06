<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class HabitStat extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'habit_stats';
    protected $guarded = [];
}
