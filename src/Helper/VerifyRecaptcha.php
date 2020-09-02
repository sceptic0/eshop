<?php

namespace App\Helper;

class VerifyRecaptcha
{
    public function captchaverify($recaptcha) {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                "secret" => "6LesIcYZAAAAAKpsp9g_Ix27Dh31GYh5xL1R2nF-",
                "response" => $recaptcha
            )
        );
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);
        return $data->success;
    }
}