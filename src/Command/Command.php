<?php
declare(strict_types=1);

namespace iHTML\Command;

use Exception;
use GetOpt\GetOpt;
use iHTML\Filesystem\FileDirectory;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use iHTML\iHTML\Ccs;
use iHTML\iHTML\Document;
use iHTML\iHTML\Project;
use Sabberworm\CSS\Parsing\SourceException;
use Throwable;

class Command
{
    private static FileDirectoryExistent $workingDir;

    /**
     * @throws Exception
     */
    public static function execute(): void
    {
        $getOpt = new GetOpt([
            ['r', 'code', GetOpt::REQUIRED_ARGUMENT, 'Code to execute'],
            ['o', 'output', GetOpt::REQUIRED_ARGUMENT, 'Output dir or file (depends on mode)'],
            ['p', 'project', GetOpt::OPTIONAL_ARGUMENT, 'Project to compile'],
            ['s', 'server', GetOpt::OPTIONAL_ARGUMENT, '(Server mode) port to launch server on'],
            ['t', 'static', GetOpt::REQUIRED_ARGUMENT, '(Server mode) Directory of static file', getcwd()],
            ['d', 'dir', GetOpt::REQUIRED_ARGUMENT, 'Working dir of code snippets', getcwd()],
            ['i', 'index', GetOpt::REQUIRED_ARGUMENT, 'Default index file name for `/`'],
            ['e', 'inheritance', GetOpt::NO_ARGUMENT, 'Print inheritance'],
            ['v', 'verbose', GetOpt::NO_ARGUMENT, 'Make verbose'],
            ['h', 'help', GetOpt::NO_ARGUMENT, 'This help'],
        ]);
        $getOpt->process();
        $options = $getOpt->getOptions();
        $operands = $getOpt->getOperands();

        self::$workingDir = new FileDirectoryExistent(getcwd());
        try {
            switch (true) {
                case isset($options['h']):
                    print $getOpt->getHelpText();
                    break;
                case isset($options['s']):
                    self::startServer(
                        $options['s'] != 1 ? $options['s'] : '1337',
                        $options['p'] ?? self::$workingDir,
                        $options['t'],
                    );
                    break;
                case isset($options['p']):
                    self::compileProject(
                        $options['p'] != 1 ? $options['p'] : self::$workingDir,
                        $options['o'] ?? null,
                        $options['i'] ?? null,
                    );
                    break;
                case isset($options['e']):
                    self::printInheritance(
                        (string)$operands[0],
                    );
                    break;
                case isset($operands[0]) && isset($operands[1]):
                    self::compileFile(
                        (string)$operands[0],
                        (string)$operands[1],
                        $options['o'] ?? null,
                    );
                    break;
                case isset($operands[0]) && isset($options['r']):
                    self::applyFromParameter(
                        (string)$operands[0],
                        $options['r'],
                        $options['d'],
                        $options['o'] ?? null,
                    );
                    break;
                case isset($operands[0]):
                    self::compileFromStandardInput(
                        (string)$operands[0],
                        $options['d'],
                        $options['o'] ?? null,
                    );
                    break;
                default:
                    print 'Please, insert template file' . "\n\n";
            }
        } catch (Throwable $exception) {
            print "Error occurred:\n{$exception->getMessage()}\n\n";
            if ($options['v'] ?? false) {
                print_r($exception);
            }
        }
    }

    private static function startServer(
        string $port,
        string $project,
        string $static,
    ): void
    {
        // $compileProject = new Project(dir($compileProject));
        // print 'Available paths:' . "\n\n";
        // $compileProject->get()->map(function ($resource) use ($port) {
        //     print "  http://127.0.0.1:{$port}/{$resource->output}\n";
        // });
        // print "\n--\n\n";
        // $loop = Factory::create();
        // $startServer = new HttpServer($loop, function (ServerRequestInterface $request) use ($compileProject, $static, $port, $options) {
        //     if ($resource = $compileProject->get()->first(function ($res) use ($request) {
        //         return '/' . $res->output === $request->getUri()->getPath();
        //     })) {
        //         $resource->ccs->applyTo($resource->document);
        //         $body = $resource->document->render(null, $options['i'] ?? null)->get();
        //         print "Rendered http://127.0.0.1:{$port}{$request->getUri()->getPath()}\n";
        //         return new Response(200, ['Content-Type' => 'text/html'], $body);
        //     }
        //
        //     if (file_exists($static . '/' . $request->getUri()->getPath())) {
        //         print "Rendered http://127.0.0.1:{$port}{$request->getUri()->getPath()}\n";
        //         return new Response(200, ['Content-Type' => getMimetype($static . '/' . $request->getUri()->getPath())], file_get_contents($static . '/' . $request->getUri()->getPath()));
        //     }
        //
        //     print "Rendered http://127.0.0.1:{$port}{$request->getUri()->getPath()}\n";
        //     return new Response(404, ['Content-Type' => 'text/plain'], "404 - Page not found\n");
        // });
        // print 'Server loaded.' . PHP_EOL;
        // $socket = new SocketServer($port, $loop);
        // $startServer->listen($socket);
        // print "Listening to http://127.0.0.1:{$port}..." . PHP_EOL . PHP_EOL;
        // $loop->run();
    }

    /**
     * @throws Exception
     */
    private static function compileProject(
        string  $projectDir,
        string  $outputDir,
        ?string $index,
    ): void
    {
        $projectDir = new FileDirectoryExistent($projectDir, self::$workingDir);
        $outputDir = new FileDirectory($outputDir, self::$workingDir);
        $project = new Project($projectDir);
        $project->render($outputDir, $index);
        print 'Project compiled successfully' . "\n\n";
    }

    /**
     * @throws Exception
     */
    private static function compileFile(
        string  $documentFile,
        string  $ccsFile,
        ?string $output,
    ): void
    {
        $documentFile = new FileRegularExistent($documentFile, self::$workingDir);
        $ccsFile = new FileRegularExistent($ccsFile, self::$workingDir);
        $document = new Document($documentFile);
        $ccs = Ccs::fromFile($ccsFile);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output, self::$workingDir);
        } else {
            $document->print();
        }
        print 'File compiled successfully' . "\n\n";
    }

    /**
     * @throws Exception
     */
    private static function applyFromParameter(
        string  $documentFile,
        string  $ccsCode,
        string  $ccsRoot,
        ?string $output,
    ): void
    {
        $documentFile = new FileRegularExistent($documentFile, self::$workingDir);
        $ccsRoot = new FileDirectoryExistent($ccsRoot);
        $document = new Document($documentFile);
        $ccs = Ccs::fromString($ccsCode, $ccsRoot);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output, self::$workingDir);
        } else {
            $document->print();
        }
        print 'Ccs code applied successfully' . "\n\n";
    }

    /**
     * @throws Exception
     */
    private static function compileFromStandardInput(
        string  $documentFile,
        string  $ccsRoot,
        ?string $output,
    ): void
    {
        $documentFile = new FileRegularExistent($documentFile, self::$workingDir);
        $document = new Document($documentFile);
        $ccsRoot = new FileDirectoryExistent($ccsRoot);
        $ccs = Ccs::fromString(file_get_contents('php://stdin'), $ccsRoot);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output, self::$workingDir);
        } else {
            $document->print();
        }
        print 'Ccs code applied successfully' . "\n\n";
    }

    /**
     * @throws SourceException
     * @throws Exception
     */
    private static function printInheritance(string $ccsFile): void
    {
        $prettyTree = function (string $file, array $includes, int $level = 0) use (&$prettyTree) {
            return str_repeat(' ', $level * 2) . $file . "\n" .
                collect($includes)
                    ->map(fn($include, $file) => $prettyTree($file, $include, $level + 1))
                    ->join('')
            ;
        };

        $ccsFile = new FileRegularExistent($ccsFile, self::$workingDir);
        $ccs = Ccs::fromFile($ccsFile);
        $inheritance = $ccs->getInheritance();
        print 'Hierarchy:' . "\n\n";
        print $prettyTree((string)$ccsFile, $inheritance);
    }
}