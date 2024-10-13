<?php

namespace App\Models;

use Closure;
use Exception;
use Filament\Panel;
use App\Models\Family;
use Spatie\Image\Enums\Fit;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Models\Role;
use Filament\Models\Contracts\HasName;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasAvatar, HasName, HasMedia
{
    use InteractsWithMedia;
    use HasUuids, HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

    public function getFilamentName(): string
    {
        return $this->username;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // if ($panel->getId() === 'admin') {
        //     return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        // }

        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getMedia('avatars')?->first()?->getUrl() ?? $this->getMedia('avatars')?->first()?->getUrl('thumb') ?? null;
    }

    // Define an accessor for the 'name' attribute
    public function getNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-shield.super_admin.name'));
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
    
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function isHeadOfFamily()
    {
        return $this->is_head_of_family;
    }

    public function isMarried(): bool
    {
        return $this->marital_status === 'married';
    }

    public function transferRequest()
    {
        return $this->hasMany(TransferRequest::class);
    }

    public function approve()
    {
        $this->update(['is_approved' => true]);
        //Assign the 'christian' role to the new user
        $citizenRole = Role::where('name', 'citizen')->first();
        if ($citizenRole) {
            $this->assignRole($citizenRole);
        }
    }


    public function scopeVisibleToUser($query, User $user)
    {
        if ($user->hasRole('cov')) {
            return $query->where('village_id', $user->village_id);
        } elseif ($user->hasAnyRole(['super_admin', 'coc'])) {
            return $query;
        } else {
            return $query->where('id', $user->id);
        }
    }
}
