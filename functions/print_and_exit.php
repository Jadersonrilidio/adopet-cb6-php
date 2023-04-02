<?php

declare(strict_types=1);

/**
 * Print arguments and exit.
 * 
 * @param mixed $arguments All sort of values/objects user wants to dump.
 * 
 * @return void
 */
function print_and_exit(): void
{
    foreach (func_get_args() as $arg) {
        print_r($arg);
        echo "__________________________________________________" . PHP_EOL;
    }
    exit(PHP_EOL . "PRINT AND EXIT" . PHP_EOL);
}

/**
 * Alias of print_and_exit().
 * 
 * @param mixed $arguments All sort of values/objects user wants to dump.
 * 
 * @return void
 */
function pae(): void
{
    foreach (func_get_args() as $arg) {
        print_r($arg);
        echo "__________________________________________________" . PHP_EOL;
    }
    exit(PHP_EOL . "PRINT AND EXIT" . PHP_EOL);
}
