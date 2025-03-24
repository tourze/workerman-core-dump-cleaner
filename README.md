# Workerman Core Dump Cleaner

[English](#english) | [中文](#中文)

## English

A simple package to automatically clean up core dump files in Workerman applications.

### Features

- Automatically cleans up core dump files (`core`, `core.1`, `core.2`, etc.)
- Configurable cleanup schedule using cron expression
- Simple integration with Workerman applications

### Requirements

- PHP >= 8.1
- Workerman >= 5.1
- Workerman/Crontab >= 1.0.7

### Installation

```bash
composer require tourze/workerman-core-dump-cleaner
```

### Usage

```php
use Tourze\Workerman\CoreDumpCleaner\CoreDumpCleaner;

// Initialize with project directory and optional cron schedule
// Default schedule is every 30 seconds
new CoreDumpCleaner('/path/to/project', '*/30 * * * * *');
```

### Configuration

The constructor accepts two parameters:

- `$projectDir` (string): The directory where core dump files are located
- `$rule` (string): Cron expression for cleanup schedule (default: `*/30 * * * * *`)

---

## 中文

一个用于自动清理 Workerman 应用程序中 core dump 文件的简单包。

### 功能特点

- 自动清理 core dump 文件（`core`、`core.1`、`core.2` 等）
- 可配置的清理计划（使用 cron 表达式）
- 与 Workerman 应用程序简单集成

### 系统要求

- PHP >= 8.1
- Workerman >= 5.1
- Workerman/Crontab >= 1.0.7

### 安装

```bash
composer require tourze/workerman-core-dump-cleaner
```

### 使用方法

```php
use Tourze\Workerman\CoreDumpCleaner\CoreDumpCleaner;

// 初始化时指定项目目录和可选的清理计划
// 默认每30秒执行一次
new CoreDumpCleaner('/path/to/project', '*/30 * * * * *');
```

### 配置说明

构造函数接受两个参数：

- `$projectDir`（字符串）：core dump 文件所在的目录
- `$rule`（字符串）：清理计划的 cron 表达式（默认：`*/30 * * * * *`）
