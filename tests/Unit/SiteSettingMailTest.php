<?php

namespace Tests\Unit;

use App\Models\SiteSetting;
use Tests\TestCase;

class SiteSettingMailTest extends TestCase
{
    public function test_apply_mail_config_sets_smtp_sender_from_site_settings(): void
    {
        $settings = new SiteSetting([
            'mail_mailer' => 'smtp',
            'mail_from_name' => 'Refugio SMTP',
            'mail_from_address' => 'noreply@refugiogastronomico.pe',
            'mail_host' => 'smtp.example.com',
            'mail_port' => 587,
            'mail_username' => 'user@example.com',
            'mail_password' => 'secret',
            'mail_encryption' => 'tls',
        ]);

        $settings->applyMailConfig();

        $this->assertSame('Refugio SMTP', config('mail.from.name'));
        $this->assertSame('noreply@refugiogastronomico.pe', config('mail.from.address'));
        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame(587, (int) config('mail.mailers.smtp.port'));
        $this->assertSame('user@example.com', config('mail.mailers.smtp.username'));
        $this->assertSame('secret', config('mail.mailers.smtp.password'));
    }

    public function test_local_mail_is_simulated_even_with_smtp_host(): void
    {
        $settings = new SiteSetting([
            'mail_from_name' => 'Refugio Gastronómico',
            'mail_from_address' => 'marketing@refugiogastronomico.pe',
            'mail_host' => 'mail.refugiogastronomico.pe',
            'mail_port' => 465,
            'mail_encryption' => 'ssl',
        ]);

        $settings->applyMailConfig();

        $this->assertSame('log', $settings->resolvedMailer());
        $this->assertTrue($settings->usesSimulatedMail());
        $this->assertSame('log', config('mail.default'));
        $this->assertSame('marketing@refugiogastronomico.pe', config('mail.from.address'));
    }

    public function test_site_mail_test_can_be_sent_on_array_mailer(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        \Illuminate\Support\Facades\Mail::to('qa@example.com')->send(
            new \App\Mail\SiteMailTest('qa@example.com')
        );

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SiteMailTest::class, function ($mail) {
            return $mail->hasTo('qa@example.com')
                && $mail->hasSubject('Prueba de correo — Refugio Gastronómico');
        });
    }

    public function test_complaint_recipients_use_stored_emails(): void
    {
        $settings = new SiteSetting([
            'complaint_book_recipients' => ['one@refugio.pe', ' two@refugio.pe '],
        ]);

        $this->assertSame(['one@refugio.pe', 'two@refugio.pe'], $settings->complaintBookRecipients());
    }
}
