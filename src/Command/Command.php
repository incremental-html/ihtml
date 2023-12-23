<?php

namespace iHTML\Command;

use Exception;
use GetOpt\GetOpt;
use iHTML\Ccs\CcsChunk;
use iHTML\Ccs\CcsFile;
use iHTML\Document\Document;
use iHTML\Messages\File;
use iHTML\Project\Project;
use SplFileInfo;
use Symfony\Component\Filesystem\Path;
use Throwable;

class Command
{
    public static function execute(): void
    {
        $getOpt = new GetOpt('r:o:p::s::t:d:i:');
        /**
         * r => passare il codice via parametro in shell
         * o => output, per il progetto la cartella, per i file il file,
         *  per i file è di default a schermo, per il progetto è la cwd
         * s => modalità server sul progetto
         * t => per il server, static, altrimenti è la cartella corrente
         * d => cartella di root per i css chunk, da input e da parametro
         * i => index di default
         */
        $options = $getOpt->getOptions();
        $operands = $getOpt->getOperands();

        try {
            if (isset($options['s'])) {
                self::server(
                    $options['p'] ?? __DIR__,
                    $options['t'] ?? __DIR__,
                    $options['s'] != 1 ? $options['s'] : 1337
                );
            } elseif (isset($options['p'])) {
                self::project(
                    $options['p'] != 1 ? $options['p'] : __DIR__,
                    $options['o'] ?? '.',
                    $options['i'] ?? null
                );
            } elseif (isset($operands[0]) && isset($operands[1])) {
                $index = $options['i'] ?? null;
                self::file(
                    $operands[0],
                    $operands[1],
                    $options['o'] ?? null,
                );
            } elseif (isset($operands[0]) && isset($options['r'])) {
                $documentFile = $operands[0];
                $ccsCode = $options['r'];
                $ccsRoot = $options['d'] ?? getcwd();
                $output = $options['o'] ?? null;
                self::param(
                    $documentFile,
                    $ccsCode,
                    $ccsRoot,
                    $output,
                );
            } elseif (isset($operands[0])) {
                $documentFile = $operands[0];
                $ccsRoot = $options['d'] ?? getcwd();
                $output = $options['o'] ?? null;
                self::input(
                    $documentFile,
                    $ccsRoot,
                    $output,
                );
            } else {
                print 'Please, insert template file' . "\n\n";
            }
        } catch (Throwable $exception) {
            print 'Error occurred:' . "\n";
            print_r($exception);
        }
    }

    private static function server(mixed $project, mixed $static, mixed $port): void
    {
        // $project = new Project(dir($project));
        // print 'Available paths:' . "\n\n";
        // $project->get()->map(function ($resource) use ($port) {
        //     print "  http://127.0.0.1:{$port}/{$resource->output}\n";
        // });
        // print "\n--\n\n";
        // $loop = Factory::create();
        // $server = new HttpServer($loop, function (ServerRequestInterface $request) use ($project, $static, $port, $options) {
        //     if ($resource = $project->get()->first(function ($res) use ($request) {
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
        // $server->listen($socket);
        // print "Listening to http://127.0.0.1:{$port}..." . PHP_EOL . PHP_EOL;
        // $loop->run();
    }

    /**
     * @throws Exception
     */
    private static function project(
        mixed   $project,
        mixed   $output,
        ?string $index,
    ): void
    {
        $pr = new Project(dir($project));
        $pr->render(new SplFileInfo(Path::makeAbsolute($output, getcwd())), $index);
    }

    private static function file(
        mixed   $documentFile,
        mixed   $ccsFile,
        ?string $output,
    ): void
    {
        $document = new Document($documentFile);
        $ccs = new CcsFile(new File($ccsFile));
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save(new File($output));
        }
    }

    private static function param(
        mixed   $documentFile,
        mixed   $ccsCode,
        mixed   $ccsRoot,
        ?string $output,
    ): void
    {
        $document = new Document($documentFile);
        $ccs = new CcsChunk($ccsCode, $ccsRoot);
        $ccs->applyTo($document);
        if (isset($output)) {
            $document->save($output);
        }
    }

    private static function input(
        mixed   $documentFile,
        mixed   $ccsRoot,
        ?string $output,
        ?string $index,
    ): void
    {
        $document = new Document($documentFile);
        $ccs = new CcsChunk(
            file_get_contents('php://stdin'),
            $ccsRoot
        );
        $ccs->applyTo($document);
        if (isset($output)) {

            $document->save($output, $index);
        }
    }
}