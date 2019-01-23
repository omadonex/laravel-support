<?php

namespace Omadonex\LaravelSupport\Classes\Utils\WebMerchants;

class UtilsWalletOne
{
    public static function getWMISignature($fields) {
        // Формирование сообщения, путем объединения значений полей, отсортированных по именам ключей в порядке возрастания.
        uksort($fields, "strcasecmp");
        $fieldValues = "";
        foreach ($fields as $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    //Конвертация из текущей кодировки (UTF-8) необходима только если кодировка магазина отлична от Windows-1251
                    $v = iconv("utf-8", "windows-1251", $v);
                    $fieldValues .= $v;
                }
            } else {
                //Конвертация из текущей кодировки (UTF-8) необходима только если кодировка магазина отлична от Windows-1251
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= $value;
            }
        }

        // Формирование значения параметра WMI_SIGNATURE, путем вычисления отпечатка, сформированного выше сообщения, по алгоритму MD5 и представление его в Base64
        $signature = base64_encode(pack("H*", md5($fieldValues . env('WMI_SECRET'))));

        return $signature;
    }

    public static function getDataArray($price, $no, $description, $optFields = [], $keyValue = false)
    {
        $wmi = [
            'WMI_MERCHANT_ID' => env('WMI_MERCHANT_ID'),
            'WMI_PAYMENT_AMOUNT' => $price,
            'WMI_CURRENCY_ID' => env('WMI_CURRENCY_ID'),
            'WMI_PAYMENT_NO' => $no,
            'WMI_DESCRIPTION' => 'BASE64:' . base64_encode($description),
            'WMI_SUCCESS_URL' => env('WMI_SUCCESS_URL'),
            'WMI_FAIL_URL' => env('WMI_FAIL_URL'),
        ];

        foreach ($optFields as $key => $value) {
            $wmi[$key] = $value;
        }

        $wmi['WMI_SIGNATURE'] = self::getWMISignature($wmi);

        if (!$keyValue) {
            return $wmi;
        }

        $wmiArr = [];
        foreach ($wmi as $key => $value) {
            $wmiArr[] = ['name' => $key, 'value' => $value];
        }

        return $wmiArr;
    }
}