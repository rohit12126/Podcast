<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class Podcasts extends Model
{
	protected $table = "podcasts";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'marketing_url', 'feed_url', 'image', 'status', 'is_deleted'
    ];
}