<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Traits\ProcessBase64Trait;

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
            dd($this->category);
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
                $this->attachData($this->extractfile($this->attach_ment['path']), [
                    'as' => $this->attach_ment['filename'],
                    'mime' => $this->attach_ment['contentType']
                    ]);
                } else {
                    
                foreach($this->attach_ment as $attached) {
                   
                    $this->attachData($this->extractfile($attached['path']), $attached['filename'], [
                        'mime' => $attached['contentType']
                    ]);
                }
            }

            // if(!is_array($this->attach_ment)) {
            //     if(file_exists($this->attach_ment)) {
            //         $this->attach($this->attach_ment->getRealPath(), [
            //             'as' => $this->attach_ment->getClientOriginalName(),
            //             'mime' => $this->attach_ment->getMimeType(),
            //         ]);
            //     }
            // } else {
                
            //     foreach($this->attach_ment as $attached) {
            //         if(file_exists($attached)) {
            //             $this->attach($attached->getRealPath(), [
            //                 'as' => $attached->getClientOriginalName(),
            //                 'mime' => $attached->getMimeType(),
            //             ]);
            //         }
            //     }
            // }
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

    private function getFileFromString($string_file) {
        
        $fileLoaded = $this->extractfile($string_file);

            // Gather information about the image using the GD library
        $size = getImageSizeFromString($fileLoaded);

        // Check the MIME type to be sure that the binary data is an image
        if (empty($size['mime']) || strpos($size['mime'], 'image/') !== 0) {
            die('Base64 value is not a valid image');
        }


        // Mime types are represented as image/gif, image/png, image/jpeg, and so on
        // Therefore, to extract the image extension, we subtract everything after the “image/” prefix
        $ext =  substr($size['mime'], strpos($size['mime'], "/") + 1); 

        // Make sure that you save only the desired file extensions
        return $ext;
    }
}
