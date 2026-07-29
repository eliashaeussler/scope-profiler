<div align="center">

# Scope Profiler

[![Coverage](https://img.shields.io/coverallsCoverage/github/eliashaeussler/scope-profiler?logo=coveralls)](https://coveralls.io/github/eliashaeussler/scope-profiler)
[![CI](https://img.shields.io/github/actions/workflow/status/eliashaeussler/scope-profiler/ci.yaml?label=CI&logo=github)](https://github.com/eliashaeussler/scope-profiler/actions/workflows/ci.yaml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/eliashaeussler/scope-profiler/php?logo=php)](https://packagist.org/packages/eliashaeussler/scope-profiler)

</div>

A simple profiler to measure duration, memory usage and memory peak of various scoped actions.

## 🔥 Installation

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/scope-profiler?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/scope-profiler)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/scope-profiler?color=brightgreen)](https://packagist.org/packages/eliashaeussler/scope-profiler)

```bash
composer require eliashaeussler/scope-profiler
```

## ⚡️ Usage

### Initalization

Initialize a new [`ScopeProfiler\ScopeProfiler`](src/ScopeProfiler.php) instance – either as singleton
if you prefer to use a shared instance or as a new instance if you want to use multiple profilers:

```php
use Eliashaeussler\ScopeProfiler;

// Option A: Shared instance (singleton)
$profiler = ScopeProfiler\ScopeProfiler::get();

// Option B: New instance
$profiler = new ScopeProfiler\ScopeProfiler();
```

To push a new scope, call `pushScope()` and pass the scope:

```php
$scope = new ScopeProfiler\Scope('Crawling the internet');

$profiler->pushScope($scope);
```

In case the scope should be detached from the profiler instance, simply call `pullScope()`:

```php
$profiler->pullScope($scope);
```

### Run task

You will receive an instance of [`ScopeProfiler\Scope`](src/Scope.php) which you can use to measure the
duration, memory usage and memory peak of the action. This can either be done automatically by using the
`run()` method or manually by using the `start()` and `stop()` methods:

```php
$task = new SomeLongRunningTask();

// Option A: Automatic start and stop
$result = $scope->run($task->execute(...));

// Option B: Manual start, execute, and stop
$scope->start();
$result = $task->execute();
$scope->stop();
```

### Measure scope

You can always profile a scope by measuring the current duration, memory usage and memory peak
– either while the scope is still active or after it has finished:

```php
$measurement = $scope->measure();
```

The received object is an instance of [`ScopeProfiler\Measurement`](src/Measurement.php) which contains
the following properties:

```php
$duration = $measurement->duration; // float
$memoryUsage = $measurement->memoryUsage; // int
$memoryPeak = $measurement->memoryPeak; // int
```

### Format measurement

To get a nicely formatted string representation of the measurement, call any of the `format*()` methods:

```php
$formattedMeasurement = $measurement->format(); // Crawling the internet took 7 min and consumed 8 GiB of memory (peak at 16 GiB)
$formattedDuration = $measurement->formatDuration(); // 7 min
$formattedMemoryUsage = $measurement->formatMemoryUsage(); // 8 GiB
$formattedMemoryPeak = $measurement->formatMemoryPeak(); // 16 GiB
```

### Release scopes

You can use the profiler to release all scopes at once:

```php
$scopes = $profiler->releaseScopes();
```

This is especially useful if you want to summarize the results of all scopes at once.

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 3.0 (or later)](LICENSE).
