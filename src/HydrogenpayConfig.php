<?php

declare(strict_types=1);

/**
 * Copyright (C) Hydrogenpay - All Rights Reserved
 *
 * File: HydrogenpayConfig.php
 * Author: Hydrogenpay
 *   Email: <support@hydrogenpay.com>
 *   Website: https://hydrogenpay.com
 * Date: 07/8/25
 * Time: 7:00 PM
 */

namespace HydrogenAfrica\HydrogenpayCi4;

class HydrogenpayConfig
{
    /**
     * Base API URL for HydrogenPay.
     */
    public const BASE_URL = 'https://api.hydrogenpay.com/bepay/api/v1';

    /**
     * Retrieve the HydrogenPay secret key.
     *
     * - If dynamic settings are enabled, get it from the CodeIgniter Settings service.
     * - Otherwise, load from the .env environment variable.
     *
     * @return string|null
     */
    protected static function secretKey()
    {
        $config = config('Hydrogenpay');
        if ($config->enableDynamicSettings) {
            return service('settings')->get('Hydrogenpay.secretKey');
        }

    // Check MODE from env; default to TEST if not set
    $mode = strtoupper(env('MODE', 'TEST'));

    if ($mode === 'LIVE') {
        return env('LIVE_API_KEY');
    }

    return env('SANDBOX_KEY');

    }
}
