<?php

namespace App\Models;

use App\Traits\Upload;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, Upload;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'images'
    ];
    protected $casts = [
        'images' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getThumbnailAttribute($value)
    {
        return $this->getUrl($value);
    }

    /**
     * Return Is User Like The Posts
     *
     * @return boolean
     */
    public function getIsLikeAttribute()
    {
        return (bool) $this->postInteractions()->where('user_id', auth()->id())->where('post_id', $this->id)->where('type', 'like')->count();
    }

    public function postInteractions()
    {
        /**
         * @var Model
         */
        $auth = auth()->user();
        return $this->hasMany(PostInteraction::class, 'post_id')->whereIn('user_id', $auth->whereHas('follower')->pluck('id'));
    }

    public function getImagesAttribute($value)
    {
        $images = [];
        foreach (json_decode($value) as $value) {
            $images[] = $this->getUrl($value);
        }
        return $images;
    }

    public function setImagesAttribute($values)
    {
        try {
            $getUploads = $this->base64Uploads($values);
            $this->attributes['images'] = $getUploads;
            /* Upload for thumbnail, get the first one though */
            $this->attributes['thumbnail'] = json_decode($getUploads)[0] ?? null;
        } catch (Exception $error) {
            throw new Error('File cannot upload, errors: ' + $error->getMessage());
        }
    }
}
