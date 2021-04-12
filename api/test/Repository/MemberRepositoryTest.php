<?php

/**
 * EventRepositoryTest
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer\Model
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */

/**
 * GameConSuite Admin API
 *
 * This is the administrative API for GameConSuite.  You can find out more about Game Con Suite at  [https://gameconsuite.org](https://gameconsuite.org)
 * The version of the OpenAPI document: 1.0.0
 * Generated by: https://github.com/openapitools/openapi-generator.git
 */

namespace OpenAPIServer\Repository;

use PHPUnit\Framework\TestCase;
use OpenAPIServer\Model\ApiResponse;
use OpenAPIServer\Model\Member;
use OpenAPIServer\Model\MemberPrivate;

/**
 * EventRepositoryTest Class Doc Comment
 *
 * @package OpenAPIServer\Repository
 * @author  lhamel
 * @link    https://github.com/openapitools/openapi-generator
 *
 * @coversDefaultClass \OpenAPIServer\Repository\
 */
class MemberRepositoryTest extends TestCase
{
    var $db;
    var $repo;


    /**
     * Setup before running any test cases
     */
    public static function setUpBeforeClass() : void
    {
        include(__DIR__.'/../../../config/config.php');
        $GLOBALS['config'] = $config;
        require_once(__DIR__.'/../../../vendor/adodb/adodb-php/adodb.inc.php');
    }

    /**
     * Setup before running each test case
     */
    public function setUp() : void
    {


// Create connection
$conn = new mysqli('localhost', 'root', 'root');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


        $this->db = \NewADOConnection('mysql://root:root@localhost/ucon_test');
        $this->db->SetFetchMode(ADODB_FETCH_ASSOC);


        // load the database with test data
        // If strict types are enabled i.e. declare(strict_types=1);
        $file = file_get_contents(dirname(__FILE__).'/../../../dev_scripts/test-db.sql', true);
        $cmds = explode(';', $file);
        foreach ($cmds as $cmd) {
            if (substr(trim($cmd), 0, 12) == "CREATE TABLE") {
                // echo trim($cmd)."\n\n";
                $sql = trim($cmd);
                $this->db->execute($sql);
            } else {
                // echo substr(trim($cmd), 0, 6)."\n";
            }
        }
        $ok = $this->db->execute("alter table ucon_member add column s_group varchar(255)");
        if ($ok === false) {
            // echo "SQL Error: ".$this->db->ErrorMsg(); exit;
        }

        $this->repo = new MemberRepository($this->db);
    }

    /**
     * Clean up after running each test case
     */
    public function tearDown() : void
    {
    }

    /**
     * Clean up after running all test cases
     */
    public static function tearDownAfterClass() : void
    {
    }


    public function testPersistUpdateDelete() : void
    {
        $sampleId = 1001;

        // insert member
        $data = ['id'=>$sampleId, 
                'firstName' => "FirstName",
                'lastName' => "LastName",
                'groupName' => "Matinee Adventurers",
                'addr1' => "123 Fake St",
                'city' => 'Ann Arbor',
                'state' => 'MI',
                'zip' => '44444',
                'email' => 'me@me.com',
                'phone' => '555-555-5555',
            ];
        $m1 = MemberPrivate::createFromData($data);

        // clear out old data
        $this->repo->deleteMember($m1);

        // insert the member
        $this->repo->persistMember($m1);

        // retrieve member
        $r = $this->repo->findById($sampleId);
        $this->assertEquals("FirstName", $r->firstName);
        $this->assertEquals("LastName", $r->lastName);
        $this->assertEquals("Matinee Adventurers", $r->groupName);
        $this->assertEquals("123 Fake St", $r->addr1);
        $this->assertEquals('Ann Arbor', $r->city);
        $this->assertEquals('MI', $r->state);
        $this->assertEquals('44444', $r->zip);
        $this->assertEquals('me@me.com', $r->email);
        $this->assertEquals('555-555-5555', $r->phone);

        // update member
        foreach($data as $k=>$v) {
            if ($k != 'id') {
                $data[$k] = $v."-";
            }
        }
        $m2 = MemberPrivate::createFromData($data);
        $this->repo->persistMember($m2);

        // retrieve member
        $r = $this->repo->findById($sampleId);
        $this->assertEquals("FirstName-", $r->firstName);
        $this->assertEquals("LastName-", $r->lastName);
        $this->assertEquals("Matinee Adventurers-", $r->groupName);
        $this->assertEquals("123 Fake St-", $r->addr1);
        $this->assertEquals('Ann Arbor-', $r->city);
        $this->assertEquals('MI-', $r->state);
        $this->assertEquals('44444-', $r->zip);
        $this->assertEquals('me@me.com-', $r->email);
        $this->assertEquals('555-555-5555-', $r->phone);

        // test deletion
        $this->repo->deleteMember($m1);
        $r = $this->repo->findById($sampleId);
        $this->assertNull($r);
    }


}
