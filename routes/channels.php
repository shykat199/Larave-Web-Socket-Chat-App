<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private-chat',function ($user){
    return $user;
});

Broadcast::channel('send-message',function ($user){
    return $user;
});

Broadcast::channel('message-deleted',function ($user){
    return $user;
});

Broadcast::channel('get-group-chat',function ($user){
    return $user;
});

Broadcast::channel('group-typing-event',function ($user){
    return $user;
});

Broadcast::channel('delete-group-chat',function ($user){
    return $user;
});
