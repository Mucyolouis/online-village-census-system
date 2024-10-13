<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectors';

    protected $fillable = [
        'name',
        'district_id',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function cells()
    {
        return $this->hasMany(Cell::class);
    }
}
