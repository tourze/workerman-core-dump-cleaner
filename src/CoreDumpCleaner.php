<?php

namespace Tourze\Workerman\CoreDumpCleaner;

use Workerman\Crontab\Crontab;

/**
 * 在部分环境，程序出错会产生很多core dump文件，目前我们收集来也不会分析，干脆直接删了算了
 */
class CoreDumpCleaner
{
    public function __construct(string $projectDir, string $rule = '*/30 * * * * *')
    {
        // 自动清理Core dump文件
        $checkFiles = ['core'];
        $i = 1;
        while ($i < 20) {
            $checkFiles[] = "core.{$i}";
            ++$i;
        }
        new Crontab($rule, function () use ($projectDir, $checkFiles) {
            foreach ($checkFiles as $name) {
                $file = "{$projectDir}/{$name}";
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        });
    }
}
