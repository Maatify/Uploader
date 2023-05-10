<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 2:25 AM
 * https://www.Maatify.dev
 */

namespace Maatify\WebPConverter;

use Maatify\Logger\Logger;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\WebPConvert;

class WebPConverter extends WebPConvert
{
    public function WebPConvert($source, $destination = '', $options = []): void
    {
        try {
            if(empty($destination)) {
                $destination = (preg_replace('/\\.[^.\\s]{3,4}$/', '', $source)) . '.webp';
            }
            self::convert($source, $destination, $options);
        } catch (ConversionFailedException $e) {
            Logger::RecordLog($e, 'WebPConvert');
        }
    }

    public function ServConverted($source, $destination): void
    {
        try {
            self::serveConverted($source, $destination, [
                'fail' => 'original',     // If failure, serve the original image (source). Other options include 'throw', '404' and 'report'
                //'show-report' => true,  // Generates a report instead of serving an image

                'serve-image' => [
                    'headers' => [
                        'cache-control' => true,
                        'vary-accept' => true,
                        // other headers can be toggled...
                    ],
                    'cache-control-header' => 'max-age=2',
                ],

                'convert' => [
                    // all convert option can be entered here (ie "quality")
                ],
            ]);
        } catch (ConversionFailedException $e) {
            Logger::RecordLog($e, 'WebPServConverted');
        }
    }
}