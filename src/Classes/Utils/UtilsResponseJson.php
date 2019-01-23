<?php

namespace Omadonex\LaravelSupport\Classes\Utils;

class UtilsResponseJson
{
    const CODE_OK = 200;
    const CODE_VALIDATION_ERROR = 422;

    public static function okResponse($data, $wrap = false)
    {
        $result = $wrap ? ['data' => $data] : $data;
        return response()->json([
            'status' => true,
            'result' => $result,
        ], self::CODE_OK);
    }

    public static function errorResponse($data, $wrap = false)
    {
        $result = $wrap ? ['data' => $data] : $data;
        return response()->json([
            'status' => false,
            'result' => $result,
        ], self::CODE_OK);
    }

    public static function validationResponse($data, $warning = false)
    {
        $wrapKey = $warning ? 'warnings' : 'errors';
        $wrappedData = [
            $wrapKey => $data,
        ];
        return response()->json($wrappedData, self::CODE_VALIDATION_ERROR);
    }
}