<?php


namespace App\Application\Actions\User;


use App\Domain\DomainException\DomainRecordNotFoundException;
use app\models\User as ModelsUser;
use Cycle\ORM\Transaction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Throwable;

class SaveAction extends \App\Application\Actions\Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        $user = new ModelsUser();
        $user->setName($data['name']);
        $user->email = $data['email'];

        $tr = new Transaction($this->container->get('orm'));
        $tr->persist($user);
        try {
            $tr->run();
            $this->response->getBody()->write('User Registered Successfully!');
        } catch (Throwable $e) {
            $data = "<pre>".print_r($e, true)."</pre>";
            $this->response->getBody()->write($data);
        }

        return $this->response;
    }
}