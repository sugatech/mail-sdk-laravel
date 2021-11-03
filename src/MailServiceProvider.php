<?php declare(strict_types=1);

namespace Mail\SDK;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Laravel\Lumen\Application as LumenApplication;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'mail');

        $this->app->singleton('mail.client', function ($app) {
            $options = $app['config']->get('mail');

            if (!isset($options['api_url'])) {
                throw new InvalidArgumentException('Not found api_url config');
            }

            if (!isset($options['oauth']['url'])) {
                throw new \InvalidArgumentException('Not found oauth.url config');
            }

            if (!isset($options['oauth']['client_id'])) {
                throw new \InvalidArgumentException('Not found oauth.client_id config');
            }

            if (!isset($options['oauth']['client_secret'])) {
                throw new \InvalidArgumentException('Not found oauth.client_secret config');
            }

            return new MailClient($options['api_url']);
        });
    }

    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('mail.php')], 'mail');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('mail');
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/mail.php';
    }

}
