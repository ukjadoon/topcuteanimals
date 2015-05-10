<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {

	protected $fillable = ['headline', 'url', 'slug', 'cloudinary_url'];

}
