<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeightRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'weight',
        'unit',
        'recorded_at',
        'note',
    ];

    protected $casts = [
        'weight'      => 'float',
        'recorded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** 统一换算为 kg */
    public function weightInKg(): float
    {
        if ($this->unit === 'jin') {
            return round($this->weight / 2, 2);
        }

        return (float) $this->weight;
    }

    /** 获取该用户上一条体重记录（按 recorded_at） */
    public function previous(): ?self
    {
        return static::where('user_id', $this->user_id)
            ->where('recorded_at', '<', $this->recorded_at)
            ->orderByDesc('recorded_at')
            ->first();
    }

    /** 相对上一条记录的体重变化（kg，负数为下降） */
    public function changeFromPrevious(): ?float
    {
        $previous = $this->previous();
        if (!$previous) {
            return null;
        }

        return round($this->weightInKg() - $previous->weightInKg(), 2);
    }

    public function toApiArray(bool $withChange = false): array
    {
        $data = [
            'id'          => $this->id,
            'weight'      => $this->weight,
            'unit'        => $this->unit ?? 'kg',
            'recorded_at' => $this->recorded_at?->format('Y-m-d H:i:s'),
            'note'        => $this->note,
        ];

        if ($withChange) {
            $data['change'] = $this->changeFromPrevious();
        }

        return $data;
    }
}
