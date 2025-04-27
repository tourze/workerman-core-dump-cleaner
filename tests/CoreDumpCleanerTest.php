<?php

namespace Tourze\Workerman\CoreDumpCleaner\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\CoreDumpCleaner\CoreDumpCleaner;

/**
 * 测试 CoreDumpCleaner 类
 */
class CoreDumpCleanerTest extends TestCase
{
    /**
     * 测试文件临时目录
     */
    private string $tempDir;

    /**
     * 测试前准备
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 创建临时目录
        $this->tempDir = sys_get_temp_dir() . '/core_dump_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    /**
     * 测试后清理
     */
    protected function tearDown(): void
    {
        // 删除临时目录及其内容
        $this->removeDirectory($this->tempDir);

        parent::tearDown();
    }

    /**
     * 递归删除目录及其内容
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    $this->removeDirectory($path);
                } else {
                    unlink($path);
                }
            }
        }

        rmdir($dir);
    }

    /**
     * 测试构建检查文件列表
     */
    public function testBuildCheckFilesList(): void
    {
        // 使用自定义的最大文件数，禁用定时任务注册
        $cleaner = new CoreDumpCleaner('/tmp', '*/30 * * * * *', 5, false);

        $expected = ['core', 'core.1', 'core.2', 'core.3', 'core.4'];
        $this->assertEquals($expected, $cleaner->buildCheckFilesList());

        // 使用默认的最大文件数，禁用定时任务注册
        $cleaner = new CoreDumpCleaner('/tmp', '*/30 * * * * *', 20, false);
        $files = $cleaner->getCheckFiles();

        $this->assertCount(20, $files);
        $this->assertEquals('core', $files[0]);
        $this->assertEquals('core.19', $files[19]);
    }

    /**
     * 测试获取器方法
     */
    public function testGetters(): void
    {
        $projectDir = '/test/dir';
        $rule = '0 * * * * *';
        $maxCoreFiles = 10;

        // 禁用定时任务注册
        $cleaner = new CoreDumpCleaner($projectDir, $rule, $maxCoreFiles, false);

        $this->assertEquals($projectDir, $cleaner->getProjectDir());
        $this->assertEquals($rule, $cleaner->getRule());
        $this->assertCount($maxCoreFiles, $cleaner->getCheckFiles());
    }

    /**
     * 测试删除文件
     */
    public function testDeleteFile(): void
    {
        // 禁用定时任务注册
        $cleaner = new CoreDumpCleaner($this->tempDir, '*/30 * * * * *', 20, false);

        // 创建测试文件
        $testFile = $this->tempDir . '/test_file.txt';
        file_put_contents($testFile, 'test content');

        // 确认文件存在
        $this->assertFileExists($testFile);

        // 删除文件
        $result = $cleaner->deleteFile($testFile);

        // 验证结果
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($testFile);

        // 测试删除不存在的文件
        $result = $cleaner->deleteFile($testFile);
        $this->assertFalse($result);
    }

    /**
     * 测试清理文件
     */
    public function testCleanFiles(): void
    {
        // 创建模拟的 CoreDump 文件
        $coreFile = $this->tempDir . '/core';
        $coreFile1 = $this->tempDir . '/core.1';

        file_put_contents($coreFile, 'core dump content');
        file_put_contents($coreFile1, 'core dump content 1');

        // 创建 cleaner 对象
        $cleaner = $this->getMockBuilder(CoreDumpCleaner::class)
            ->setConstructorArgs([$this->tempDir, '*/30 * * * * *', 20, false])
            ->onlyMethods(['deleteFile'])
            ->getMock();

        // 设置期望，deleteFile 将被调用两次并返回 true
        $cleaner->expects($this->exactly(2))
            ->method('deleteFile')
            ->willReturn(true);

        // 设置检查文件列表
        $cleaner->setCheckFiles(['core', 'core.1', 'core.2']);

        // 执行清理
        $results = $cleaner->cleanFiles();

        // 验证结果
        $this->assertTrue($results[$this->tempDir . '/core']);
        $this->assertTrue($results[$this->tempDir . '/core.1']);
        $this->assertFalse($results[$this->tempDir . '/core.2']);
    }

    /**
     * 测试实际清理文件功能
     */
    public function testActualCleanFiles(): void
    {
        // 创建模拟的 CoreDump 文件
        $coreFile = $this->tempDir . '/core';
        $coreFile1 = $this->tempDir . '/core.1';
        $coreFile2 = $this->tempDir . '/core.2';

        file_put_contents($coreFile, 'core dump content');
        file_put_contents($coreFile1, 'core dump content 1');
        // core.2 文件不存在

        // 禁用定时任务注册
        $cleaner = new CoreDumpCleaner($this->tempDir, '*/30 * * * * *', 20, false);
        $cleaner->setCheckFiles(['core', 'core.1', 'core.2']);

        // 确认文件存在
        $this->assertFileExists($coreFile);
        $this->assertFileExists($coreFile1);
        $this->assertFileDoesNotExist($coreFile2);

        // 执行清理
        $results = $cleaner->cleanFiles();

        // 验证结果
        $this->assertTrue($results[$coreFile]);
        $this->assertTrue($results[$coreFile1]);
        $this->assertFalse($results[$this->tempDir . '/core.2']);

        // 确认文件已被删除
        $this->assertFileDoesNotExist($coreFile);
        $this->assertFileDoesNotExist($coreFile1);
    }
}
