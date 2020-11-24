<?php


namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use app\models\User as ModelsUser;
use Cycle\ORM\Transaction;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

class SaveAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        $user = new ModelsUser($this->container);
        $user->setName($data['name']);
        $user->email = $data['email'];

        try {
            $user->save();
            $this->response->getBody()->write('User Registered Successfully!');
        } catch (Throwable $e) {
            $data = "<pre>".print_r($e, true)."</pre>";
            $this->response->getBody()->write($data);
        }

        return $this->response;
    }
}