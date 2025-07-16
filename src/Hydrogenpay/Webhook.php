<?php

declare(strict_types=1);
/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: Webhook.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/11/25
 * Time: 7:00 PM
 */


namespace HydrogenAfrica\HydrogenpayCi4\Hydrogenpay;

use Config\Services;
use HydrogenAfrica\HydrogenpayCi4\HydrogenpayConfig;


class Webhook extends HydrogenpayConfig
{
    private $data;

    /**
     * Get webhook data from incoming request.
     */
    public static function data(): Webhook
    {
        $instance = new self();

        $request     = Services::incomingrequest();
        $receiveData = $request->getJSON();

        $instance->data = $receiveData;

        return $instance;
    }

    /**
     * Optionally verify IP address to ensure webhook comes from HydrogenPay.
     * You can improve this by checking against the official IP list.
     */
    public static function verifyWebhook(): bool
    {
        $request = Services::incomingrequest();
        $ip      = $request->getIPAddress();

        $validIps = ['20.54.14.223', '20.67.189.4'];

        return in_array($ip, $validIps, true);
    }

    public function id(): string
    {
        return $this->data->id;
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

    public function description(): ?string
    {
        return $this->data->description ?? null;
    }

    public function status(): string
    {
        return $this->data->status;
    }

    public function transactionRef(): string
    {
        return $this->data->transactionRef;
    }

    public function createdAt(): ?string
    {
        return $this->data->createdAt ?? null;
    }

    public function paidAt(): ?string
    {
        return $this->data->paidAt ?? null;
    }

    public function ipAddress(): ?string
    {
        return $this->data->ip ?? null;
    }

    public function paymentType(): ?string
    {
        return $this->data->paymentType ?? null;
    }

    public function fees(): float
    {
        return (float) $this->data->fees ?? 0.0;
    }

    public function vat(): float
    {
        return (float) $this->data->vat ?? 0.0;
    }

    public function recurringCardToken(): ?string
    {
        return $this->data->recurringCardToken ?? null;
    }

    public function meta(): ?string
    {
        return $this->data->meta ?? null;
    }
}
