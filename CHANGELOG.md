## 1.1.0 | 2025-04-10
Performance enhancements, feature updates and bugfixes.

Changes include:
- [ADDED] Test for card Preauthorization, ENAIRA, FAWRYPAY and GOOGLEPAY.
- [FIXED] Removed enums to support PHP v7.4 and above.
- [UPDATED] Code refactor.

## 1.0.6 | 2023-07-24
Performance Optimization and Feature Update.

Changes include:
- [FIXED] RequeryPayment method on checkout
- [ADDED] Support for TANZANIA MOBILE MONEY
- [ADDED] Support for FAWRY PAY
- [ADDED] Support for GOOGLEPAY
- [ADDED] Support for E-NAIRA Payment.
- [UPDATED] Simplified the Custom Configuration Contract to allow for easy configuration with the package.
- [SECURITY & PERF] Checkout Process and Callback in processPayment.php

## 1.0.5 | 2023-01-24
This change allows the package to automatically detect .env file and use the variables declared in them.

Changes include:
- [UPDATE] Log files generated in project directory.
- [REFACTOR] remove unused files from distribution. 

## 1.0.4 | 2022-06-11
This release adds support for PHP version 7.4 and above, as well as a new workflow for old and new tests.

Changes include:
- [ADDED] Support for PHP 7.4 and above
- [ADDED] New workflow for old and new tests
