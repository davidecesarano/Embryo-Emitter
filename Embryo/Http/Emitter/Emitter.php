<?php 

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
                    $cookie = stripos($name, 'Set-Cookie') === 0 ? false : true;
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
                        echo $stream->read($this->size);
                    }
                }

            }
            return $response;
        }

        /**
         * Emits response.
         *
         * @param ResponseInterface $response
         * @return void
         */
        public function emit(ResponseInterface $response)
        {
            $this->headers($response);
            $this->body($response);
        }
    }