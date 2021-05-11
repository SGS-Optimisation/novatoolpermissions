<?php


namespace App\Services\MySgs\Api\Resolvers;


class PrinterResolver
{

    public static function handle($data)
    {
        //\Log::debug('printer resolver received data:'.print_r($data, true));

        $accumulator = [];

        foreach ($data as $contact) {
            if ($contact->contactType == 30) {
                $accumulator[] = $contact->customerName;
            }
        }
        return $accumulator;
    }
}
