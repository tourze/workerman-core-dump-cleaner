# Workerman Core Dump Cleaner

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/workerman-core-dump-cleaner.svg?style=flat-square)](https://packagist.org/packages/tourze/workerman-core-dump-cleaner)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/workerman-core-dump-cleaner.svg?style=flat-square)](https://packagist.org/packages/tourze/workerman-core-dump-cleaner)
[![License](https://img.shields.io/github/license/tourze/workerman-core-dump-cleaner.svg?style=flat-square)](https://github.com/tourze/workerman-core-dump-cleaner/blob/master/LICENSE)

A simple package to automatically clean up core dump files in Workerman applications.

## Features

- Automatically cleans up core dump files (`core`, `core.1`, `core.2`, etc.)
- Configurable cleanup schedule using cron expression
- Simple integration with Workerman applications
- Monitors up to 20 core dump files (core to core.19)

## Requirements

- PHP >= 8.1
- Workerman >= 5.1
- Workerman/Crontab >= 1.0.7

## Installation

```bash
composer require tourze/workerman-core-dump-cleaner
```

## Quick Start

```php
<?php

use Tourze\Workerman\CoreDumpCleaner\CoreDumpCleaner;

// Initialize with project directory and optional cron schedule
// Default schedule is every 30 seconds
new CoreDumpCleaner('/path/to/project', '*/30 * * * * *');
```

## Configuration

The constructor accepts two parameters:

- `$projectDir` (string): The directory where core dump files are located
- `$rule` (string): Cron expression for cleanup schedule (default: `*/30 * * * * *`)

## Contributing

Please feel free to submit pull requests or open issues to improve this package.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
