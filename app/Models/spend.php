<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class spend extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'month_name',
        'total_per_month',
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'total_per_month'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(SpendDetail::class, 'spend_id', 'id');
    }

    public function getMonthNameAttribute(Type $var = null)
    {
        $month = [
            1 =>   'Januari',
          'Februari',
          'Maret',
          'April',
          'Mei',
          'Juni',
          'Juli',
          'Agustus',
          'September',
          'Oktober',
          'November',
          'Desember'
        ];

        return $month[$this->month];
    }

    public function getTotalPerMonthAttribute()
    {
        return $this->details->sum('total');
    }
}
