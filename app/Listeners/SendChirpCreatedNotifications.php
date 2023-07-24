<?php

namespace App\Listeners;

use App\Events\ChirpCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\NewChirp;

// ShouldQueue is implemented so instead of synchronously, events are listened in background
class SendChirpCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Sends notifications to every user in the platform, except the author of the Chirp.
     * Used a database cursor to avoid loading every user into memory at once.
     */
    public function handle(ChirpCreated $event): void
    {
        foreach (User::whereNot('id', $event->chirp->user_id)->cursor() as $user) {
            $user->notify(new NewChirp($event->chirp));
        }
    }
}
