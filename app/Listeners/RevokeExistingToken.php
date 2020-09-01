<?php

namespace App\Listeners;


use App\User;
use function foo\func;

class RevokeExistingToken
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = User::find($event->userId);

        if (!$user->hasRole('customer')){
            $user->tokens()->where('id','!=' ,$event->tokenId)->get()->map(function ($token){
                $token->revoke();
            });
        }


    }
}
