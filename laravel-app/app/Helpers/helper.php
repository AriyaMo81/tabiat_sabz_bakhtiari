
<?php

use Ghasedak\GhasedaksmsApi;
use Ghasedak\DataTransferObjects\Request\InputDTO;
use Ghasedak\DataTransferObjects\Request\OtpMessageDTO;
use Ghasedak\DataTransferObjects\Request\ReceptorDTO;

function imageUrl($image)
{
    return env('ADMIN_PANEL_URL') . env('PRODUCT_IMAGES_PATH') . $image;
}

function salePercent($price, $salePrice)
{
    return round((($price - $salePrice) / $price) * 100);
}

function sendOtpSms($cellphone, $code)
{
    $api = new GhasedaksmsApi(
        env('GHASEDAK_SMS_API_KEY')
    );

    return $api->sendOtp(
        new OtpMessageDTO(
            sendDate: new DateTimeImmutable('now'),

            receptors: [
                new ReceptorDTO(
                    mobile: $cellphone,
                    clientReferenceId: uniqid()
                )
            ],

            // نام قالب در پنل قاصدک
            templateName: 'Ghasedak',

            // پارامتر قالب: %Code%
            inputs: [
                new InputDTO(
                    param: 'Code',
                    value: $code
                )
            ]
        )
    );
}

