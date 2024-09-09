<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function saveImage($image, $path = 'public')
    {
        if (!$image) {
            return null;
        }

        $filename = time().'.png';





        //save image

        Storage::disk($path)->put($filename, file_get_contents($image));

        //return path to image
        return URL::to('/').'/storage/'.$path.'/'.$filename;

    }

    public function sendNotification($user, $message)
    {
        try {
            $endPoint = 'https://api.mnotify.com/api/sms/quick';
        $apiKey = 'Yi0wvyk9morR1sRP1dRimvqLA';
        $url = $endPoint . '?key=' . $apiKey;
        $data = [
          'recipient' => [$user->tel1],
          'sender' => 'notAim',
          'message' => $message,
          'is_schedule' => 'false',
          'schedule_date' => ''
        ];

        $ch = curl_init();
        $headers = array();
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        $result = json_decode($result, TRUE);
        curl_close($ch);
        } catch (\Throwable $th) {
            // print error to console
            echo $th->getMessage();
        }
    }
}
