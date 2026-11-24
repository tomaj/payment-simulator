<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Runs every ```php block of README.md and docs/ in its own process, with the Composer autoloader and a `$gateway`
 * (omnipay-tatra's TatraPay gateway) already defined, so the documented snippets keep working. The complete clients
 * are the examples in examples/, which the Examples workflow runs against the simulator.
 */
final class DocsSnippetsTest extends TestCase
{
    private const string PRELUDE = <<<'PHP'
        <?php
        require %s;
        $gateway = Omnipay\Omnipay::create('TatraPay');
        PHP;

    /**
     * @return iterable<string, array{string}>
     */
    public static function blockProvider(): iterable
    {
        $root = dirname(__DIR__);
        $files = [$root . '/README.md'];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root . '/docs', \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            \assert($file instanceof \SplFileInfo);
            $path = $file->getPathname();
            if ($file->getExtension() === 'md' && !str_contains($path, '/node_modules/') && !str_contains($path, '/.vitepress/')) {
                $files[] = $path;
            }
        }
        sort($files);

        foreach ($files as $path) {
            $lines = explode("\n", (string) file_get_contents($path));
            $count = count($lines);
            for ($i = 0; $i < $count; $i++) {
                if (trim($lines[$i]) !== '```php') {
                    continue;
                }
                $start = $i + 1;
                $body = [];
                for ($i++; $i < $count && !str_starts_with(trim($lines[$i]), '```'); $i++) {
                    $body[] = $lines[$i];
                }
                yield substr($path, strlen($root) + 1) . ':' . $start => [implode("\n", $body) . "\n"];
            }
        }
    }

    public function testDocumentationHasBlocks(): void
    {
        self::assertGreaterThanOrEqual(2, iterator_count(self::blockProvider()));
    }

    #[DataProvider('blockProvider')]
    public function testBlockRuns(string $code): void
    {
        $script = tempnam(sys_get_temp_dir(), 'snippet');
        $prelude = tempnam(sys_get_temp_dir(), 'prelude');
        self::assertIsString($script);
        self::assertIsString($prelude);
        try {
            file_put_contents($prelude, sprintf(self::PRELUDE, var_export(__DIR__ . '/../vendor/autoload.php', true)));
            file_put_contents($script, "<?php\n\ndeclare(strict_types=1);\n\n" . preg_replace('/^<\?php\s*/', '', $code));
            $process = proc_open(
                [PHP_BINARY, '-d', 'auto_prepend_file=' . $prelude, '-d', 'display_errors=stderr', '-d', 'error_reporting=-1', $script],
                [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                $pipes,
            );
            self::assertIsResource($process);
            $stdout = (string) stream_get_contents($pipes[1]);
            $stderr = (string) stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $status = proc_close($process);
        } finally {
            unlink($script);
            unlink($prelude);
        }

        self::assertSame('', $this->withoutDependencyDeprecations($stderr), "The block wrote to stderr:\n{$code}\n{$stderr}");
        self::assertSame(0, $status, "The block failed:\n{$code}\n{$stdout}");
    }

    /**
     * Deprecations raised inside dependencies are not ours to fix (omnipay/common on PHP 8.6).
     */
    private function withoutDependencyDeprecations(string $stderr): string
    {
        $lines = preg_split('~\R~', $stderr);
        $lines = array_filter(
            $lines === false ? [$stderr] : $lines,
            static fn(string $line): bool => trim($line) !== '' && preg_match('~^(PHP )?Deprecated:\s.* in \S+/vendor/\S+ on line \d+$~', $line) !== 1,
        );

        return implode("\n", $lines);
    }
}
