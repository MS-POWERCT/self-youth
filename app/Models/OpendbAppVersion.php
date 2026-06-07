<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class OpendbAppVersion extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'opendb_app_versions';
    public $timestamps = false;

}
