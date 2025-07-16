<?php

declare(strict_types=1);

namespace HydrogenAfrica\HydrogenpayCi4\Config;

use CodeIgniter\Config\BaseConfig;

class Hydrogenpay extends BaseConfig
{
  /*
     * Enable dynamic settings:
     * - If set to true, API secrets keys will be loaded from this config file
     *   instead of your .env file.
     * - If you have CodeIgniter4 settings installed, those settings will override both.
     */
  public bool $enableDynamicSettings = false;

  /*
     * Your HydrogenPay secret key.
     * Used only if $enableDynamicSettings is true.
     */
  public string $secretKey = '';

}
