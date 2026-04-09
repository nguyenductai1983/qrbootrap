<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('printstationapp.{stationKey}', function ($user, $stationKey) {
    // Chỉ cần kiểm tra user đã login qua Sanctum thành công
    // Hoặc kiểm tra logic: $user->tokenCan('print') 
    return !is_null($user);
});

// Kênh trạm cân — public channel cho Web lắng nghe real-time
Broadcast::channel('scale.{stationCode}', function () {
    return true; // Public channel, C# App push data lên qua API
});
