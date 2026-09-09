<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIdentity extends Model
{
    use HasDateTimeFormatter;

    public const PROVIDER_VISITOR = 'visitor';
    public const PROVIDER_EMAIL = 'email';
    public const PROVIDER_WEB3 = 'web3';
    public const PROVIDER_TAPTAP = 'taptap';
    public const PROVIDER_WECHAT_MP = 'wechat_mp';

    public const STATUS_ACTIVE = 0;
    public const STATUS_DISABLED = 1;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
