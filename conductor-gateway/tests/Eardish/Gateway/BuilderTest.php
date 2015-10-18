<?php
namespace Eardish\Gateway;

use Eardish\DataObjects\Response;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Monolog\Logger;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var \Eardish\DataObjects\Response
     */
    protected $responseObject;

    public function setUp()
    {

        $data = new DataBlock(array(
                'profile' => array('name' => 'Bonjovi', 'location' => 'Sayreville, New Jersey', 'genre' => 'rock'),
                'albums' => array('id' => 524, 'name' => "Bon Jovi")
            ));
        $meta = new MetaBlock();
        $meta->setResponseToken(36);
        $meta->setConnId(4);

        $this->builder = new Builder();
        $this->responseObject = new Response(array($data, $meta));
    }

    public function testBuildResponder1()
    {
        $ab = new AuditBlock();
        try {
            throw new \Exception("test",21);
        }
        catch(\Exception $ex) {
            $ab->addException($ex);
        }
        $builder = new Builder();
        $builder->handleException($ab);

        $this->assertTrue($ab->hasExceptions());

//
//        $response["status"] = ["code" => 2];
//        $response["data"] = ["stuff" => "someStuff"];
//        $response["meta"] = ["responseToken" => 100];
//
//
//        var_dump($builder->buildResponder($response));
//
//        unset($response);
//
//        $dataBlock = new DataBlock(["artId" => 10, "name" => "Cesar Flores"]);
//        $metaBlock = new MetaBlock();
//        $metaBlock->setResponseToken(10);
//        $response = new Response([$dataBlock, $metaBlock]);
//
//        var_dump($builder->buildResponder($response));
////
//        $message = ["from" => "system", "type" => "toast", "content" => "somebody logged in successfully", "destination" => "none"];
//
//        var_dump($builder->prepareMessageResponder($message, 1));

    }

 /*
    public function testBuildOutput()
    {
        $result = '{"data":{"profile":{"name":"Bonjovi","location":"Sayreville, New Jersey","genre":"rock"},"albums":{"id":524,"name":"Bon Jovi"}},"meta":{"responseToken":36}}'
        ;

        $this->assertEquals(
            $result,
            json_encode($this->builder->buildOutput($this->responseObject))
        );

//        // test data creation
//        $str1 = '{"status":{"code":32,"message":"A third-party service refused the service call and the reason is described."},"data":{"model":"loggedInUser","raw":{"name":"TestName","path":"artists"},"action":"null","communicationType":"[requested|update]"}}';
//
//        $this->assertEquals(
//            '"{\n\"status\": {\"code\":10,\"message\":\"No action could be found for the request.\"},\n\"data\": {\"model\":\"loggedInUser\",\"raw\":{\"name\":\"TestName\",\"path\":\"artists\"},\"action\":\"null\",\"communicationType\":\"[requested|update]\"}\n}"',
//            json_encode($data->buildOutput($x, $str1))
//        );
//
//        // test null creation
//        $str1 = '';
//
//        $this->assertEquals(
//            '"{\n\"status\": {\"code\":10,\"message\":\"No action could be found for the request.\"}\n}"',
//            json_encode($data->buildOutput($x, $str1))
//        );
    }
*/
}
