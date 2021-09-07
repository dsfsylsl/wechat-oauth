<?php

namespace Gxyshs\WechatOauth;
use App\Models\WechatUserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Log;

class WechatOauth
{
    // Build wonderful things

    private $config = [
        'app_id'  => '',         // AppID
        'secret'  => '',     // AppSecret
        'token'   => '',          // Token
//        'aes_key' => '',

        /**
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         * 使用自定义类名时，构造函数将会接收一个 `EasyWeChat\Kernel\Http\Response` 实例
         */

        'response_type' => 'array',

        'http' => [
            'max_retries' => 1,
            'retry_delay' => 500,
            'timeout' => 5.0,
//            'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
        ],

        // ...
        'oauth' => [
            'scopes'   => ['snsapi_userinfo'],
            'callback' => '',
        ],
        // ..
    ];

    public function access()
    {
        $commonConfig = DB::select('select * from wechat_settings where code = :code limit 1', ['code' => 'config']);

        if (count($commonConfig) === 0) {
            throw new \Exception('微信基础配置必需');
        }

        $commonContent = json_decode($commonConfig[0]->content);
        $commonSetting = json_decode($commonContent);

        $this->config['app_id'] = $commonSetting->appId;
        $this->config['secret'] = $commonSetting->appSecret;
        $this->config['token'] = $commonSetting->token;
//        $this->config['oauth']['callback'] = 'http://' . $_SERVER['HTTP_HOST'] . '/wechat/oauth/callback';
//        $this->config['oauth']['callback'] = route('wechatOauthCallback');
//        $this->config['oauth']['callback'] = route('wechatOauthCallback');
        $this->config['oauth']['callback'] = Config::get('app.url') . '/wechat/oauth/callback';
//        Log::info($this->config['oauth']['callback']);

        $app = Factory::officialAccount($this->config);
        $oauth = $app->oauth;

        $redirectUrl = $oauth->redirect();
        header("Location: {$redirectUrl}");
//        exit;
    }

    public function getUserInfo($code)
    {
        $commonConfig = DB::select('select * from wechat_settings where code = :code limit 1', ['code' => 'config']);

        if (count($commonConfig) === 0) {
            throw new \Exception('微信基础配置必需');
        }

        $commonContent = json_decode($commonConfig[0]->content);
        $commonSetting = json_decode($commonContent);
        $this->config['app_id'] = $commonSetting->appId;
        $this->config['secret'] = $commonSetting->appSecret;
        $this->config['token'] = $commonSetting->token;

        $app = Factory::officialAccount($this->config);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->userFromCode($code);

        // 获取或创建微信用户
        $wechatInfo = WechatUserInfo::where('openid', $user->id)->first();
        if (empty($wechatInfo)) {
            $userInsert = [
                'avatar' =>  $user->avatar,
                'openid' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'register_time' => new \DateTime,
                'last_login_time' => new \DateTime,
                'created_at' => new \DateTime,
                'updated_at' => new \DateTime
            ];
            $wechatInfo = WechatUserInfo::create($userInsert);
        }

        return $wechatInfo;
    }

    public function getOpenId(Request $request)
    {
        $openId = null;
        if ($request->session()->has('wechatInfo')) {
            if ($wechatInfo = $request->session()->get('wechatInfo', null)) {
                if (isset($wechatInfo['openid'])) {
                    $openId = $wechatInfo['openid'];
                }
            }
        }
        return $openId;
    }

    public function getOpenIdByUserId(Request $id)
    {
        try {
            $wechatInfo = WechatUserInfo::findOrFail($id);
        } catch (\Throwable $e) {
            throw new \Exception('用户微信未授权');
        }

        return $wechatInfo->openid;
    }

    public function getWechatInfo(Request $request)
    {
        $wechatInfo = null;
        if ($request->session()->has('wechatInfo')) {
            $wechatInfo = $request->session()->get('wechatInfo', null);
        }
        return $wechatInfo;
    }
}