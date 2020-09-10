<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProcessBase64Trait;
use Exception;

class SendMailToRecipients extends Mailable
{
    use Queueable, SerializesModels, ProcessBase64Trait;

    public $subject;
    public $body;
    public $attach_ment;
    public $category;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject = '', $body = "", $file, $category)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->attach_ment = $file;
        $this->category = $category;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $validateCat = is_array($this->category) ? !empty($this->category) : $this->category != "";

        if($validateCat) {
            $headerData = [
                'category' => $this->category,
                // 'unique_args' => [
                //     'variable_1' => 'abc'
                // ]
            ];
    
            $header = $this->asString($headerData);
    
            $this->withSwiftMessage(function ($message) use ($header) {
                $message->getHeaders()
                        ->addTextHeader('X-SMTPAPI', $header);
            });
        }


        $this->subject($this->subject)
                ->markdown('emails.send_mail');

        
        if($this->attach_ment  != '') {
            if(!is_array($this->attach_ment)) {
                
                $this->attachFileToMail($this->attach_ment);
                
            } else {
                    
                foreach($this->attach_ment as $attached) {

                    $this->attachFileToMail($attached);
                    // $this->attachData($this->extractfile($attached['path']), $attached['filename'], [
                    //     'mime' => $attached['contentType']
                    // ]);
                }
            }

        }
        return $this;
        
    }

    private function asJSON($data)
    {
        $json = json_encode($data);
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);

        return $json;
    }

    private function asString($data)
    {
        $json = $this->asJSON($data);

        return wordwrap($json, 76, "\n   ");
    }

    private function attachFileToMail($string_or_file) {
        
        if(@is_file($string_or_file)) {
            return $this->attach($string_or_file->getRealPath(), [
                        'as' => $string_or_file->getClientOriginalName(),
                        'mime' => $string_or_file->getMimeType(),
                    ]);
        } else {
            if(is_array($string_or_file) && array_key_exists('path', $string_or_file) && array_key_exists('filename', $string_or_file) && array_key_exists('contentType', $string_or_file)) {
               if (base64_encode(base64_decode($string_or_file['path'], true)) === $string_or_file['path']){
                    return $this->attachData($this->extractfile($string_or_file['path']), $string_or_file['filename'], [
                        'as' => $string_or_file['filename'],
                        'mime' => $string_or_file['contentType']
                    ]);
                } else {
                    throw new Exception('Path is expects a base64 encoded image format');
                } 
            } else {
                throw new Exception('Please include a file path or use a base64encoded data');
                
            }
           
        }
    }
}
