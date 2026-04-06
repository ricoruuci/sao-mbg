<?php

namespace App\Traits;

trait HttpResponse
{
    protected function responsePagination($data, $httpCode = 200)
    {
        return response()->json($data, $httpCode);
        // if (is_array($data) && array_key_exists('data', $data)) {
        //     $data['data'] = $this->formatDecimalPayload($data['data']);

        //     return response()->json($data, $httpCode);
        // }

        // return response()->json($this->formatDecimalPayload($data), $httpCode);
    }

    protected function responseData($data, $httpCode = 200)
    {
        return response()->json([
            'data' => $data
        ], $httpCode);

        // if ($data)
        // {
        //     return response()->json([
        //         'data' => $this->formatDecimalPayload($data)
        //     ], $httpCode);
        // }
        // else
        // {
        //     return response()->json([
        //         'data' => ''
        //     ]);
        // }
    }

    protected function responseError($message, $httpCode)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $httpCode);
    }

    protected function responseSuccess($message, $httpCode, $data = null)
    {
        if($data)
        {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $this->formatDecimalPayload($data)
            ], $httpCode);
        }
        else
        {
            return response()->json([
                'success' => true,
                'message' => $message
            ], $httpCode);
        }
    }

    private function formatDecimalPayload($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->formatDecimalPayload($item);
            }

            return $value;
        }

        if (is_object($value)) {
            foreach ($value as $key => $item) {
                $value->{$key} = $this->formatDecimalPayload($item);
            }

            return $value;
        }

        if (is_float($value)) {
            return number_format($value, 6, '.', '');
        }

        if (is_string($value) && preg_match('/^-?\d+\.\d+$/', $value)) {
            return number_format((float) $value, 6, '.', '');
        }

        return $value;
    }

}