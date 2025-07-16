<?php

declare(strict_types=1);
/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: WebhookTest.php
 * Author: Hydrogenpay
 * Email: <support@hydrogenpay.com>
 * Website: https://hydrogenpay.com
 * Date: 07/07/2024
 * Time: 11:03 AM
 */

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class WebhookTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testHydrogenPayWebhookReceivesData(): void
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $payload = [
            'id'                 => 'txn_12345',
            'amount'             => 500.0,
            'chargedAmount'      => 500.0,
            'currency'           => 'NGN',
            'customerEmail'      => 'user@example.com',
            'narration'         => 'Test payment narration',
            'description'       => 'Test payment description',
            'status'            => 'successful',
            'transactionRef'    => 'ref_67890',
            'createdAt'         => '2024-07-07T10:00:00Z',
            'paidAt'            => '2024-07-07T10:01:00Z',
            'ip'                => '197.210.64.96',
            'paymentType'       => 'card',
            'fees'              => 2.5,
            'vat'               => 0.5,
            'recurringCardToken' => 'tok_abc123',
            'meta'              => '{"order_id":"ORD123"}',
        ];

        // Make POST request to your /webhook route
        $response = $this
            ->withHeaders($headers)
            ->withBody(json_encode($payload))
            ->post('/webhook');

        // Echo the raw response to see it
        echo "\n\n=== Webhook response body ===\n";
        echo $response->getBody() . "\n";
        echo "=== End of response ===\n";

        // Assert the webhook endpoint responds OK
        $response->assertOK();
    }
}
