<?php

namespace Eardish\EphectSandbox\Bcrypt;


class Bcrypt {
    public function __construct($username, $password)
    {
        $user = new User();
        $user->setUser($username);
        $this->data['user'] = $user;
        $pass = new Pass();
        $pass->setPassword($password);
        $this->data['pass'] = $pass;

    }
    public function setBcrypt($password)
    {
        $options = [
            'cost' => 10,
//            not sure about int size for salt
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];

        $bcrypt = password_hash($password, PASSWORD_DEFAULT, $options);
    }
    public function verifyBcrypt($password)
    {
//       TODO need to retrieve correct hashed password value
        $hash = $databaseValue;

        if (password_verify($password, $hash)) {
            echo 'Password is valid!';
        } else {
            echo 'Invalid password.';
        }
    }
    public function needsHash($password, $hash)
    {
        if (password_verify($password, $hash)) {
            if (password_needs_rehash($hash, PASSWORD_DEFAULT, $options)) {
                $hash = password_hash($password, PASSWORD_DEFAULT, $options);
                /* Store new hash in db */
            }
        }
    }
}

