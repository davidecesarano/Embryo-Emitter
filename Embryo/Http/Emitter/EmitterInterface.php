<?php 

    namespace Embryo\Http\Emitter;

    use Psr\Http\Message\ResponseInterface;

    interface EmitterInterface
    {
        public function emit(ResponseInterface $response);
    }