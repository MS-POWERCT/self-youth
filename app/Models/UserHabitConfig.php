<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class UserHabitConfig extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'user_habit_configs';

    protected $guarded = [];
}
