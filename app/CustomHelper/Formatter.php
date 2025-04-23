<?php

namespace App\CustomHelper;

use Symfony\Component\HttpFoundation\JsonResponse;
use function PHPUnit\Framework\isEmpty;

class Formatter
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function removeVowel(string $string): string
    {
        $vowels = ["A","I","U","E","O","a","i","u","e","o"];
        $result = str_replace($vowels, "", $string);

        if ($result == "") {
            return $string;
        }

        return $result;
    }

    public static function makeDash(string $string): string
    {
        return str_replace(" ", "-", trim($string));
    }

    public static function apiResponse(int $code = 200, string $msg = "", mixed $data = null, mixed $error = null): JsonResponse
    {
        return response()->json([
            "message" => $msg,
            "content" => $data,
            "error" => $error
        ], $code);
    }
}
