<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Podcastcomments extends Model
{
    protected $table = "podcastcomments";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'podcast_id', 'author_name', 'author_email', 'comment', 'is_deleted'
    ];
}
