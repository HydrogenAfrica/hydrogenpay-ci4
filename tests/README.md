# HydrogenPay – CodeIgniter SDK Tests

Unit tests for the **HydrogenPay CodeIgniter SDK**, covering core payment operations like standard & recurring payments, bank transfers, and webhooks.

This guide helps you set up and run the tests locally to validate your integration.

---

## Requirements

- PHP 8.x
- [Composer](https://getcomposer.org/)
- CodeIgniter 4
- [PHPUnit](https://phpunit.de/) (recommended version 9.x)

---

## Installation & Setup

* Install PHPUnit locally:

```bash
- composer require --dev phpunit/phpunit

- Update or create phpunit.xml (or phpunit.xml.dist) in your project root with:

<testsuites>
    <testsuite name="Unit">
        <directory>app/tests/unit</directory>
    </testsuite>
</testsuites>

This tells PHPUnit to look for test cases under app/tests/unit.


- Test Cases Included

Test File	            | Purpose
CollectPaymentTest.php	| Standard & recurring payments, bank transfer, cancel test
VerificationTest.php	| Payment verification
WebhookTest.php	        | Handle and simulate webhook events

All test files are inside:

app/tests/unit

```

* Running the Tests

Run all tests:

```console
>   ./vendor/bin/phpunit

```

Run a single test file:

```console
>   ./vendor/bin/phpunit app/tests/unit/CollectPaymentTest.php

```

Filter to run only specific test method(s):

```console
./vendor/bin/phpunit --filter testStandardPayment

```

## Optional: Generate Code Coverage

* To see how much of your code is covered by tests:

```console
./vendor/bin/phpunit --coverage-html=tests/coverage/

```

Then open:

tests/coverage/index.html 
in your browser.


## References

* [CodeIgniter 4 User Guide on Testing](https://codeigniter4.github.io/userguide/testing/index.html)
* [PHPUnit docs](https://phpunit.de/documentation.html)
* [Any tutorials on Unit testing in CI4?](https://forum.codeigniter.com/showthread.php?tid=81830)

## Contributing
Feel free to add more test cases, edge scenarios, or submit improvements!
Pull requests are welcome.

## License
© HydrogenPay – All rights reserved.