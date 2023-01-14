<?php

namespace App\Models;

use App\Traits\FactoryExploreFIle;
use App\Traits\Upload;
use Error;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Upload;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($Object) {
            $Object->username = Str::snake("$Object->name $Object->id");
        });
        static::updated(function($Object) {
            $Object->username = Str::snake("$Object->name $Object->id");
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'following_id');
    }

    public function follower()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'following_id', 'user_id');
    }

    public function getProfileAttribute($value)
    {
        return $this->getUrl($value);
    }

    public function setProfileAttribute($value)
    {
        if (Str::startsWith($value,'data:')) {
            try {
                $baseValue = $this->base64Upload($value);
                $this->attributes['profile'] = $this->base64Upload($value);
            } catch (Exception $error) {
                /* Throwing error to debug */
                throw new Error('Failed to upload user profile, error: '. $error->getMessage());
            }
        } else {
            $this->attributes['profile'] = $value;
        }
    }
}
