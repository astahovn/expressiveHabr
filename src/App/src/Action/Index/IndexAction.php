<?php

namespace App\Action\Index;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Interop\Container\ContainerInterface;
use App\Action\BaseAction;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Diactoros\Response\RedirectResponse;

class IndexAction extends BaseAction implements ServerMiddlewareInterface
{
    private $authAdapter;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->authAdapter = $container->get(CallbackCheckAdapter::class);
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $method = $request->getMethod();
        $params = $request->getParsedBody();
        $error = false;
        if ('POST' === $method) {
            $error = (empty($params['login']) || empty($params['password']));

            if (!$error) {
                $this->authAdapter
                    ->setIdentity($params['login'])
                    ->setCredential($params['password']);

                $result = $this->auth->authenticate($this->authAdapter);
                if ($result->isValid()) {
                    return new RedirectResponse('/');
                }
                $error = true;
            }
        }

        $tplData = [
            'error' => $error,
            'login' => $params['login'] ?? ''
        ];

        return new HtmlResponse($this->template->render('app-index::index-page', $tplData));
    }
}
