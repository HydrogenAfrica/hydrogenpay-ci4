# Hydrogenpay SDK for CodeIgniter 4

Hydrogenpay PHP SDK for CodeIgniter 4 makes it easy to connect your apps to the Hydrogenpay APIs.  
It simplifies integration, so you can quickly collect payments, handle payouts, and more — all in just a few lines of code.

## Features

| Operation              | Description                                                                                                 |
|-----------------------:|-------------------------------------------------------------------------------------------------------------|
| **Single Payment**     | Create a one-time payment request using either card or bank transfer.                                        |
| **Recurring Payment**  | Set up a subscription-based payment request for card payments.                                               |
| **Cancel Recurring**   | Cancel a recurring transaction or deactivate a card token associated with a recurring payment.               |
| **Simulate Transfer**  | To test the bank transfer functionality during the development phase of integrating the Payment Gateway.     |
| **Initiate Transfer**  | Generate unique account details to receive transfer-only payment requests.                                   |
| **Payment Confirmation**| Confirm and validate the status of a completed card or transfer payment.                                    |
| **Payment Webhook**     | The webhook allows you to receive instant notifications about payment status from the payment gateway.      |

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Usage](#usage)
4. [Contributing](#contributing)
5. [License](#license)
6. [API References](#api-references)

---

## Requirements

- PHP >= ^8.0 
- CodeIgniter 4
- [Hydrogenpay API keys](https://docs.hydrogenpay.com/reference/api-authentication)

---

## Installation

Install via Composer:

```bash
composer require hydrogenafrica/hydrogenpay-ci4

```

Then copy the provided .env.example file to .env and update with your Hydrogenpay credentials:

```bash
cp .env.example .env
```

Example .env settings:

```bash
LIVE_API_KEY=SK_LIVE_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
SANDBOX_KEY=PK_TEST_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
MODE=TEST

```
* LIVE_API_KEY: Your HydrogenPay live API key obtained from the dashboard. (Required)
* SANDBOX_KEY: Your HydrogenPay sandbox API key obtained from the dashboard. (Required)
* MODE: Defines the environment mode. Set to LIVE to use the LIVE_API_KEY or TEST to use the SANDBOX_KEY. (Required)

## Usage

### Single Payment
Create a one-time payment request using either card or bank transfer.

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

Then initiate a payment:

```bash

 $data = [
            'amount'          => 50,
            'customer_name'   => 'Dev Test',
            'customer_email'  => 'devtest@randomuser.com',
            'currency'        => 'NGN',
            'description'     => 'test desc',
            'meta'            => 'test meta',
            'callback'        => base_url('verify'), // must match your app's route
        ];

        # return CollectPayment::standard($data); 
        $result = CollectPayment::recurring($data); // get JSON string
        $decoded = json_decode($result);

        echo "\n==== Raw decoded response ====\n";
        var_dump($decoded);

```


### Recurring Payment
Set up a subscription-based payment request for card payments.

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

```bash

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


        return CollectPayment::recurring($data);

```

### Cancel Recurring
Cancel a recurring transaction or deactivate a card token associated with a recurring payment. 

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

```bash

        $transactionRef = '36934683_76927e4cf6'; // Replace this with a real transactionRef

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

```

### Initiate Transfer
Generate unique account details to receive transfer-only payment requests.

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

```bash

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

        echo "\n==== Bank Transfer Response ====\n";
        var_dump($decoded);

```

### Simulate Transfer
To test the bank transfer functionality during the development phase of integrating the Payment Gateway.
Developers are required to select the bank transfer method at the payment checkout stage before making the request

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

```bash

         $data = [
            'clientTransactionRef' => '36934683_774526591e', // A unique trax ref for the client’s transaction
            'currency'             => 'NGN',
            'amount'               => 50,
        ];

        $result = CollectPayment::simulateBankTransfer($data);
        $decoded = json_decode($result);

        echo "\n==== Raw decoded response ====\n";
        var_dump($decoded);

```

### Payment Confirmation
Confirm and validate the status of a completed card or transfer payment.  

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

```bash
   
        $transactionRef = '36934683_76927e4cf6'; // Replace with a real transaction ref

        try {
            $verification = Verification::transaction($transactionRef);

            // Dump some fields so you can see
            echo "\nVerified Transaction Ref: " . $verification->transactionRef() . "\n";
            echo "Status: " . $verification->status() . "\n";
            echo "Amount: " . $verification->amount() . "\n";

        } catch (Exception $e) {
            // Fail the test with the exception message
            $this->fail('Verification failed: ' . $e->getMessage());
        }

```

### Payment Webhook
The webhook allows you to receive instant notifications about payment status from the payment gateway.

First, import the class:

```bash
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;

```

```bash
   
   //Verify Webhook
if (Webhook::verifyWebhook())
   {
      // Continue reading the webhook data
      Webhook::data()->status();

   }


```

## API References

* [Hydrogenpay Dashboard](https://dashboard.hydrogenpay.com/)
* [Hydrogenpay Developer Docs](https://docs.hydrogenpay.com/reference/api-authentication)

## Contributing
Feel free to add more test cases, edge scenarios, or submit improvements!
Pull requests are welcome.

## License
© HydrogenPay – All rights reserved.