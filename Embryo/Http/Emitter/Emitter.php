<?php 

    namespace Embryo\Http\Emitter;

    use Psr\Http\Message\ResponseInterface;

    class Emitter implements InterfaceEmitter
    {
        /**
         * @var array $empty
         */
        private $empty = [204, 205, 304];

        /**
         * @var int
         */
        private $size = 4096;

        /**
         * Sends headers, protocol, status code and reason
         * phrase from response.
         *
         * @param ResponseInterface $response
         * @return void
         */
        private function headers(ResponseInterface $response): void
        {
            if (!headers_sent()) {
                foreach ($response->getHeaders() as $name => $values) {
                    foreach ($values as $value) {
                        header(sprintf('%s: %s', $name, $value), $first);
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
         * Write body.
         *
         * @param ResponseInterface $response
         * @return ResponseInterface
         */
        private function body(ResponseInterface $response): ResponseInterface
        {
            if (!in_array($response->getStatusCode(), $empty)) {
                
                $body = $response->getBody();
                if ($body->isSeekable()) {
                    $body->rewind();
                }

                $contentLength = (!$response->getHeaderLine('Content-Length')) ? $body->getSize() : $response->getHeaderLine('Content-Length');
                if (isset($contentLength)) {
                    $lengthToRead = $contentLength;
                    while ($lengthToRead > 0 && !$body->eof()) {
                        $data = $body->read(min($this->size, $lengthToRead));
                        echo $data;
                        $lengthToRead -= strlen($data);
                        if (connection_status() != CONNECTION_NORMAL) {
                            break;
                        }
                    }
                } else {
                    while (!$body->eof()) {
                        echo $body->read($this->size);
                        if (connection_status() != CONNECTION_NORMAL) {
                            break;
                        }
                    }
                }

            }
            return $response;
        }

        /**
         * Emit response.
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