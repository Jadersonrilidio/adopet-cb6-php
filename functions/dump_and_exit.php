<?php

declare(strict_types=1);

/**
 * Dump arguments and exit.
 * 
 * @param mixed $arguments All sort of values/objects user wants to dump.
 * 
 * @return void
 */
function dump_and_exit(): void
{
    foreach (func_get_args() as $arg) {
        var_dump($arg);
        echo "__________________________________________________<br>";
    }
    exit(PHP_EOL . "<br> DUMP AND EXIT" . PHP_EOL);
}

/**
 * Alias of dump_and_exit().
 * 
 * @param mixed $arguments All sort of values/objects user wants to dump.
 * 
 * @return void
 */
function dae(): void
{
    foreach (func_get_args() as $arg) {
        var_dump($arg);
        echo "__________________________________________________<br>";
    }
    exit(PHP_EOL . "<br> DUMP AND EXIT" . PHP_EOL);
}
