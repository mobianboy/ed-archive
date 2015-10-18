<?php
namespace Eardish\Gateway\Agents;

use Eardish\Gateway\Agents\Core\AbstractAgent;

class AuthAgent extends AbstractAgent
{
    protected $addr;
    protected $port;

    public function __construct($connection, $agentConfig)
    {
        parent::__construct($connection, $agentConfig, "auth");
    }

    public function authenticate($email, $password, $requestId)
    {
        // Second argument here is priority.
        $data = $this->arrayGenerator(__FUNCTION__, 10, $requestId);
        $data['params'] = array(
            'email' => $email,
            'password' => $password
        );

        $this->send(json_encode($data));
    }
}
