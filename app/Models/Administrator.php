<?php

namespace App\Models;

use Asundust\DcatAuthGoogle2Fa\Models\AdminUser as BaseAdminUser;

class Administrator extends BaseAdminUser
{
    protected $fillable = [
        'username',
        'password',
        'name',
        'avatar',
        'status',
        'google_two_fa_enable',
        'google_two_fa_secret',
    ];
}
