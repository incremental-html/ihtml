<?php
declare(strict_types=1);

namespace iHTML\Command;

use Exception;
use GetOpt\GetOpt;
use iHTML\Ccs\Ccs;
use iHTML\Document\Document;
use iHTML\Filesystem\FileDirectory;
use iHTML\Filesystem\FileDirectoryExistent;
use iHTML\Filesystem\FileRegularExistent;
use iHTML\Project\Project;
use Throwable;

class Command
{
    /**
     * @return void
     */
    public static function execute(): void
    {
        $getOpt = new GetOpt('r:o:p::s::t:d:i:');
        $getOpt->process();
        $options = $getOpt->getOptions();
        $operands = $getOpt->getOperands();

        try {
            switch (true) {
                /**
                 * Server mode
                 */
                case isset($options['s']):
                     $port = $options['s'] != 1 ? $options['s'] : '1337';
                     $project = $options['p']; // TODO: add default
                     $static = $options['t']; // TODO: add default
                     self::startServer(
                         $port,
                         $project,
                         $static,
                     );
                    break;
                case isset($options['p']):
                    /**
                     * Compile project
                     */
                    $project = $options['p']; // TODO: add default on $options['p'] != 1
                    $output = $options['o']; // TODO: add default
                    $index = $options['i'] ?? null;
                    self::compileProject(
                        $project,
                        $output,
                        $index,
                    );
                    break;
                case isset($operands[0]) && isset($operands[1]):
                    /**
                     * Compile single file
                     */
                    $documentFile = (string)$operands[0];
                    $ccsFile = (string)$operands[1];
                    $output = $options['o'] ?? null;
                    self::compileFile(
                        $documentFile,
                        $ccsFile,
                        $output,
                    );
                    break;
                case isset($operands[0]) && isset($options['r']):
                    $documentFile = (string)$operands[0];
                    $ccsCode = $options['r'];
                    $ccsRoot = $options['d']; // TODO: add default
                    $output = $options['o'] ?? null;
                    self::applyFromParam(
                        $documentFile,
                        $ccsCode,
                        $ccsRoot,
                        $output,
                    );
                    break;
                case isset($operands[0]):
                    $documentFile = (string)$operands[0];
                    $ccsRoot = $options['d']; // TODO: add default
                    $output = $options['o'] ?? null;
                    self::compileFromStandardInput(
                        $documentFile,
                        $ccsRoot,
                        $output,
                    );
                    break;
                default:
                    print 'Please, insert template file' . "\n\n";
            }
        } catch (Throwable $exception) {
            print 'Error occurred:' . "\n";
            print_r($exception);
        }
    }

    private static function startServer(
        string $port,
        string $project,
        string $static
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
        $projectDir = new FileDirectoryExistent($projectDir, getcwd());
        $outputDir = new FileDirectory($outputDir, getcwd());
        $project = new Project($projectDir);
        $project->render($outputDir, $index);
    }

    private static function compileFile(
        string  $documentFile,
        string  $ccsFile,
        ?string $output,
    ): void
    {
        $documentFile = new FileRegularExistent($documentFile, getcwd());
        $ccsFile = new FileRegularExistent($ccsFile, getcwd());
        $document = new Document($documentFile);
        $ccs = Ccs::fromFile($ccsFile);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output, getcwd());
        } else {
            $document->print();
        }
    }

    private static function applyFromParam(
        string  $documentFile,
        string  $ccsCode,
        string  $ccsRoot,
        ?string $output,
    ): void
    {
        $documentFile = new FileRegularExistent($documentFile, getcwd());
        $ccsRoot = new FileDirectoryExistent($ccsRoot);
        $document = new Document($documentFile);
        $ccs = Ccs::fromChunk($ccsCode, $ccsRoot);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output, getcwd());
        } else {
            $document->print();
        }
    }

    private static function compileFromStandardInput(
        string  $documentFile,
        string  $ccsRoot,
        ?string $output,
    ): void
    {
        $documentFile = new FileRegularExistent($documentFile, getcwd());
        $document = new Document($documentFile);
        $ccsRoot = new FileDirectoryExistent($ccsRoot);
        $ccs = Ccs::fromChunk(file_get_contents('php://stdin'), $ccsRoot);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output, getcwd());
        } else {
            $document->print();
        }
    }
}