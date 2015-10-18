<?php
//namespace Eardish\DataObjects\Blocks;
//
//class AuditBlockTest extends \PHPUnit_Framework_TestCase
//{
//    /**
//     * @var AuditBlock
//     */
//    protected $auditBlock;
//
//    public function setUp()
//    {
//        $this->auditBlock = new AuditBlock();
//        $this->auditBlock->addException(new \Exception("failed"));
//    }
//
//    public function testGetAddException()
//    {
//        $this->auditBlock->addException(new \OutOfBoundsException("Out of bounds"));
//        $entries = $this->auditBlock->getLog();
//
//        $this->assertEquals(
//            'failed',
//            $entries[0]['message']
//        );
//
//        $this->assertEquals(
//            'OutOfBoundsException',
//            $entries[1]['type']
//        );
//    }
//
//    public function testAddNotice()
//    {
//        $this->auditBlock->addNotice('User is not logged in.');
//        $this->assertEquals(
//            'User is not logged in.',
//            $this->auditBlock->getLog()[1]['message']
//        );
//    }
//
//    public function testToString()
//    {
//        $this->auditBlock->addNotice('User not logged in');
//        $msg =  $this->auditBlock;
//
//        $this->expectOutputString('-Exception thrown: failed with code: 0'.PHP_EOL.'-User not logged in');
//
//        print $msg;
//    }
//}
