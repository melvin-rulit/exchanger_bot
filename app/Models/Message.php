<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id', 'order_id', 'user_id', 'sender_type', 'message'];

    public static function getTodayMessagesOnlyForConsultation()
    {
//        return self::whereDate('created_at', Carbon::now()->format('Y-m-d'))
//            ->whereNull('order_id')
//            ->where('sender_type', 'client')
//            ->get();

        return self::select('chat_id', DB::raw('MAX(id) as id'), DB::raw('MAX(message) as message'), DB::raw('MAX(created_at) as created_at'))
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->whereNull('order_id')
            ->where('sender_type', 'client')
            ->groupBy('chat_id')
            ->orderBy('created_at', 'desc') // Сортируем по времени последнего сообщения
            ->get();
    }
}
