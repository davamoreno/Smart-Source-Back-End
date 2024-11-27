<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('message', function(){
    return true;
});
