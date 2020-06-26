<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array 验证器
     */
    protected $validators = [
        'phone' => \App\Validators\PhoneValidator::class,
        'tel_phone' => \App\Validators\TelPhoneValidator::class,
        'phone_two' => \App\Validators\PhoneTwoValidator::class,
        'username' => \App\Validators\UsernameValidator::class,
        'nickname' => \App\Validators\NicknameValidator::class,
        'phone_verify_code' => \App\Validators\PhoneVerifyCodeValidator::class,
        'mail_verify_code' => \App\Validators\MailVerifyCodeValidator::class,
        'hash' => \App\Validators\HashValidator::class,
        'longitude' => \App\Validators\LongitudeValidator::class,//纬度
        'latitude' => \App\Validators\LatitudeValidator::class,//经度
        'ticket' => \App\Validators\TicketValidator::class,
        'phone_verify' => \App\Validators\PhoneVerifyValidator::class,
        'mail_verify' => \App\Validators\MailVerifyValidator::class,
        'mac_address' => \App\Validators\MacAddressValidator::class,//Mac 地址验证
        'captcha' => \App\Validators\CaptchaValidator::class,//验证码验证
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //忽略 Passport 默认迁移
        \Laravel\Passport\Passport::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }
        //关闭 API 响应的 data 包裹
        JsonResource::withoutWrapping();
        Carbon::setLocale('zh');

        $this->registerObserve();
        $this->registerValidators();
    }

    /**
     * Register observes.
     */
    protected function registerObserve()
    {
        \App\Models\User::observe(\App\Observers\UserObserver::class);//用户
        \App\Models\UserLoginHistory::observe(\App\Observers\UserLoginHistoryObserver::class);//登录
        \App\Models\Message::observe(\App\Observers\MessageObserver::class);//站内信
    }

    /**
     * Register validators.
     */
    protected function registerValidators()
    {
        foreach ($this->validators as $rule => $validator) {
            Validator::extend($rule, "{$validator}@validate");
        }
    }
}
