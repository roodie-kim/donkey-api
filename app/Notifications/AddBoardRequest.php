<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;

class AddBoardRequest extends Notification implements ShouldQueue
{
    use Queueable;
    private $board;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($board, $user)
    {
        $this->board = $board;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $board = $this->board;
        $user = $this->user;

        return (new SlackMessage)
            ->success()
            ->content($user->name . ' 님이 "' . $board->name . '" 보드 추가를 요청했습니다. :boom:')
            ->attachment(function ($attachment) use ($board, $user) {
                $attachment->title(env('APP_ENV'))
                    ->fields([
                        '이메일' => $user->email,
                        '유저 이름' => $user->name,
                        '게시판 이름' => $board->name
                    ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
