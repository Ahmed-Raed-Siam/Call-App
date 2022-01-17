<?php

namespace App\Notifications;

use App\Models\User;
use App\Channels\Log;
use App\Channels\Msegat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRecoveryCodeNotificatione extends Notification
{
    use Queueable;

    protected $user;
    protected $recovery_code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        $via = [Msegat::class, 'database', 'mail'];

        if (!$notifiable instanceof AnonymousNotifiable):
            if ($notifiable->notify_mail):
                $via[] = 'mail';
            endif;

            if ($notifiable->notify_sms):
                $via[] = Msegat::class;
//                $via[] = 'nexmo';
            endif;
        endif;

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        if (count($this->user->recoveryCodes()) > 0):
            $recovery_code = $this->user->recoveryCodes()[0];
            $this->recovery_code = $recovery_code;
            $body = sprintf('%s Recovery Codes: %s',
                $this->user->name,
                $this->recovery_code,
            );
        endif;
        $message = new MailMessage;
        $message->subject('New Recovery Code')
            ->from('ar99@callaapp.com', 'Calla Notifications')
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('AR99--The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line($body)
            ->line('Thank you for using our application!');

        return $message;
    }

    /**
     * @param $notifiable
     * @return int
     */
    public function toLog($notifiable)
    {
        $body = sprintf('%s Recovery Codes: \n %s',
            $this->user->name,
            $this->user->recoveryCodes()[0],
        );

        return $body;
    }

    /**
     * @param $notifiable
     * @return int
     */
    public function toMsegat($notifiable): int
    {
        $body = sprintf('%s Recovery Codes: \n %s',
            $this->user->name,
            $this->user->recoveryCodes()[0],
        );

        return $body;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
