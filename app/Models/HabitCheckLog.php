<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class HabitCheckLog extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'habit_check_logs';
    protected $guarded = [];
}
