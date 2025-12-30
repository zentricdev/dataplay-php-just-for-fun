[**EN**](./README.md) | [ES](./README.es.md)

---

# PayDay

**PayDay** is a PHP module designed to calculate the exact breakdown of banknotes and coins required for cash payments.

In the past, it was common practice to make periodic cash payments for items such as salary supplements, per diems, or representation expenses. This process presented several logistical challenges once the payment report was ready:

- **Bank Coordination:** You had to inform the bank of the exact total to be withdrawn, ensuring you requested the right mix of banknotes and coins for each individual payment. This was often done by estimation and was prone to miscalculation.

- **Filling Envelopes:** Manually stuffing envelopes was tedious and problematic if you ran out of specific change. When the denominations didn't match, previously processed envelopes often had to be opened and reorganized.

- **Risk of Error:** If the amount in the final envelope didn't match the remaining cash, it meant an error had occurred earlier in the process. This forced a full manual reconciliation—opening and verifying every single envelope until the mistake was found.

Fortunately, this practice is almost obsolete today. Consequently, this module serves less as a practical tool and more as a demonstration of the **recursion** technique in a real-world use case.

Nevertheless, its advantages include:

- **Accuracy:** Accurately calculates the precise quantity of banknotes and coins to request from the bank.

- **Operational Efficiency:** Saves time during the filling process by applying a consistent logic: using the largest possible denominations first to minimize the number of units handled.

- **Error Reduction:** Processing a €1,500 payment with three €500 banknotes is significantly less error-prone than using fifteen €100 banknotes.

## Components

### Currency

Represents a specific currency along with its available banknote and coin denominations.

### Cash

Handles the calculation of breaking down an amount into physical currency units.

The `Cash::breakdown(float $amount)` method accepts the initial amount and, using **recursion**, iterates through all available units (banknotes and coins) until the total is accounted for.

The `Cash::breakdownCollection(iterable $collection, ?string $attributeName = 'amount')` method processes a list of individual amounts and returns a consolidated summary. This summary provides the specific details required by a bank to prepare the cash order.

### currencies.php

A configuration file defining the banknote and coin denominations for each supported currency (EUR, USD, etc.).

### Command.php

The `make payday` command triggers `src/PayDay/Command.php`, which serves as a functional demonstration. It generates a set of random payments, processes them via `Cash::breakdownCollection`, and outputs a detailed report to the terminal:

```text
**PAYROL**
┌────────────┬──────────────┐
│ Employee   │ Amount (EUR) │
├────────────┼──────────────┤
│ Employee 1 │     1,036.87 │
│ Employee 2 │     1,034.00 │
│ Employee 3 │     1,197.67 │
├────────────┼──────────────┤
│            │     3,268.54 │
└────────────┴──────────────┘
**BREAKDOWN SUMMARY**
┌───────┬────────┬──────┬──────────────┐
│ Count │   Unit │ Type │ Amount (EUR) │
├───────┼────────┼──────┼──────────────┤
│     6 │ 500.00 │ note │     3,000.00 │
│     1 │ 100.00 │ note │       100.00 │
│     1 │  50.00 │ note │        50.00 │
│     4 │  20.00 │ note │        80.00 │
│     2 │  10.00 │ note │        20.00 │
│     2 │   5.00 │ note │        10.00 │
│     3 │   2.00 │ coin │         6.00 │
│     1 │   1.00 │ coin │         1.00 │
│     2 │   0.50 │ coin │         1.00 │
│     1 │   0.20 │ coin │         0.20 │
│     2 │   0.10 │ coin │         0.20 │
│     2 │   0.05 │ coin │         0.10 │
│     2 │   0.02 │ coin │         0.04 │
├───────┼────────┼──────┼──────────────┤
│       │        │      │     3,268.54 │
└───────┴────────┴──────┴──────────────┘
```

## Usage

### Basic example

```php
use J4F\PayDay\Currency;
use J4F\PayDay\Cash;

// Load currency configuration
$currencies = require 'currencies.php';
$currency = Currency::fromArray($currencies['EUR']);

// Create Cash instance
$cash = new Cash($currency);

// Breakdown an individual amount
$breakdown = $cash->breakdown(1234.56);
print_r($breakdown);
```

### Process a complete payroll

```php
use J4F\PayDay\Currency;
use J4F\PayDay\Cash;

// Define payroll
$payrol = [
    ['employee' => 'Employee 1', 'amount' => 1500.50],
    ['employee' => 'Employee 2', 'amount' => 1750.25],
    ['employee' => 'Employee 3', 'amount' => 1200.00],
];

// Configure currency
$currencies = require 'currencies.php';
$currency = Currency::fromArray($currencies['EUR']);

// Calculate total breakdown
$cash = new Cash($currency);
$results = $cash->breakdownCollection($payrol);

// Display results
foreach ($results as $result) {
    echo sprintf(
        "%d x %.2f %s = %.2f EUR\n",
        $result['count'], // number of banknotes or coins
        $result['denomination'], // value of banknote or coin
        $result['type'], // coin | note (coin or banknote)
        $result['amount'] // amount
    );
}
```

## Technical notes

- **Accuracy**: All amounts are processed internally in **cents** (multiplied by 100) to avoid precision issues with floating-point numbers.

- **Recursion**:The `breakdown()` method is recursive and uses a `$processing` flag to control the state.

- **Rounding**: The `round()` function is applied to avoid rounding errors in floating-point operations.

## Extension

To add support for a new currency, edit the `currencies.php` configuration file:

```php
return [
    'EUR' => [...],
    'USD' => [...],
    'GBP' => [
        'code' => 'GBP',
        'coins' => [0.01, 0.02, 0.05, 0.10, 0.20, 0.50, 1, 2],
        'notes' => [5, 10, 20, 50],
    ],
];
```
