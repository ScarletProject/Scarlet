<?php
require_once 'PHPUnit/Framework.php';

/** 
* Short Description
*
* Long Description
* @package GridTestCase extends 
* @author Matt Mueller
*/

class GridTestCase extends PHPUnit_Framework_TestCase
{
	public function testNormalGrid()
    {
        $grid = array('width'=>'50px', 'gutter'=>'20px', 'columns' => '9');

		$this->assertEquals( '60px', $grid['width'], 'Normal grid should have width - 50px' );
    }

    public function testSmallGrid()
    {
		$this->markTestSkipped(
			'Who wants a small grid??'
        );
    }

    public function testLargeGrid()
    {
	    $this->markTestIncomplete(
          'Have not implemented large grid yet.'
        );
    }

    public function testHugeGrid()
    {
		print_r(array('waka' => 'waka', 'wahots','whats', 'going', 'on'));
		$this->assertTrue( true, 'Huge Grid Succeeded' );
    }
}


?>