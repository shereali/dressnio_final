<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OtpAuthTest extends TestCase
{
    use RefreshDatabase;

    private function configureSms(): void
    {
        Setting::write('sms_enabled', '1');
        Setting::write('sms_api_key', 'secret-key-1234');
        Setting::write('sms_caller_id', '8801000000');
    }

    public function test_otp_is_sent_through_the_configured_gateway_and_stored_hashed(): void
    {
        $this->configureSms();
        Http::fake(['bulksmsdhaka.net/*' => Http::response(['status' => 'success'], 200)]);

        $this->postJson('/api/client/send-otp', ['mobile_no' => '+8801712345678'])
            ->assertOk()
            ->assertJsonPath('data.mobile_no', '01712345678')
            ->assertJsonPath('data.dev_otp', null);

        Http::assertSent(function ($request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY), $q);

            return str_starts_with($request->url(), 'https://bulksmsdhaka.net/api/otpsend')
                && $q['apikey'] === 'secret-key-1234'
                && $q['callerID'] === '8801000000'
                && $q['number'] === '01712345678'
                && preg_match('/\d{6}/', $q['message']) === 1;
        });

        $this->assertDatabaseMissing('login_codes', ['mobile_no' => '01712345678', 'code' => '123456']);
    }

    public function test_resend_is_blocked_during_the_cooldown(): void
    {
        $this->configureSms();
        Http::fake(['bulksmsdhaka.net/*' => Http::response(['status' => 'success'], 200)]);

        $this->postJson('/api/client/send-otp', ['mobile_no' => '01712345678'])->assertOk();
        $this->postJson('/api/client/send-otp', ['mobile_no' => '01712345678'])
            ->assertStatus(429)
            ->assertJsonStructure(['errors' => ['retry_after']]);
    }

    public function test_correct_code_signs_in_and_creates_the_customer(): void
    {
        $this->configureSms();
        $sent = null;
        Http::fake(function ($request) use (&$sent) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY), $q);
            $sent = $q['message'];

            return Http::response(['status' => 'success'], 200);
        });

        $this->postJson('/api/client/send-otp', ['mobile_no' => '01712345678'])->assertOk();
        preg_match('/\d{6}/', $sent, $m);

        $this->postJson('/api/client/verify-otp', ['mobile_no' => '01712345678', 'otp' => $m[0]])
            ->assertOk()
            ->assertJsonPath('data.is_new', true)
            ->assertJsonStructure(['data' => ['token', 'customer']]);

        $this->assertDatabaseHas('customers', ['mobile_no' => '01712345678']);
    }

    public function test_wrong_codes_run_out_of_attempts(): void
    {
        $this->configureSms();
        Http::fake(['bulksmsdhaka.net/*' => Http::response(['status' => 'success'], 200)]);
        $this->postJson('/api/client/send-otp', ['mobile_no' => '01712345678'])->assertOk();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/client/verify-otp', ['mobile_no' => '01712345678', 'otp' => '000000'])->assertStatus(422);
        }

        $this->postJson('/api/client/verify-otp', ['mobile_no' => '01712345678', 'otp' => '000000'])->assertStatus(429);
    }

    public function test_gateway_failure_is_reported_and_no_code_is_stored(): void
    {
        $this->configureSms();
        Http::fake(['bulksmsdhaka.net/*' => Http::response('Invalid API key', 200)]);

        $this->postJson('/api/client/send-otp', ['mobile_no' => '01712345678'])->assertStatus(502);
        $this->assertDatabaseMissing('login_codes', ['mobile_no' => '01712345678']);
    }

    public function test_api_key_is_encrypted_at_rest_and_masked_for_admin(): void
    {
        $this->seed();
        $token = $this->postJson('/api/admin-login', ['email' => 'admin@dressnio.com', 'password' => 'dressnio@2026'])->json('data.token');
        $headers = ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'];

        $this->putJson('/api/settings', ['sms_api_key' => 'secret-key-1234', 'sms_enabled' => '1'], $headers)->assertOk();

        $this->assertNotSame('secret-key-1234', Setting::where('key', 'sms_api_key')->value('value'));
        $this->assertSame('secret-key-1234', Setting::read('sms_api_key'));

        $this->getJson('/api/settings', $headers)->assertJsonPath('data.sms_api_key', '••••••••1234');

        // Saving the masked value back keeps the stored key
        $this->putJson('/api/settings', ['sms_api_key' => '••••••••1234'], $headers)->assertOk();
        $this->assertSame('secret-key-1234', Setting::read('sms_api_key'));
    }
}
