<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon as SupportCarbon;

class CommentResource extends JsonResource
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
            'created_at' => $self->created_at->diffForHumans(),
            'updated_at' => $self->updated_at->diffForHumans(),
            'user'      => $this->whenLoaded('user', new UserResource($this->user)),
            'children' => $this::collection($self->children)
        ]);
    }
}
