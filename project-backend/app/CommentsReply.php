<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentsReply extends Model
{
    protected $fillable = [
        "comment_reply_author","comment_reply","comment_id"
    ];
}
