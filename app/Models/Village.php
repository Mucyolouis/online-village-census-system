<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;

    protected $table = 'villages';

    protected $fillable = [
        'name',
        'cell_id',
    ];

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
