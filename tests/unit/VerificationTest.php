<?php

declare(strict_types=1);

/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: VerificationTest.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/14/25
 * Time: 1:59 PM
 */

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\Verification;

use Exception;

/**
 * @internal
 */
final class VerificationTest extends CIUnitTestCase
{
    /**
     * Test payment verification by transactionRef.
     *
     * @throws Exception
     */
    public function testPaymentVerification(): void
    {
        // Replace with a real transaction ref
        $transactionRef = '36934683_76927e4cf6';

        try {
            $verification = Verification::transaction($transactionRef);

            // Dump some fields so you can see
            echo "\nVerified Transaction Ref: " . $verification->transactionRef() . "\n";
            echo "Status: " . $verification->status() . "\n";
            echo "Amount: " . $verification->amount() . "\n";

            // Assertions: check that required data is present
            $this->assertNotEmpty($verification->transactionRef());
            $this->assertNotEmpty($verification->status());
            $this->assertGreaterThan(0, $verification->amount());
            $this->assertEquals($transactionRef, $verification->transactionRef());

        } catch (Exception $e) {
            // Fail the test with the exception message
            $this->fail('Verification failed: ' . $e->getMessage());
        }
    }
}
