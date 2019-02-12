<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Board_Request extends Model
{
    use Notifiable;

    protected $table = 'board_request';

    protected $fillable = ['name'];

    public function routeNotificationForSlack($notification)
    {
        return env('SLACK_WEBHOOK_ADD_BOARD_REQUEST');
    }
}
