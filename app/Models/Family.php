<?php

namespace App\Models;

use App\Models\User;
use App\Models\Village;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Family extends Model
{
    use HasFactory;
    protected $fillable = ['village_id'];
    protected $rules = [
        'family_code' => 'required|unique:families,family_code',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($family) {
            $family->family_code = $family->generateUniqueCode();
        });
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function headOfFamily()
    {
        return $this->hasOne(User::class)->where('is_head_of_family', true);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    protected function generateUniqueCode()
    {
        $code = strtoupper(Str::random(8)); // Generate an 8-character code
        
        // Check if the code already exists
        while (static::where('family_code', $code)->exists()) {
            $code = strtoupper(Str::random(8)); // Generate a new code if it exists
        }

        return $code;
    }
}
