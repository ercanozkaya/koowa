<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Csrf Dispatcher Authenticator
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Dispatcher
 */
class KDispatcherAuthenticatorCsrf extends KDispatcherAuthenticatorAbstract
{
    /**
     * The CSRF token
     *
     * @var string
     */
    protected $_token;

    /**
     * Return the CSRF request token
     *
     * @return  string  The CSRF token or NULL if no token could be found
     */
    public function getToken()
    {
        if(!isset($this->token))
        {
            $token   = false;
            $request = $this->getObject('request');

            if($request->headers->has('X-XSRF-Token')) {
                $token = $request->headers->get('X-XSRF-Token');
            }

            if($request->headers->has('X-CSRF-Token')) {
                $token = $request->headers->get('X-CSRF-Token');
            }

            if($request->data->has('csrf_token')) {
                $token = $request->data->get('csrf_token', 'sha1');
            }

            $this->_token = $token;
        }

        return $this->_token;
    }

    /**
     * Verify the request to prevent CSRF exploits
     *
     * Method will always perform a referrer check and a cookie token check if the user is not authentic and
     * additionally a session token check if the user is authentic.
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     *
     * @throws KControllerExceptionRequestInvalid      If the request referrer is not valid
     * @throws KControllerExceptionRequestForbidden    If the cookie token is not valid
     * @throws KControllerExceptionRequestNotAuthenticated If the session token is not valid
     * @return  boolean Returns FALSE if the check failed. Otherwise TRUE.
     */
    protected function _beforePost(KDispatcherContextInterface $context)
    {
        $request = $context->request;
        $user    = $context->user;

        //Check referrer
        if(!$request->getReferrer()) {
            throw new KControllerExceptionRequestInvalid('Request Referrer Not Found');
        }

        //Check csrf token
        if(!$this->getToken()) {
            throw new KControllerExceptionRequestNotAuthenticated('Csrf Token Not Found');
        }

        //Check cookie token
        if($this->getToken() !== $request->cookies->get('csrf_token', 'sha1')) {
            throw new KControllerExceptionRequestNotAuthenticated('Invalid Cookie Token');
        }

        if($user->isAuthentic())
        {
            //Check session token
            if( $this->getToken() !== $user->getSession()->getToken()) {
                throw new KControllerExceptionRequestForbidden('Invalid Session Token');
            }
        }

        return true;
    }

    /**
     * Sign the response with a session token
     *
     * @param KDispatcherContextInterface $context	A dispatcher context object
     */
    protected function _afterGet(KDispatcherContextInterface $context)
    {
        if(!$context->response->isError())
        {
            $token = $context->user->getSession()->getToken();

            $context->response->headers->addCookie($this->getObject('lib:http.cookie', array(
                'name'   => 'csrf_token',
                'value'  => $token,
                'path'   => $context->request->getBaseUrl()->getPath(),
            )));

            $context->response->headers->set('X-CSRF-Token', $token);
        }
    }
}