<?php

namespace FiftySq\Commerce\Payments\Console;

use Eco\Env;
use FiftySq\Commerce\Payments\GatewayManager;
use Illuminate\Console\Command;

class ConfigureStripe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commerce:stripe {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the Stripe payment gateway';

    /**
     * Execute the console command.
     *
     * @param  GatewayManager  $manager
     * @return int
     */
    public function handle(GatewayManager $manager): int
    {
        $config = config('commerce.gateways.drivers.stripe');

        if (! array_key_exists('webhook_token', $config) || ! $webhook_token = $config['webhook_token']) {
            $this->error('You must set a Stripe access token in your environment before configuring.');

            return 0;
        }

        if (! empty($webhook_token) && ! $this->option('force')) {
            $this->error('Stripe has already been configured. Run this again with --force to overwrite.');

            return 0;
        }

        $endpoint = $manager->driver('stripe')->createWebhookEndpoint();

        if (app()->environment('production')) {
            $this->comment('Webhook endpoint generated.');
            $this->info("Set the STRIPE_WEBHOOK_TOKEN env variable to \"{$endpoint['secret']}\".");
        } else {
            Env::set('.env', 'STRIPE_WEBHOOK_TOKEN', $endpoint['secret']);
            $this->info('The webhook token has been successfully set in your environment file.');
        }

        $this->comment('Installation completed.');

        return 0;
    }
}
