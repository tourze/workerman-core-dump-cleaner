<?php

namespace Tourze\Workerman\CoreDumpCleaner;

use Workerman\Crontab\Crontab;

/**
 * 在部分环境，程序出错会产生很多core dump文件，目前我们收集来也不会分析，干脆直接删了算了
 */
class CoreDumpCleaner
{
    /**
     * @var array<string> 需要检查的 Core Dump 文件列表
     */
    private array $checkFiles;

    /**
     * @param string $projectDir      Core dump 文件所在的目录
     * @param string $rule            清理计划的 cron 表达式
     * @param int    $maxCoreFiles    最大监控的 core 文件数量
     * @param bool   $registerCrontab 是否注册定时任务（便于测试时禁用）
     */
    public function __construct(
        private string $projectDir,
        private string $rule = '*/30 * * * * *',
        private int $maxCoreFiles = 20,
        bool $registerCrontab = true,
    ) {
        $this->checkFiles = $this->buildCheckFilesList();

        if ($registerCrontab) {
            $this->registerCrontab();
        }
    }

    /**
     * 构建需要检查的文件列表
     *
     * @return array<string>
     */
    public function buildCheckFilesList(): array
    {
        $checkFiles = ['core'];
        $i = 1;
        while ($i < $this->maxCoreFiles) {
            $checkFiles[] = "core.{$i}";
            ++$i;
        }

        return $checkFiles;
    }

    /**
     * 注册定时任务
     */
    private function registerCrontab(): void
    {
        new Crontab($this->rule, $this->cleanFiles(...));
    }

    /**
     * 清理 Core Dump 文件
     *
     * @return array<string, bool> 包含文件路径和删除结果的数组
     */
    public function cleanFiles(): array
    {
        $results = [];

        foreach ($this->checkFiles as $name) {
            $file = "{$this->projectDir}/{$name}";
            $results[$file] = false;

            if (is_file($file)) {
                $results[$file] = $this->deleteFile($file);
            }
        }

        return $results;
    }

    /**
     * 删除指定文件
     *
     * @param string $file 文件路径
     *
     * @return bool 删除是否成功
     */
    public function deleteFile(string $file): bool
    {
        if (!file_exists($file)) {
            return false;
        }

        try {
            return unlink($file);
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * 获取项目目录
     */
    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    /**
     * 获取 Cron 规则
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * 获取检查的文件列表
     *
     * @return array<string>
     */
    public function getCheckFiles(): array
    {
        return $this->checkFiles;
    }

    /**
     * 设置检查的文件列表（用于测试）
     *
     * @param array<string> $checkFiles
     */
    public function setCheckFiles(array $checkFiles): void
    {
        $this->checkFiles = $checkFiles;
    }

    /**
     * 设置项目目录（用于测试）
     */
    public function setProjectDir(string $projectDir): void
    {
        $this->projectDir = $projectDir;
    }
}
