<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id', 'order_id', 'user_id', 'sender_type', 'message'];

    public static function getTodayMessagesOnlyForConsultationByGroup()
    {
        return self::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('messages')
                ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                ->whereNull('order_id')
                ->where('sender_type', 'client')
                ->groupBy('chat_id');
        })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getTodayMessagesOnlyForConsultation($chat_id)
    {
        return self::where('chat_id', $chat_id)
            ->whereDate('created_at', Carbon::today())
            ->get();
    }

    public static function getChatId($message_id)
    {
        return self::where('id', $message_id)
            ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
            ->value('chat_id');
    }
}
