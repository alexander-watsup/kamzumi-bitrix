<?

namespace Reklamafia\Messaging;

class Telegram
{

    const TELEGRAM_BOT_ID = '1753663316:AAH5WzAtTM8BfG_7_fh6E8C-hQMUfHBlWJk';
    const TELEGRAM_CHAT_ID = '-526133363';

    public static function sendMessage($text)
    {
        try {
            $bot = new \TelegramBot\Api\BotApi(self::TELEGRAM_BOT_ID);
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, $text);
        } catch (\TelegramBot\Api\Exception $e) {
            //            AddMessage2Log($e->getMessage());
            return false;
        }

        return true;
    }
}
