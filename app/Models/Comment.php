<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'user_id',
        'post_id',
        'parent_id'
    ];

    public function user() {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Guest User'
        ]);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
