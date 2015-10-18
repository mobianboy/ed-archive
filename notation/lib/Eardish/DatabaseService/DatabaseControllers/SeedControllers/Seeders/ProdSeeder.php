<?php
/**
 * Created by PhpStorm.
 * User: darias
 * Date: 5/18/15
 * Time: 3:44 PM
 */

namespace Eardish\DatabaseService\DatabaseControllers\SeedControllers\Seeders;

class ProdSeeder extends PostgresSeeder {

    public function seed() {
        $this->genre();
        $this->badge();
        $this->setUpArt();
    }

    public function setUpArt()
    {
        $dateCreated = new \DateTime();
        $dateCreated = $dateCreated->format('c');

        $this->database->insertSeed(
            'user',
            array(
                'email' => 'systems@eardish.com',
                'password' => 'edsystemsadmin',
                'date_created' => $dateCreated
            )
        );

        $this->database->insertSeed(
            'profile',
            array(
                'user_id' => 1,
                'first_name' => 'eardish',
                'last_name' => 'admin',
                'type' => 'fan',
                'date_created' => $dateCreated
            )
        );

        $this->database->insertSeed(
          'art',
            array(
                'profile_id' => 1,
                'type' => 'avatar',
                'date_created' => $dateCreated
            )
        );
    }

}