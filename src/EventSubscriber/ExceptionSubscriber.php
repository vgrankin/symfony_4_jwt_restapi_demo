<?php


namespace App\EventSubscriber;

use App\Service\ResponseErrorDecoratorService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExceptionSubscriber
 *
 * https://symfony.com/doc/current/event_dispatcher.html
 *
 * @package App\EventSubscriber
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    private $errorDecorator;

    public function __construct(ResponseErrorDecoratorService $errorDecorator)
    {
        $this->errorDecorator = $errorDecorator;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            KernelEvents::EXCEPTION => array(
                array('processException', 100)
            )
        );
    }

    /**
     * Hijacks exceptions and turns them into REST API compatible error responses
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function processException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        $response = new JsonResponse();

        $message = null;


        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
            if ($exception instanceof AccessDeniedHttpException) {
                $message = $exception->getMessage();
            }
        } else {
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        if (!$message) {
            $message = isset(JsonResponse::$statusTexts[$status])
                ? JsonResponse::$statusTexts[$status] : "Unknown error";
        }

        $data = $this->errorDecorator->decorateError($status, $message);

        // Customize your response object to display the exception details
        $response->setData($data);
        $response->setStatusCode($status);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}