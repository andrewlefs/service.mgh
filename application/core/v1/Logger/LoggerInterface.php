<?php

namespace MigEvents\Logger;

use MigEvents\Http\RequestInterface;
use MigEvents\Http\ResponseInterface;
use MigEvents\Http\ReceiverInterface;

interface LoggerInterface {

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array());

    /**
     * @param string $level
     * @param RequestInterface $request
     * @param array $context
     */
    public function logRequest(
    $level, RequestInterface $request, array $context = array());

    /**
     * @param string $level
     * @param ResponseInterface $response
     * @param array $context
     */
    public function logResponse(
    $level, ResponseInterface $response, array $context = array());

    /**
     * @param string $level
     * @param ResponseInterface $response
     * @param array $context
     */
    public function logFullRequest(
    $level, RequestInterface $resuest, ResponseInterface $response, array $context = array());

    /**
     * 
     * @param type $level
     * @param \MigEvents\Logger\ReceiverInterface $receiver
     * @param array $context
     */
    public function captureReceiver(
    $level, ReceiverInterface $receiver, array $context = array());
}
