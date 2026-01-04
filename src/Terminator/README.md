[**EN**](./README.md) | [ES](./README.es.md)

---

# SkyNet and Terminator: A PHP Simulation

This directory houses a **functional blueprint** that emulates the "Terminator" universe in a playful and educational way. The project serves a dual purpose: conceptually recreating a mission assigned to a Terminator unit within a hypothetical **PHP 11.4** environment, while acting as a practical case study for modern PHP techniques and architectural patterns.

## Thematic Context

Inspired by the 1984 film _The Terminator_, the simulation features the artificial superintelligence **SkyNet** (`Skynet.php`), which builds and deploys an infiltration unit known as **T-800** (`T800.php`). The unit is sent to a specific spatio-temporal coordinate to eliminate its primary target: **Sarah Connor**.

The `Command.php` file serves as the entry point, where the T-800 is instantiated, assigned its target, and ordered to fulfill its mission.

## PHP Concepts Illustrated

This project leverages several design patterns and modern PHP features (8.1+).

### 1. Abstract Classes and Inheritance

- **`Skynet.php`**: Defined as an `abstract class`. It cannot be instantiated directly but establishes the system's foundation, containing fundamental constants like the corporation (`Cyberdyne Systems`) and versioning, under the playful assumption that SkyNet is developed in a future **PHP 11.4** environment.

- **`T800.php`**: Inherits from `Skynet` (`extends Skynet`), specializing the base functionality for a combat unit.

### 2. Final Classes

- **`T800.php`**, DTOs, and the `SkyNetException` are declared as `final`. This prevents further inheritance, ensuring the implementation remains immutable and unique, a best practice for domain-specific logic.

### 3. Data Transfer Objects (DTOs) and Readonly Classes

To ensure data integrity, the project utilizes immutable DTOs:

- **`DTOs/SpatioTemporalLocation.php`**: A `readonly class` representing space-time coordinates.

- **`DTOs/Target.php`**: A `readonly class` defining the target, which encapsulates the `SpatioTemporalLocation` DTO.

- **Constructor Property Promotion**: Both classes use this concise syntax for declaring and initializing properties directly within the constructor signature.

- **String Representation**: Both DTOs implement the `__toString()` magic method. This allows the objects to be directly embedded into log messages, as PHP automatically converts them into a human-readable string (e.g., coordinates and timestamps), significantly simplifying the logging logic within the main classes.

### 4. Builder Pattern & Fluent Interface

- The `T800` class implements a variant of the **Builder pattern**. Methods like `setTarget()` and `relocate()` return `$this`, allowing for a **Fluent Interface**. This results in highly readable code within `Command.php`.

### 5. Recursion and Static State Persistence

- **Stateful Recursion**: The `accomplish()` method uses **recursion** to mirror the machine's relentless nature.

- **Static Variables**: Instead of passing the attempt count as a function argument (which would clutter the method's signature), the project utilizes a `static $attempts` variable inside the method. This advanced PHP technique allows the function to "remember" its state across recursive calls, maintaining a persistent counter throughout multiple temporal loops without exposing it to the outside world. This demonstrates a cleaner, more encapsulated approach to state management in recursive algorithms.

### 6. Semantic Exception Handling

- **`Exceptions/SkyNetException.php`**: A custom exception used to handle domain-specific errors. This demonstrates a robust and semantic approach to error management beyond generic PHP exceptions.

- **Static Throw Pattern**: The exception implements a static `throw()` method. This allows the system to trigger errors using a cleaner syntax (`SkyNetException::throw('...')`) instead of the traditional `throw new` instantiation. This pattern centralizes the exception creation and can be extended to handle specific error types with descriptive method names.

### 7. Spatio-Temporal Synchronization

- **Dynamic Timeline Adjustment**: The `relocate()` method performs a crucial internal state change. When the Terminator is displaced, the system's internal clock synchronizes with the target's timeline (`$this->timeline = $this->target->location->timeline`). This is reflected in the execution logs, where you can observe the jump from the origin date in 2029 to the target's arrival date in 1984, simulating a real-time temporal displacement.

- **Technical Note on Logging**: The `Logger` class operates independently of the host's system time. Instead, it calculates timestamps by combining the internal `$timeline` property, initialized at the moment of deployment (July 11, 2029), with the `$timelineClock`. This clock captures the exact `microtime()` of the timeline's start, allowing the system to offset the future date by the actual elapsed seconds of the operation. This ensures that logs remain chronologically consistent with the movie's canon, even as the unit transitions from the future to the past during the `relocate()` sequence.

### 8. Timeline Divergence & Persistence

While the films often depict the Terminator's failure, this simulation reflects SkyNet's ultimate multi-timeline strategy. Every execution of `accomplish()` represents a specific branch of reality.

The use of **static variables within recursion** allows the unit to track its attempts across these temporal loops. If a mission fails (`random_int` simulation), the unit triggers an "I'll be back" exception and re-executes. From SkyNet's perspective, the mission is only complete when the target is null, meaning the simulation will persist until it finds the specific timeline where the Terminator successfully eliminates the target.

### 9. Logging System

The `Logger` class provides a timeline-synchronized logging mechanism. It maintains an internal timeline and mission clock, calculating timestamps by offsetting the starting date with elapsed seconds. Messages are stored in an array with precise timestamps and output to the console with ANSI color codes for improved readability. This ensures logs remain consistent with the simulation's fictional chronology, even during temporal displacements.

## File Breakdown

- **`Command.php`**: The main execution script.

- **`Models/`**:
  - `Skynet.php`: The abstract base defining SkyNet's core attributes.
  - `T800.php`: The T-800 implementation containing the mission logic.
  - `Logger.php`: Timeline-synchronized logging system with colored output.

- **`DTOs/`**:

  - `Target.php`: Structure for target data (Sarah Connor).
  - `SpatioTemporalLocation.php`: Space-time coordinate definition (Los Ángeles, 1984).

- **`Exceptions/`**:
  - `SkyNetException.php`: Custom domain-specific exception.

## Execution

Once the repository is downloaded and the dependencies are installed (via `composer install`), you can initiate the mission directly from your terminal.

The project includes a `Makefile` to simplify the interaction with the T-800 unit. To run the simulation and witness the temporal displacement and mission logs, execute `make terminator`:

```text
2029-07-11 22:38:14 BUILDING UNIT Terminator SERIES T-800 MODEL 101
2029-07-11 22:38:15 TARGET SET TO Sarah Connor, Big Jeff's waitress, Born 1965
1984-05-12 01:52:01 UNIT RELOCATED TO Lat: 34.0522, Lon: 118.2437, 1984-05-12 01:52:00 (America/Los_Angeles)
1984-05-12 01:52:02 ACQUIRING TARGET...
1984-05-12 01:52:04 MISSION FAILED - TARGET ESCAPED - I'LL BE BACK
1984-05-12 01:52:06 ACQUIRING TARGET...
1984-05-12 01:52:09 MISSION FAILED - TARGET ESCAPED - I'LL BE BACK
1984-05-12 01:52:12 ACQUIRING TARGET...
1984-05-12 01:52:16 MISSION FAILED - TARGET ESCAPED - I'LL BE BACK
1984-05-12 01:52:20 ACQUIRING TARGET...
1984-05-12 01:52:25 MISSION ACCOMPLISHED - TARGET TERMINATED AFTER 4 ATTEMPTS
```
