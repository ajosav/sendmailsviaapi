<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\SendMailToRecipients;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Validator;

class MailController extends Controller
{
    public function sendMail(Request $request) {

        // Validates the user input
        // Validation is being done for required fields only
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'recipient' => 'required',
            'content' => 'required'
        ]);
        
        // check if validation passed and return error response if it doesn't
        if($validator->fails()) {
            return $this->respondError('Validation failed', $validator->errors(), 422);
        }

        //  set an empty array for ill emails
        $illEmails = [];
        $cc = [];

        // get recipients address and convert to array in not in array
        $recipients = is_array($request->to) ? $request->recipient : (array) $request->recipient;

        // get copy address and convert to array in not in array
        // check if all provided copy email well constructed if not remove
        if($request->cc) {
            $cc = is_array($request->cc) ? $request->cc : (array) $request->cc;
            foreach($cc as $index=>$toCC) {
                if(!filter_var($toCC, FILTER_VALIDATE_EMAIL)) {
                    array_splice($cc, $index, 1);
                    $illEmails['cc'][] = $toCC;
                }
            }
        }

        
        // initialize empty attachment
        $attachment = '';
        // check if request contains attachment and not empty
        if($request->attachment ) {
            $attached_req = is_array($request->attachment) ? !empty($request->attachment) : $request->attachment != "";
            if($attached_req) {
                $attachment = $request->file('attachment');
            }
        }
        
        foreach($recipients as $index=>$sendTo) {
            if(!filter_var($sendTo, FILTER_VALIDATE_EMAIL)) {
                
                // if(trim($sendTo) == '') {
                    array_splice($recipients, $index, 1);
                    $illEmails['recipient'][] = $sendTo;
                    // return $this->respondError('Invalid Email', "Blank index detected", 422);
                    // }
                    // return $this->respondError('Invalid Email', "{$sendTo} is not a valid email", 422);
            }
        }
        if(count($recipients) > 0 && count($cc) > 0) {
            // try {
               $this->mailRecipientCC($recipients, $cc, $request->subject, $request->content, $attachment);
                return $this->respondSent([
                    'message' => 'Mail sent successfully', 
                    'details' => [
                        'successfully_sent' => count($recipients),
                        'ill_emails' => $illEmails
                    ]
                ]);
        //    } catch(Exception $e) {
        //        return $this->respondError('Error Sending Email', $e, 400);
        //    }
        } elseif (count($recipients) > 0 ) {
            // try {
                $this->mailRecipient($request->subject, $recipients, $request->content, $attachment);
                return $this->respondSent([
                    'message' => 'Mail sent successfully', 
                    'details' => [
                        'successfully_sent' => count($recipients),
                        'ill_emails' => $illEmails
                    ]
                ]);
            // } catch(Exception $e) {
            //     return $this->respondError('Error Sending Email', $e, 400);
            // }
        } else {
            return $this->respondError('Failed sending mail to recipient', ["recipients" => $illEmails], 400);
        }


        




    }

    public function mailRecipientCC($to, $cc, $subject, $body, $attachment = '') {
        // foreach($to as $recipient) {
        // Mail::send('emails.send_mail', ['body' => $body], function($control) use($to, $cc, $subject) {
        //     $control->to($to)
        //     ->cc($cc);
        // });
        Mail::to($to)
            ->cc($cc)
            ->send(new SendMailToRecipients($subject, $body, $attachment));
        Log::log('critical', Mail::failures());
           
        // }
    }
    public function mailRecipient($subject, $to, $body, $attachment = '') {
       
        Mail::to($to)
        ->send(new SendMailToRecipients($subject, $body, $attachment));
        Log::log('critical', Mail::failures());
    }
}
