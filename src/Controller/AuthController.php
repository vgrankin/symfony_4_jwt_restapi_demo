<?php


namespace App\Controller;


use App\Entity\User;
use App\Service\ResponseErrorDecoratorService;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController
{

    /**
     * Authenticate user by given credentials
     *
     * @Route("/api/authenticate")
     * @Method("POST")
     * @param Request $request
     * @param UserService $userService
     * @param ResponseErrorDecoratorService $errorDecorator
     * @return JsonResponse
     */
    public function issueJWTToken(
        Request $request,
        UserService $userService,
        ResponseErrorDecoratorService $errorDecorator
    )
    {
        $email = $request->getUser();
        $result = $userService->getUser($email);
        if ($result instanceof User) {
            $status = JsonResponse::HTTP_OK;
            $data = [
                'data' => [
                    'token' => 'TOKEN'
                ]
            ];
        } else {
            $status = JsonResponse::HTTP_BAD_REQUEST;
            $data = $errorDecorator->decorateError($status, $result);
        }

        return new JsonResponse($data);
    }
}