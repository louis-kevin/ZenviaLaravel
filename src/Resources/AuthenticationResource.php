<?php
/**
 * Created by PhpStorm.
 * User: DevMaker BackEnd
 * Date: 01/06/2018
 * Time: 11:06
 */

namespace Louisk\Zenvia\Resources;


use Zenvia\Exceptions\AuthenticationNotFoundedException;

class AuthenticationResource
{
    /**
     * @var string
     */
    private $account;
    /**
     * @var string
     */
    private $password;

    /**
     * AuthenticationResource constructor.
     * @param string $account
     * @param string $password
     * @throws AuthenticationNotFoundedException
     */
    public function __construct(string $account, string $password)
    {
        if(blank($account) || blank($password)){
            throw new AuthenticationNotFoundedException();
        }
        $this->account = $account;
        $this->password = $password;
    }

    public function getKey()
    {
        return base64_encode($this->account.':'.$this->password);
    }
}
