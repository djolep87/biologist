<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WastePlan extends Model
{
    protected $fillable = [
        'company_name',
        'created_by',
        'form_data',
        'plan_content',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
