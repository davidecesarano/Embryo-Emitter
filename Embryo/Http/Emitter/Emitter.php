<?php 

    /**
     * Emitter
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-emitter 
     */

    namespace Embryo\Http\Emitter;

    use Psr\Http\Message\ResponseInterface;

    class Emitter implements EmitterInterface
    {
        /**
         * @var array $responseIsEmpty
         */
        private $responseIsEmpty = [204, 205, 304];

        /**
         * @var int $sizeLimit
         */
        private $sizeLimit = 4096;

        /**
         * Emits headers, protocol, status code and reason
         * phrase from response.
         *
         * @param ResponseInterface $response
         * @return void
         */
        private function headers(ResponseInterface $response): void
        {
            if (!headers_sent()) {
                
                foreach ($response->getHeaders() as $name => $values) {
                    $name = str_replace('_', '-', $name);
                    $name = ucwords(strtolower($name), '-');
                    $cookie = $name !== 'Set-Cookie';
                    foreach ($values as $value) {
                        header(sprintf('%s: %s', $name, $value), $cookie);
                        $cookie = false;
                    
                    }
                }

                header(sprintf(
                    'HTTP/%s %s %s',
                    $response->getProtocolVersion(),
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                ), true, $response->getStatusCode());

            }
        }

        /**
         * Writes body.
         *
         * @param ResponseInterface $response
         * @return ResponseInterface
         */
        private function body(ResponseInterface $response): ResponseInterface
        {
            if (!in_array($response->getStatusCode(), $this->responseIsEmpty)) {
                
                $stream = $response->getBody();
                if ($stream->isSeekable()) {
                    $stream->rewind();
                }

                $bufferLenght = (!$response->getHeaderLine('Content-Length')) ? $stream->getSize() : $response->getHeaderLine('Content-Length');
                if (isset($bufferLenght)) {
                    $lengthToRead = $bufferLenght;
                    while ($lengthToRead > 0 && !$stream->eof()) {
                        $data = $stream->read(min($this->sizeLimit, $lengthToRead));
                        echo $data;
                        $lengthToRead -= strlen($data);
                    }
                } else {
                    while (!$stream->eof()) {
                        echo $stream->read($this->sizeLimit);
                    }
                }

            }
            return $response;
        }

        /**
         * Emits response.
         *
         * @param ResponseInterface $response
         * @return mixed
         */
        public function emit(ResponseInterface $response)
        {
            $this->headers($response);
            $this->body($response);
        }
    }