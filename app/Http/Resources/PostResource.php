<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /**
         * @var Model
         */
        $self = $this;
        return array_merge(parent::toArray($request), [
            'user' => $this->whenLoaded('user'),
            'is_like' => $this->is_like,
            'created_at' => $self->created_at->diffForHumans(),
            'post_interactions' => $this->whenLoaded('postInteractions')
        ]);
    }
}
