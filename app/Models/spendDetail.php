<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class spendDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'created_at', 'updated_at', 'spend_id'
    ];

    public function spend()
    {
        return $this->belongsTo(Spend::class, 'spend_id', 'id');
    }
}
