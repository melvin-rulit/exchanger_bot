<?php

namespace App\Services\Web\Consultation;

use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Services\Web\BaseWebService;
use App\Telegram\Traits\HandlesFile;
use App\Exceptions\TelegramApiException;
use Illuminate\Database\Eloquent\Collection;
use App\Services\ClientService\ClientsService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Images\MediaLibraryException;
use App\Services\RedisService\RedisSessionService;
use App\Exceptions\Consultation\MessageNotFoundException;
use App\Services\TelegramBotService\TelegramMessageService;

class ConsultationWebService extends BaseWebService
{
    use HandlesFile;

    private TelegramMessageService $telegramService;

    public function __construct(RedisSessionService $redis, TelegramMessageService $telegramMessageService, ClientsService $clientsService)
    {
        parent::__construct($redis, $telegramMessageService, $clientsService);
        $this->telegramService = $telegramMessageService;
    }

    public function getTodayConsultationMessages(): LengthAwarePaginator
    {
        return Message::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('messages')
                ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                ->whereNull('order_id')
                ->where('sender_type', 'client')
                ->groupBy('chat_id');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(16);
    }

    /**
     * @throws MessageNotFoundException
     */
    public function getTodayMessagesChat($messageId): Collection
    {
        $chat_id = Message::getChatId($messageId);

        if (!$chat_id) {
            throw new MessageNotFoundException();
        }

        return Message::where('chat_id', $chat_id)
            ->whereNull('order_id')
            ->whereDate('created_at', Carbon::today())
            ->with('media')
            ->get();
   }

    /**
     * @throws TelegramApiException
     * @throws MessageNotFoundException
     */
    public function storeMessage($request)
    {
        $chat_id = Message::getChatId($request->getIdFromRoute('messageId'));

        if (!$chat_id) {
            throw new MessageNotFoundException();
        }

        $created_message = Message::create([
            'chat_id' => $chat_id,
            'order_id' => null,
            'user_id' => auth()->user()->id,
            'sender_type' => 'user',
            'message' => $request->getMessage(),
        ]);

        $this->telegramService->sendMessage($chat_id, $request->getMessage());

        return $created_message;
   }

    /**
     * @throws MediaLibraryException
     * @throws OrderNotFoundException
     */
    public function storeMessageWithPhoto($request)
    {
        $created_message = Message::create([
            'chat_id' => $request->getChatId(),
            'order_id' => null,
            'user_id' => auth()->user()->id,
            'sender_type' => 'user',
            'message' => null,
        ]);

        $imageContent = file_get_contents($request->file('photo')->getRealPath());

        if ($created_message) {
            $this->saveImageToModelFromResponse($imageContent, 'screenshot.jpg', $created_message, 'chat_screenshot');
        }

        $created_message->refresh();

        $media = $created_message->getFirstMedia('chat_screenshot');

        $filePath = $media->getPath();

        try {
            $this->telegramMessageService->sendPhoto($request->getChatId(), new \CURLFile($filePath), $request->getCaption());
        } catch (TelegramApiException|ConnectionException $e) {

        }

        return $created_message;
    }

    /**
     * @throws MessageNotFoundException
     */
    public function setMessagesRead($request): void
    {
        $message = Message::find($request->getIdFromRoute('messageId'));

        if (!$message) {
            throw new MessageNotFoundException();
        }

        $message->is_message = true;
        $message->save();
    }
}
