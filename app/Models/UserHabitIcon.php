<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class UserHabitIcon extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'user_habit_icon';
    protected $guarded = [];
}
