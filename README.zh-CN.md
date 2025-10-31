# Workerman Core Dump 清理器

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/workerman-core-dump-cleaner.svg?style=flat-square)](https://packagist.org/packages/tourze/workerman-core-dump-cleaner)
[![下载总量](https://img.shields.io/packagist/dt/tourze/workerman-core-dump-cleaner.svg?style=flat-square)](https://packagist.org/packages/tourze/workerman-core-dump-cleaner)
[![许可证](https://img.shields.io/github/license/tourze/workerman-core-dump-cleaner.svg?style=flat-square)](https://github.com/tourze/workerman-core-dump-cleaner/blob/master/LICENSE)

一个用于自动清理 Workerman 应用程序中 core dump 文件的简单包。

## 功能特点

- 自动清理 core dump 文件（`core`、`core.1`、`core.2` 等）
- 可配置的清理计划（使用 cron 表达式）
- 与 Workerman 应用程序简单集成
- 监控最多 20 个 core dump 文件（从 core 到 core.19）

## 系统要求

- PHP >= 8.1
- Workerman >= 5.1
- Workerman/Crontab >= 1.0.7

## 安装

```bash
composer require tourze/workerman-core-dump-cleaner
```

## 快速开始

```php
<?php

use Tourze\Workerman\CoreDumpCleaner\CoreDumpCleaner;

// 初始化时指定项目目录和可选的清理计划
// 默认每30秒执行一次
new CoreDumpCleaner('/path/to/project', '*/30 * * * * *');
```

## 配置说明

构造函数接受以下参数：

- `$projectDir`（字符串）：core dump 文件所在的目录
- `$rule`（字符串）：清理计划的 cron 表达式（默认：`*/30 * * * * *`）
- `$maxCoreFiles`（整数）：监控的最大 core 文件数量（默认：20）
- `$registerCrontab`（布尔值）：是否自动注册定时任务（默认：true）

### 高级用法

```php
<?php

use Tourze\Workerman\CoreDumpCleaner\CoreDumpCleaner;

// 自定义配置
$cleaner = new CoreDumpCleaner(
    projectDir: '/path/to/project',
    rule: '0 */10 * * * *',           // 每10分钟执行一次
    maxCoreFiles: 50,                 // 监控最多50个core文件
    registerCrontab: true             // 自动注册定时任务
);

// 用于测试（禁用定时任务注册）
$cleaner = new CoreDumpCleaner(
    projectDir: '/path/to/project',
    registerCrontab: false
);

// 手动清理
$results = $cleaner->cleanFiles();
```

## 贡献

欢迎提交 pull requests 或开 issues 来改进这个包。

## 许可证

MIT 许可证。请查看 [许可证文件](LICENSE) 获取更多信息。
