<?php

namespace App\Services;

class ResponseService
{
    public static function meta($collection)
    {
        return
            [
                'current_page' => $collection->currentPage(),
                'last_page' => $collection->lastPage(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ];
    }


    public static function response(array $params)
    {
        $status = $params['status'] ?? 200;
        $replace = $params['replace'] ?? [];

        $response = [];

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'status':
                case 'replace':
                    break;

                case 'message':
                    $response['message'] = trans($value, $replace);
                    $response['key'] = $value;
                    break;

                case 'data':
                    $response['data'] = $value;
                    break;

                case 'meta':
                    if ($value) {
                        $response['meta'] = self::meta($params['data'] ?? null);
                    }
                    break;

                case 'info':
                    $response['info'] = $value;
                    break;

                default:
                    $response[$key] = $value;
                    break;
            }
        }

        $response = ['success' => $status >= 200 && $status < 300] + $response;

        return response()->json($response, $status);
    }
}
