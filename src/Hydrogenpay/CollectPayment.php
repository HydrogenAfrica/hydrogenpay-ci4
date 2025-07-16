<?php

declare(strict_types=1);

/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: CollectPayment.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/10/25
 * Time: 7:00 PM
 */


namespace HydrogenAfrica\HydrogenpayCi4\Hydrogenpay;

use Config\Services;
use HydrogenAfrica\HydrogenpayCi4\HydrogenpayConfig;

class CollectPayment extends HydrogenpayConfig
{
    /**
     * Initiate a standard (one-time) payment.
     * 
     * @param array $data Payment details.
     * @param bool $redirect Whether to redirect user to payment page.
     * @return mixed RedirectResponse|string
     * @throws \Exception on failure.
     */
    public static function standard(array $data, bool $redirect = true)
    {
        $client = Services::curlrequest();

        // Prepare request headers including Authorization
        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . self::secretKey(),
        ];

        // Build payload for payment
        $payload = [
            'amount'         => $data['amount'],
            'customerName'   => $data['customer_name'],
            'email'          => $data['customer_email'],
            'currency'       => $data['currency'] ?? 'NGN',
            'description'    => $data['description'] ?? '',
            'meta'           => $data['meta'] ?? '',
            'transactionRef' => $data['transaction_ref'] ?? null,
            'callback'       => $data['callback'],
        ];

        // Add recurring fields if present
        if (!empty($data['frequency'])) {
            $payload['frequency'] = $data['frequency'];
        }
        if (!empty($data['is_recurring'])) {
            $payload['isRecurring'] = $data['is_recurring'];
        }
        if (!empty($data['end_date'])) {
            $payload['endDate'] = $data['end_date'];
        }

        // Make API call to HydrogenPay
        $request = $client->request('POST', self::BASE_URL . '/merchant/initiate-payment', [
            'headers'     => $headers,
            'json'        => $payload,
            'http_errors' => false,
        ]);

        $response = json_decode($request->getBody());

        // Check for errors
        if ($request->getStatusCode() !== 200 || $response->statusCode != "90000") {
            throw new \Exception($response->message ?? 'Payment initiation failed');
        }

        // Redirect user to payment URL if $redirect is true
        if ($redirect) {
            return redirect()->to($response->data->url);
        }

        // Otherwise, return raw response as JSON string
        return json_encode($response);
    }

    /**
     * Initiate recurring payment.
     */
    public static function recurring(array $data, bool $redirect = true)
    {
        // Validate recurring-specific fields
        if (empty($data['frequency']) || empty($data['is_recurring']) || empty($data['end_date'])) {
            throw new \InvalidArgumentException('Missing required recurring fields: frequency, is_recurring, end_date');
        }

        $client = Services::curlrequest();

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . self::secretKey(),
        ];

        // Build payload
        $payload = [
            'amount'         => $data['amount'],
            'customerName'   => $data['customer_name'],
            'email'          => $data['customer_email'],
            'currency'       => $data['currency'] ?? 'NGN',
            'description'    => $data['description'] ?? '',
            'meta'           => $data['meta'] ?? '',
            'transactionRef' => $data['transaction_ref'] ?? null,
            'callback'       => $data['callback'],
            'frequency'      => $data['frequency'],
            'isRecurring'    => $data['is_recurring'],
            'endDate'        => $data['end_date'],
        ];

        $request = $client->request('POST', self::BASE_URL . '/merchant/initiate-payment', [
            'headers'     => $headers,
            'json'        => $payload,
            'http_errors' => false,
        ]);

        $response = json_decode($request->getBody());

        // Handle errors
        if ($request->getStatusCode() !== 200 || $response->statusCode != "90000") {
            throw new \Exception($response->message ?? 'Payment initiation failed');
        }

        // Redirect or return JSON
        if ($redirect) {
            return redirect()->to($response->data->url);
        }

        return json_encode($response);
    }

    /**
     * Cancel a recurring payment by transactionRef and optional token.
     */
    public static function cancelRecurring(array $data)
    {
        if (empty($data['transactionRef'])) {
            throw new \InvalidArgumentException('transactionRef is required.');
        }

        $client = Services::curlrequest();

        $payload = [
            'transactionRef' => $data['transactionRef'],
        ];

        // Add token if provided
        if (!empty($data['token'])) {
            $payload['token'] = $data['token'];
        }

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . self::secretKey(),
        ];

        $request = $client->request('POST', self::BASE_URL . '/Merchant/deactivate-recurring-card-token', [
            'headers'     => $headers,
            'json'        => $payload,
            'http_errors' => false,
        ]);

        $response = json_decode($request->getBody());

        if ($request->getStatusCode() !== 200 || $response->statusCode != "90000") {
            throw new \Exception($response->message ?? 'Failed to cancel recurring payment');
        }

        return json_encode($response);
    }

    /**
     * Initiate bank transfer payment.
     */
    public static function bankTransfer(array $data)
    {
        $client = Services::curlrequest();

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . self::secretKey(),
        ];

        $payload = [
            'amount'         => $data['amount'] ?? null,
            'customerName'   => $data['customer_name'] ?? null,
            'email'          => $data['email'] ?? null,
            'currency'       => $data['currency'] ?? 'NGN',
            'description'    => $data['description'] ?? null,
            'meta'           => $data['meta'] ?? null,
            'transactionRef' => $data['transactionRef'] ?? null,
            'callback'       => $data['callback'] ?? null,
        ];

        $request = $client->request('POST', self::BASE_URL . '/Merchant/initiate-bank-transfer', [
            'headers'     => $headers,
            'json'        => $payload,
            'http_errors' => false,
        ]);

        $response = json_decode($request->getBody());

        if ($request->getStatusCode() !== 200 || $response->statusCode != "90000") {
            throw new \Exception($response->message ?? 'Failed to initiate bank transfer');
        }

        return json_encode($response);
    }

    /**
     * Simulate a bank transfer payment (for testing only).
     */
    public static function simulateBankTransfer(array $data)
    {
        $client = Services::curlrequest();

        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . self::secretKey(),
            'Mode'          => '19289182', // Required simulation header
        ];

        $payload = [
            'clientTransactionRef' => $data['clientTransactionRef'] ?? null,
            'currency'             => $data['currency'] ?? 'NGN',
            'amount'               => $data['amount'] ?? null,
        ];

        $request = $client->request('POST', self::BASE_URL . '/Payment/simulate-bank-transfer', [
            'headers'     => $headers,
            'json'        => $payload,
            'http_errors' => false,
        ]);

        $response = json_decode($request->getBody());

        if ($request->getStatusCode() !== 200 || $response->statusCode != "90000") {
            throw new \Exception($response->message ?? 'Failed to simulate bank transfer');
        }

        return json_encode($response);
    }
}
