<?php

namespace Jayrods\ScubaPHP\Http\Core\Helper;

use Jayrods\ScubaPHP\Http\Core\Request;

class ParserTest
{
    /**
     * 
     */
    private const TEMPFILE_PATH = ROOT_DIR . SLASH . 'storage' . SLASH . 'file' . SLASH . 'putfile.txt';

    /**
     * 
     */
    private Request $request;

    /**
     * 
     */
    private string $contentType;

    /**
     * 
     */
    private ?string $boundary = null;

    /**
     * 
     */
    private array $putVars = [];

    /**
     * 
     */
    private array $files = [];

    /**
     * 
     */
    private array $tmpfile = array(
        'filename' => '',
        'content-type' => '',
        'name' => '',
        'value' => ''
    );

    /**
     * 
     */
    private array $tmpvar = array(
        'name' => '',
        'value' => ''
    );

    /**
     * 
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 
     */
    public function parse()
    {
        $this->setContentType($this->request->contentType());
        $this->parsePutVars();
    }

    /**
     * 
     */
    private function setContentType(string $contentType)
    {
        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            $this->contentType = 'application/x-www-form-urlencoded';
        } else if (str_contains($contentType, 'multipart/form-data')) {
            $this->contentType = 'multipart/form-data';
            $this->boundary = str_replace('multipart/form-data; boundary=', '', $contentType);
        } else if (str_contains($contentType, 'application/octet-stream')) {
            $this->contentType = 'application/octet-stream';
        } else if (str_contains($contentType, 'application/json')) {
            $this->contentType = 'application/json';
        }
    }

    /**
     * 
     */
    private function parsePutVars(): void
    {
        switch ($this->contentType) {
            case 'multipart/form-data':
                $this->parseMultipartFormDataContent();
                break;
            case 'application/x-www-form-urlencoded':
                $this->parseApplicationFormUrlEncoded();
                break;
        }
    }

    /**
     * 
     */
    private function parseApplicationFormUrlEncoded()
    {
        $putData = fopen("php://input", 'r');
        $content = stream_get_contents($putData);

        fclose($putData);

        $content = str_replace('%20', ' ', $content);
        $content = str_replace('%40', '@', $content);
        $content = explode("&", $content);

        foreach ($content as $line) {
            $keyValue = explode("=", $line);
            $this->putVars[$keyValue[0]] = $keyValue[1];
        }
    }

    /**
     * 
     */
    private function parseMultipartFormDataContent(): void
    {
        $content = file_get_contents("php://input");

        $fp = fopen(SELF::TEMPFILE_PATH, 'w');
        fwrite($fp, $content);

        fclose($fp);

        $handler = fopen(SELF::TEMPFILE_PATH, 'r');

        while ($line = fgets($handler)) {
            if (empty($line) or str_contains($line, $this->boundary)) {
                continue;
            }

            if (preg_match('/\sfilename\=\"([a-zA-Z0-9\-\.\_]+)\"/', $line, $matches)) {
                $this->tmpfile['filename'] = $matches[1];

                if (preg_match('/\sname\=\"([a-zA-Z0-9\-]+)\"/', $line, $matches)) {
                    $this->tmpfile['name'] = $matches[1];

                    $line = fgets($handler);
                    $this->tmpfile['content-type'] = str_replace('Content-Type: ', '', $line);

                    while ($line = fgets($handler)) {
                        if (str_contains($line, $this->boundary)) {
                            break;
                        }

                        $this->tmpfile['value'] .= $line;
                    }
                }

                $this->files[$this->tmpfile['name']] = array(
                    'filename' => $this->tmpfile['filename'],
                    'content-type' => $this->tmpfile['content-type'],
                    'value' => $this->tmpfile['value'],
                    'size' => strlen($this->tmpfile['value']),
                );

                unset($this->tmpfile);
            } else if (preg_match('/\sname\=\"([a-zA-Z0-9\-]+)\"/', $line, $matches)) {
                $this->tmpvar['name'] = $matches[1];

                while ($line = fgets($handler)) {
                    if (str_contains($line, $this->boundary)) {
                        break;
                    } else {
                        $this->tmpvar['value'] .= trim($line);
                    }
                }

                $this->putVars[$this->tmpvar['name']] = $this->tmpvar['value'];

                unset($this->tmpvar);
            }
        }

        fclose($handler);
    }

    /**
     * 
     */
    public function putVars()
    {
        return $this->putVars;
    }

    /**
     * 
     */
    public function files()
    {
        return $this->files;
    }
}
