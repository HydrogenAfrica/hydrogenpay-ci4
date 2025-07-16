<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\CollectPayment;
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\Verification;
use HydrogenAfrica\HydrogenpayCi4\Hydrogenpay\Webhook;

class Home extends BaseController
{
	/**
	 * Redirect user to standard (one-time) payment link.
	 * Adjust the payload data as needed.
	 */
	public function index()
	{
		$data = [
			'amount'          => 50,
			'customer_name'   => 'Dev Test',
			'customer_email'  => 'bwitlawalyusuf@gmail.com',
			'currency'        => 'NGN',
			'description'     => 'test desc',
			'meta'            => 'test meta',
			// 'callback'        => 'https://hydrogenpay.com',
			'callback'        => 'http://localhost:8080/verify',

		];

		return CollectPayment::standard($data);
	}

	/**
	 * Initiate a recurring payment.
	 * Adjust 'frequency', 'is_recurring', and 'end_date' as needed.
	 */
	public function recurring()
	{
		$data = [
			'amount'         => 50,
			'customer_name'  => 'Dev Test',
			'customer_email' => 'devtest@randomuser.com',
			'currency'       => 'NGN',
			'description'    => 'Weekly subscription payment',
			'meta'           => 'Subscription ID: 123',
			'callback'       => 'https://hydrogenpay.com',
			'frequency'      => 1, // weekly
			'is_recurring'   => true,
			'end_date'       => '2024-12-29T19:01:41.745Z',
		];

		return CollectPayment::recurring($data);
	}

	/**
	 * Cancel an existing recurring payment.
	 * Provide both 'transactionRef' and 'token' if applicable.
	 */
	public function cancelRecurring()
	{
		$data = [
			'transactionRef' => 'csE8CyZTn3OD',
			'token'          => '2c382ed1-b5e5-4050-81fb-1b4d61443429',
		];

		$response = CollectPayment::cancelRecurring($data);

		echo $response;
	}

	/**
	 * Payment verification page.
	 * Retrieves transactionRef from query param, confirms payment status, and shows details.
	 */
	public function verify()
	{
		try {
			// Get transactionRef from URL: /verify?transactionRef=xxx
			$transactionRef = $this->request->getGet('TransactionRef');

			if (!$transactionRef) {
				return 'Transaction reference is missing.';
			}

			// Call API to confirm payment status
			$verification = Verification::transaction($transactionRef);

			// For debugging: dump entire object
			echo '<pre>';
			var_dump($verification);
			echo '</pre>';

			// You could also use:
			// echo "Transaction Status: " . $verification->status() . "<br>";
			// echo "Amount: " . $verification->amount() . "<br>";
			// echo "Customer Email: " . $verification->customerEmail() . "<br>";
			// echo "Transaction Reference: " . $verification->transactionRef() . "<br>";

		} catch (\Exception $e) {
			echo "Error: " . $e->getMessage();
		}
	}

	/**
	 * Initiate a dynamic virtual bank transfer.
	 * Returns virtual account number, expiry, and other details.
	 */
	public function bankTransfer()
	{
		$data = [
			'amount'        => 50,
			'customer_name' => 'Dev Test',
			'email'         => 'devtest@randomuser.com',
			'currency'      => 'NGN',
			'description'   => 'test description',
			'meta'          => 'test meta',
			'callback'      => base_url('verify'),  // replace with your live callback
			// 'transactionRef' => 'custom_ref_1234', // optional
		];

		// Send request & print raw response for testing
		echo '<pre>';
		var_dump(CollectPayment::bankTransfer($data));
		echo '<pre>';

		// Example: instead of var_dump, you could:
		// $response = json_decode(CollectPayment::bankTransfer($data), true);
		// return view('bank_transfer_view', [
		//     'virtualAccountNo'   => $response['data']['virtualAccountNo'] ?? '',
		//     'virtualAccountName' => $response['data']['virtualAccountName'] ?? '',
		//     'expiryDateTime'     => $response['data']['expiryDateTime'] ?? '',
		//     'transactionRef'     => $response['data']['transactionRef'] ?? '',
		//     'bankName'           => $response['data']['bankName'] ?? '',
		//     'transactionStatus'  => $response['data']['transactionStatus'] ?? '',
		// ]);
	}

	/**
	 * Simulate a bank transfer (for testing only).
	 */
	public function simulateTransfer()
	{
		$data = [
			'clientTransactionRef' => '36934683_135796c393', // use your test ref
			'currency'             => 'NGN',
			'amount'               => '50',
		];

		try {
			$response = json_decode(CollectPayment::simulateBankTransfer($data), true);

			echo '<pre>';
			print_r($response);
			echo '</pre>';
		} catch (\Exception $e) {
			echo 'Error: ' . $e->getMessage();
		}
	}

	/**
	 * Handle incoming payment webhook from HydrogenPay.
	 * Verifies the source IP and prints payload.
	 */
	public function webhook()
	{
		// ✅ Check if request came from allowed IPs
		if (!Webhook::verifyWebhook()) {
			return 'Unauthorized source';
		}

		// Get webhook data object
		$webhook = Webhook::data();

		echo '<pre>';
		var_dump([
			'Transaction Ref' => $webhook->transactionRef(),
			'Amount'          => $webhook->amount(),
			'Status'          => $webhook->status(),
			'Customer Email'  => $webhook->customerEmail(),
		]);
		echo '</pre>';

		// ✅ Here you should update payment status in your database, etc.
	}
}
