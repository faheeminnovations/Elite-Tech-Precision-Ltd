<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : The email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');

        $this->info("Sending test email to: {$email}");
        $this->info("Using mailer: " . config('mail.mailer'));
        $this->info("Host: " . config('mail.mailers.smtp.host'));
        $this->info("Port: " . config('mail.mailers.smtp.port'));

        try {
            Mail::raw('This is a test email from EliteFlow. Your email configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                    ->subject('EliteFlow Email Test - ' . now()->format('Y-m-d H:i:s'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info('✓ Test email sent successfully!');
            $this->info('Please check your inbox (and spam folder) for the test email.');

            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Failed to send test email:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->info('Please check your .env file mail configuration:');
            $this->info('MAIL_MAILER=smtp');
            $this->info('MAIL_HOST=195.35.39.91');
            $this->info('MAIL_PORT=65002');
            $this->info('MAIL_USERNAME=u249713597');
            $this->info('MAIL_PASSWORD=Rac@909090');
            $this->info('MAIL_ENCRYPTION=null');
            $this->info('MAIL_FROM_ADDRESS=eliteflow@playwithlegit.com');
            $this->info('MAIL_FROM_NAME="EliteFlow - Elite Tech Precision Ltd"');

            return 1;
        }
    }
}
