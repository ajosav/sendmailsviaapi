<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailToRecipients extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $attach_ment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject = '', $body = "", $file)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->attach_ment = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject($this->subject)
                ->markdown('emails.send_mail');

        if($this->attach_ment  != '') {
            if(!is_array($this->attach_ment)) {
                if(file_exists($this->attach_ment)) {
                    $this->attach($this->attach_ment->getRealPath(), [
                        'as' => $this->attach_ment->getClientOriginalName(),
                        'mime' => $this->attach_ment->getMimeType(),
                    ]);
                }
            } else {
                
                foreach($this->attach_ment as $attached) {
                    if(file_exists($this->attach_ment)) {
                        $this->attach($attached->getRealPath(), [
                            'as' => $attached->getClientOriginalName(),
                            'mime' => $attached->getMimeType(),
                        ]);
                    }
                }
            }
        }
        return $this;
        
    }
}
