<?php

namespace Gxyshs\WechatOauth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use WechatOauth;

class OauthController extends BaseController
{
    public function access(Request $request)
    {
        if (!$targetUrl = $request->get('page', null)) {
            throw new \Exception('首页参数必需');
        }

        if ($request->session()->has('wechatInfo')) {
            $query = '';
            if ($wechatInfo = $request->session()->get('wechatInfo', null)) {
                if (isset($wechatInfo['openid'])) {
                    $query = '?id=' . $wechatInfo['openid'];
                }
            }
            return redirect(urldecode($targetUrl) . $query);
        } else {
            $request->session()->put('targetUrl', urldecode($targetUrl));
            return WechatOauth::access();
        }
    }

    public function callback(Request $request)
    {
        if (!$code = $request->get('code', null)) {
            throw new \Exception('code参数必需');
        }

        $user = WechatOauth::getUserInfo($code);

        if (!$targetUrl = $request->session()->get('targetUrl', null)) {
            throw new \Exception('首页参数异常');
        }

//        return redirect($targetUrl . '?id=' . $user['openid']);
        $request->session()->put('wechatInfo', $user);
        return redirect($targetUrl);
    }

}
