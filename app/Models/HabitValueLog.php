<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HabitValueLog extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'habit_value_logs';
    protected $guarded = [];
}
