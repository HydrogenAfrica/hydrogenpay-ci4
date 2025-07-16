<?php

declare(strict_types=1);

/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: CollectPaymentTest.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/10/25
 * Time: 7:00 PM
 */

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\TestResponse;
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;
use Exception;

/**
 * @internal
 */
final class CollectPaymentTest extends CIUnitTestCase
{
    /**
     * Test standard payment initiation.
     *
     * @throws Exception
     */
    public function testStandardPayment(): void
    {

        $data = [
            'amount'          => 50,
            'customer_name'   => 'Dev Test',
            'customer_email'  => 'devtest@randomuser.com',
            'currency'        => 'NGN',
            'description'     => 'test desc',
            'meta'            => 'test meta',
            'callback'        => base_url('verify'), // must match your app's route
        ];

        $result = CollectPayment::standard($data); // no redirect, get JSON string
        $decoded = json_decode($result);

        echo "\n==== Raw decoded response ====\n";
        var_dump($decoded);

        if (isset($decoded->data->transactionRef)) {
            echo "\nTransaction Ref: " . (string)$decoded->data->transactionRef . "\n";
        } else {
            echo "\nNo transactionRef found in response.\n";
        }

        // Assert response structure
        $this->assertObjectHasProperty('statusCode', $decoded);
        $this->assertEquals('90000', $decoded->statusCode);
        $this->assertObjectHasProperty('data', $decoded);
        $this->assertObjectHasProperty('url', $decoded->data);
    }

    /**
     * Test recurring payment initiation.
     */
    public function testRecurringPayment(): void
    {
        $data = [
            'amount'          => 50,
            'customer_name'   => 'Dev Test',
            'customer_email'  => 'devtest@randomuser.com',
            'currency'        => 'NGN',
            'description'     => 'Weekly subscription',
            'meta'            => 'subscription_id:5678',
            'callback'        => base_url('verify'), // must match your app's route
            'frequency'       => 1,
            'is_recurring'    => true,
            'end_date'        => '2025-08-14T23:59:59Z',
        ];


        $result = CollectPayment::recurring($data); // get JSON string
        $decoded = json_decode($result);

        //    echo "\n==== Raw decoded response ====\n";
        var_dump($decoded);

        if (isset($decoded->data->transactionRef)) {
            echo "\nTransaction Ref: " . (string)$decoded->data->transactionRef . "\n";
        } else {
            echo "\nNo transactionRef found in response.\n";
        }

        $this->assertObjectHasProperty('statusCode', $decoded);
        $this->assertEquals('90000', $decoded->statusCode);
        $this->assertObjectHasProperty('data', $decoded);
        $this->assertObjectHasProperty('url', $decoded->data);
    }

    /**
     * Test cancelRecurring separately with a known transactionRef.
     */
    public function testCancelRecurring(): void
    {
        // ⚠ Replace this with a real transactionRef you got from testRecurringPayment or your dashboard
        $transactionRef = '36934683_76927e4cf6';

        if (empty($transactionRef)) {
            $this->fail('transactionRef is empty. Please replace with a real transactionRef to test cancelRecurring.');
        }

        $result = CollectPayment::cancelRecurring([
            'transactionRef' => $transactionRef,
            'token' => '2c382ed1-b5e5-4050-81fb-1b4d61443429',
        ]);

        $decoded = json_decode($result);

        echo "\n==== Cancel Recurring Response ====\n";
        var_dump($decoded);

        // Assertions
        $this->assertObjectHasProperty('statusCode', $decoded);
        $this->assertEquals('90000', $decoded->statusCode);
        $this->assertObjectHasProperty('message', $decoded);
    }


    /**
     * Test bank transfer initiation.
     */
    public function testBankTransfer(): void
    {
        $data = [
            'amount'         => 50,
            'customer_name'  => 'Dev Test',
            'email'          => 'devtest@randomuser.com',
            'currency'       => 'NGN',
            'description'    => 'Bank transfer test',
            'meta'           => 'order_id:1234',
            'callback'       => base_url('verify'),
        ];

        $result = CollectPayment::bankTransfer($data);
        $decoded = json_decode($result);

        //    echo "\n==== Raw decoded response ====\n";
        var_dump($decoded);

        $this->assertObjectHasProperty('statusCode', $decoded);
        $this->assertEquals('90000', $decoded->statusCode);
        $this->assertObjectHasProperty('data', $decoded);
    }

    /**
     * Test simulate bank transfer.
     */
    public function testSimulateBankTransfer(): void
    {
        $data = [
            'clientTransactionRef' => '36934683_774526591e', // A unique trax ref for the client’s transaction from bankTransfer.
            'currency'             => 'NGN',
            'amount'               => 500,
        ];

        $result = CollectPayment::simulateBankTransfer($data);
        $decoded = json_decode($result);

        //    echo "\n==== Raw decoded response ====\n";
        var_dump($decoded);

        $this->assertObjectHasProperty('statusCode', $decoded);
        $this->assertEquals('90000', $decoded->statusCode);
    }
}
