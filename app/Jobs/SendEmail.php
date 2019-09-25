<?php

namespace App\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $from;
    protected $to;
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id, $from, $to, $amount)
    {
        $this->user_id = $user_id;
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send('emails.manager', array(), function($message)
        {
            $mail_from = env("MAIL_FROM_ADDRESS", "");
            $mail_from_name = env("MAIL_FROM_NAME", "");

            $mail_to = env("MAIL_MANAGER_ADDRESS", "");
            $mail_to_name = env("MAIL_MANAGER_NAME", "");

            $message->to($mail_to, $mail_to_name)->subject('Уведомление о заказе.');
            $message->from($mail_from, $mail_from_name);
        });
    }
}
