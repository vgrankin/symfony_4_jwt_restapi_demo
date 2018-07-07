<?php


namespace App\Service;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthService
{
    private $jwtSecretKey;
    private $requestStack;

    public function __construct(RequestStack $requestStack, $jwtSecretKey)
    {
        $this->jwtSecretKey = $jwtSecretKey;
        $this->requestStack = $requestStack;
    }

    const JWT_ALG = 'HS256';
    const SECONDS_VALID = 60 * 60;

    /**
     * Generate JWT token based on given user-data
     *
     * @param array $userData Array which contains relevant user-data to include into JWT payload
     * @return string JWT token required to gain access to restricted rest api methods
     */
    public function authenticate(array $userData)
    {
        return $this->_generateJWT($userData);
    }

    /**
     * Generate JWT token based on given user-data
     *
     * @param array $userData
     * @return string
     */
    private function _generateJWT(array $userData)
    {
        $issuedAt = time();
        $secondsValid = self::SECONDS_VALID;
        $expirationTime = $issuedAt + $secondsValid; // jwt valid for $secondsValid seconds from the issued time
        $payload = array(
            'sub' => $userData['email'],
            'email' => $userData['email'],
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );
        $key = $this->jwtSecretKey;
        $alg = self::JWT_ALG;
        $jwt = JWT::encode($payload, $key, $alg);

        return $jwt;
    }

    /**
     * Check if request is authenticated
     *
     * @return boolean true if is authenticated, false otherwise
     */
    public function isAuthenticated()
    {
        $token = $this->_getBearerToken();

        $decoded_array = $this->_validateJWT($token);
        if (!empty($decoded_array)) {
            // process valid token
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get decoded authentication token for the currently authenticated user
     *
     * @return mixed array of decoded jwt claims or false
     */
    public function getDecodedAuthToken()
    {
        $token = $this->_getBearerToken();
        $decoded_array = $this->_validateJWT($token);
        print_r($decoded_array);
        die();
        if (!empty($decoded_array)) {
            return $decoded_array;
        } else {
            return false;
        }
    }

    /**
     * Get access token from header
     *
     * @return authorization request header information (if exists) or null
     */
    private function _getBearerToken()
    {
        $request = $this->requestStack->getCurrentRequest();
        $authHeader = $request->headers->get('Authorization');
        if (strpos($authHeader, "Bearer ") !== false) {
            $token = explode(" ", $authHeader);
            if (isset($token[1])) {
                return $token[1]; // actual token
            }
        }

        return null;
    }

    /**
     * Check if jwt token is valid
     *
     * @param string $token
     * @return array decoded array if is-valid-wt, null otherwise
     */
    private function _validateJWT($token)
    {
        try {
            $key = $this->jwtSecretKey;
            JWT::$leeway = 60; // $leeway in seconds
            $decoded = JWT::decode($token, $key, array(self::JWT_ALG));
            $decoded_array = (array)$decoded;
        } catch (\Exception $e) {
            $decoded_array = null;
        }

        return $decoded_array;
    }
}