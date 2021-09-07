<?php
namespace Gxyshs\WechatOauth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
class BaseController extends Controller
{
    public function success($data)
    {
        $result = [
            'success' => true,
            'message' => 'ok',
            'code' => 0,
            'data' => $data,
            'errors' => null,
            'showType' => 0, // 0 silent; 1 message.warn; 2 message.error; 4 notification; 9 page
            'traceId' => '',
            'host' => Request::getHost()
        ];

       return response()->json($result);
    }

    public function error($data)
    {
        $result = [
            'success' => false,
            'message' => $data['message'] ?? 'error',
            'code' => $data['code'] ?? 1,
            'data' => $data['data'] ?? null,
            'errors' => $data['errors'] ?? null,
            'showType' => $data['showType'] ?? 2, // 0 silent; 1 message.warn; 2 message.error; 4 notification; 9 page
            'traceId' => '',
            'host' => Request::getHost()
        ];

       return response()->json($result);
    }
}
