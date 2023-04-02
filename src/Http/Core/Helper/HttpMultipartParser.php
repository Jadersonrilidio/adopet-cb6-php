<?php

namespace Jayrods\ScubaPHP\Http\Core\Helper;

use InvalidArgumentException;

class HttpMultipartParser
{
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
    private ?array $partInfo = null;

    /**
     * 
     */
    private array $variables = [];

    /**
     * 
     */
    private array $files = [];

    /**
     * Set and parse Request Content-Type if needed.
     * 
     * @throws InvalidArgumentException
     */
    public function setContentType(string $contentType): void
    {
        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            $this->contentType = 'application/x-www-form-urlencoded';
            return;
        }

        if (str_contains($contentType, 'multipart/form-data')) {
            $this->contentType = 'multipart/form-data';
            $this->boundary = '--' . trim(str_replace('multipart/form-data; boundary=', '', $contentType));
            return;
        }

        if (str_contains($contentType, 'application/json')) {
            $this->contentType = 'application/json';
            return;
        }

        throw new InvalidArgumentException('Invalid Content-Type or not defined.');
    }

    /**
     * Parse stream context body according to previously set Content-Type in method 'setContentType'.
     */
    public function parse($stream): void
    {
        switch ($this->contentType) {
            case 'multipart/form-data':
                $this->parseMultipartFormData($stream);
                break;
            case 'application/x-www-form-urlencoded':
                $this->parseApplicationFormUrlEncoded($stream);
                break;
        }
    }

    /**
     * 
     */
    public function get(): array
    {
        return array(
            'variables' => $this->variables,
            'files' => $this->files,
        );
    }

    /**
     * 
     */
    private function parseApplicationFormUrlEncoded($stream): void
    {
        $content = stream_get_contents($stream);

        //todo
        $content = str_replace('%20', ' ', $content);
        $content = str_replace('%40', '@', $content);
        $content = explode("&", $content);

        foreach ($content as $line) {
            $param = explode("=", $line);
            $this->variables[$param[0]] = $param[1];
        }
    }

    /**
     * 
     */
    public function parseMultipartFormData($stream): void
    {
        $this->resetPartInfo();

        while ($lineN = fgets($stream)) {
            $line = trim($lineN);

            if ($this->boundary == $line) {
                continue;
            }

            if ($line === '') {
                if (!empty($this->partInfo['Content-Disposition']['filename'])) {
                    $this->parseFile($stream, $this->partInfo);
                } else if (!empty($this->partInfo['Content-Disposition']['name'])) {
                    $this->parseVariable($stream, $this->partInfo['Content-Disposition']['name']);
                }

                $this->resetPartInfo();
                continue;
            }

            $delimiter = strpos($line, ':');

            $headerKey = substr($line, 0, $delimiter);

            // $headerVal = str_replace('Content-Disposition: form-data; ', '', $line);

            $this->partInfo[$headerKey] = $this->parseHeaderValue($line, $headerKey);
        }
    }

    /**
     * 
     */
    private function parseHeaderValue(string $line, string $header = '')
    {
        $retval = [];

        $regex  = '/(^|;)\s*(?P<name>[^=:,;\s"]*):?(=("(?P<quotedValue>[^"]*(\\.[^"]*)*)")|(\s*(?P<value>[^=,;\s"]*)))?/mx';

        preg_match_all($regex, $line, $matches, PREG_SET_ORDER);

        foreach ($matches as $index => $match) {
            $name = $match['name'];

            if ($name == $header and $index == 0) {
                $name = 'value';
            }

            $quotedValue = stripcslashes($match['quotedValue']);

            $value = (empty($quotedValue)) ? $match['value'] : $quotedValue;

            $retval[$name] = $value;
        }

        return $retval;
    }

    /**
     * 
     */
    private function parseVariable($stream, string $name)
    {
        $fullValue = '';

        while ($lineN = fgets($stream) and strpos($lineN, $this->boundary) !== 0) {
            $fullValue .= trim($lineN);
        }

        $this->variables[$name] = $fullValue;
    }

    /**
     * 
     */
    private function parseFile($stream, array $partInfo)
    {
        $tempdir = sys_get_temp_dir();

        $name = $partInfo['Content-Disposition']['name'];
        $fileStruct['name'] = $partInfo['Content-Disposition']['filename'];
        $fileStruct['type'] = $partInfo['Content-Type']['value'];

        $this->files[$name] = &$fileStruct;

        if (empty($tempdir)) {
            $fileStruct['error'] = UPLOAD_ERR_NO_TMP_DIR;
            return;
        }

        $tempname = tempnam($tempdir, 'php_upload_');

        $outFP = fopen($tempname, 'wb');

        if ($outFP === false) {
            $fileStruct['error'] = UPLOAD_ERR_CANT_WRITE;
            return;
        }

        $lastLine = null;
        while ($lineN = fgets($stream, 4096)) {
            if ($lastLine != null) {
                if (strpos($lineN, $this->boundary) === 0) {
                    break;
                }
                if (fwrite($outFP, $lastLine) === false) {
                    $fileStruct = UPLOAD_ERR_CANT_WRITE;
                    return;
                }
            }
            $lastLine = $lineN;
        }

        if ($lastLine != null) {
            if (fwrite($outFP, rtrim($lastLine, '\r\n')) === false) {
                $fileStruct['error'] = UPLOAD_ERR_CANT_WRITE;
                return;
            }
        }
        $fileStruct['error'] = UPLOAD_ERR_OK;
        $fileStruct['size'] = filesize($tempname);
        $fileStruct['tmp_name'] = $tempname;
    }

    /**
     * 
     */
    private function resetPartInfo(): void
    {
        $this->partInfo = null;
    }
}
