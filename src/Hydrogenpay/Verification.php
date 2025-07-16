<?php

declare(strict_types=1);
/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: Verification.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/11/25
 * Time: 6:54 PM
 */

namespace HydrogenAfrica\HydrogenpayCi4\Hydrogenpay;

use Config\Services;
use Exception;
use HydrogenAfrica\HydrogenpayCi4\HydrogenpayConfig;

class Verification extends HydrogenpayConfig
{
    private $data;

    /**
     * @throws Exception
     */

    /**
     * Confirm payment by transactionRef
     * @throws Exception
     */
    public static function transaction(string $transactionRef): Verification
    {
        $instance = new self();

        $client = \Config\Services::curlrequest();

        $headers = [
            'Authorization' => 'Bearer ' . self::secretKey(),
            'Content-Type'  => 'application/json',
        ];

        $payload = [
            'transactionRef' => $transactionRef,
        ];

        $request = $client->request('POST', self::BASE_URL . '/Merchant/confirm-payment', [
            'headers'     => $headers,
            'json'        => $payload,
            'http_errors' => false,
        ]);

        $response = json_decode($request->getBody());

        // Check for HTTP errors or API statusCode not equal to 90000
        if ($request->getStatusCode() !== 200 || $response->statusCode != "90000") {
            throw new Exception($response->message ?? 'Failed to confirm payment');
        }

        // Store transaction data
        $instance->data = $response->data;

        return $instance;
    }


    // Expose methods to get data easily:
    public function id(): string
    {
        return (string) $this->data->id;
    }

    public function transactionRef(): string
    {
        return $this->data->transactionRef;
    }

    public function amount(): float
    {
        return (float) $this->data->amount;
    }

    public function chargedAmount(): float
    {
        return (float) $this->data->chargedAmount;
    }

    public function currency(): string
    {
        return $this->data->currency;
    }

    public function customerEmail(): string
    {
        return $this->data->customerEmail;
    }

    public function narration(): ?string
    {
        return $this->data->narration ?? null;
    }

    public function status(): string
    {
        return $this->data->status;
    }

    public function createdAt(): string
    {
        return $this->data->createdAt;
    }

    public function paidAt(): ?string
    {
        return $this->data->paidAt ?? null;
    }

    public function paymentType(): ?string
    {
        return $this->data->paymentType ?? null;
    }

    public function processorResponse(): ?string
    {
        return $this->data->processorResponse ?? null;
    }

    public function fees(): float
    {
        return (float) ($this->data->fees ?? 0);
    }

    public function ip(): ?string
    {
        return $this->data->ip ?? null;
    }

    public function meta(): ?string
    {
        return $this->data->meta ?? null;
    }
}
