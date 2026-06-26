<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class FarmUserTask extends Model
{
    use HasDateTimeFormatter;
    protected $table = 'farm_user_tasks';
    protected $guarded = [];
    public function farmTask()
    {
        return $this->belongsTo(FarmTask::class, 'farm_task_id');
    }

    // 状态
    public static $status_color = [
        0 => 'info',
        1 => 'success',
        2 => 'danger',
    ];
}
