<?php 

    /**
     * EmitterInterface
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-emitter
     */

    namespace Embryo\Http\Emitter;

    use Psr\Http\Message\ResponseInterface;

    interface EmitterInterface
    {
        /**
         * @param ResponseInterface $response 
         * @return mixed
         */
        public function emit(ResponseInterface $response);
    }