<?php
require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../tools/LibraryCreator.php';
require_once dirname(__FILE__).'/../../Scarlet.php';
require_once dirname(__FILE__).'/../tools/phpQuery.php';

/**
 * Test class for Tag.
 * Generated by PHPUnit on 2010-06-19 at 09:18:19.
 */
class TagTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tag
     */
    protected $object;
	private $creator_pool;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
	private function data() {
		return array(
			// library path relative to this file
			'viva:la:vida' => '../test-library-0',
			'john:mayer' => '../test-library-0',
			'halo' => '../../test-library-1',
			'tribal:runner' => '../../test-library-1',
			'when:love:comes:to:town' => '../../../library-test-2',
			'when:love:comes' => '../../../library-test-2'
		);
	}

    protected function setUp() {
		$data = $this->data();
		
		foreach ($data as $namespace => $library) {
			$creator = creator($namespace, realpath(dirname(__FILE__).'/'.$library));
			$this->creator_pool[$namespace] = $creator;
			
			// Add stylesheets
			for ($i=0; $i < 10; $i++) { 
				$creator->attach("style$i.css");
			}
			for ($i=10; $i < 20; $i++) { 
				$creator->attach("stylesheets/style$i.css");
			}
		
			// Add javascript
			for ($i=0; $i < 10; $i++) { 
				$creator->attach("script$i.js");
			}
			for ($i=10; $i < 20; $i++) { 
				$creator->attach("scripts/script$i.js");
			}
		
			// Add images
			for ($i=0; $i < 10; $i++) { 
				$creator->attach("img$i.jpg");
			}
			for ($i=10; $i < 20; $i++) { 
				$creator->attach("images/img$i.jpg");
			}

			$creator->init();
			S()->library(realpath(dirname(__FILE__).'/'.$library));
		}
		
		$this->object = S('viva:la:vida');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
		foreach ($this->creator_pool as $creator) {
			$creator->cleanUp();
		}
		
		Tag::_clear_stylesheets();
		Tag::_clear_scripts();
		Tag::_clear_attachments();

    }

	public function testExtend() {
		$blarg = S('box');
		
		$john = S('box:rounded')->args(array('3px', 'width'=>'200', 'height'=>'200', 'background-color'=>'orange', 'border'=>'4px solid green'));

		// Extension
		$john->extend($blarg);

		// $john->defaults('rounded');

		$john->stylesheet('box:rounded:rounded.css');

		$john->attr('name', 'john mayer')
				->addClass('rounded');
		
		
		echo $john;

		$j = $this->rasterize($john);
	
		// $this->fail();
	}

    public function testStylesheet() {
		$tag = S('john:mayer');

		$arr = $tag->stylesheet();
		$this->assertTrue( is_array($arr), 'no params returns stylesheets array' );
		$this->assertTrue( empty($arr), 'stylesheet starts out empty');

		$return = $tag->stylesheet('style0.css');
		$arr = $tag->stylesheet();

		$this->assertContains($tag->location().'/style0.css', $arr, 'stylesheets contain style0.css' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->stylesheet('style1.css', 'style2.css');
		$arr = $tag->stylesheet();

		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($tag->location().'/style0.css', $arr, 'stylesheets contain style0.css' );
		$this->assertContains($tag->location().'/style1.css', $arr, 'stylesheets contain style2.css' );
		$this->assertContains($tag->location().'/style2.css', $arr, 'stylesheets contain style3.css' );

		// Took care of local stylesheets, now need to check stylesheets from other libraries		
		$return = $tag->stylesheet('halo:style4.css');
		$location = dirname(S()->find('halo'));
		$arr = $tag->stylesheet();
		$this->assertContains($location.'/style4.css', $arr, 'stylesheets contain style4.css' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->stylesheet('viva:la:vida:style5.css');
		$location = dirname(S()->find('viva:la:vida'));
		$arr = $tag->stylesheet();
		$this->assertContains($location.'/style5.css', $arr, 'stylesheets contain style5.css' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->stylesheet('tribal:runner:stylesheets/style12.css');
		$location = dirname(S()->find('tribal:runner'));
		$arr = $tag->stylesheet();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($location.'/stylesheets/style12.css', $arr, 'stylesheets contain style12.css' );

		// Now need to check for absolute paths
		$return = $tag->stylesheet('/Scarlet/test-library-1/tribal/runner/stylesheets/style13.css');
		$location = dirname(S()->find('tribal:runner'));
		$arr = $tag->stylesheet();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($location.'/stylesheets/style13.css', $arr, 'stylesheets contain style13.css' );

		$return = $tag->stylesheet('/Users/Matt/Sites/Scarlet/tests/test-library-0/viva/la/vida/stylesheets/style14.css');
		$location = dirname(S()->find('viva:la:vida'));
		$arr = $tag->stylesheet();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($location.'/stylesheets/style14.css', $arr, 'stylesheets contain style14.css' );

		// Allow for shortcuts that link to css file.
		$return = $tag->stylesheet('grid');
		$arr = $tag->stylesheet();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		// Defer responsibility till css can take care of it
		$this->assertContains('grid', $arr, 'scripts contain grid' );


	// Not necessary right now....
		// // Remove all of them
		// $tag->removeStylesheet($tag->)
		// 
		// // Take an array of stylesheets
		// $return = $tag->stylesheet(array('grid', '/Users/Matt/Sites/Scarlet/tests/test-library-0/viva/la/vida/stylesheets/style14.css', 'viva:la:vida:style5.css', 'tribal:runner:stylesheets/style12.css'));
		
    }

    public function testRemoveStylesheet() {

        $tag = S('viva:la:vida');

		$tag->stylesheet('style1.css', 'stylesheets/style12.css', 'john:mayer:style3.css');
		
		// Show that it does nothing.
		$return = $tag->removeStylesheet();
		$arr = $tag->stylesheet();
		
		$location = dirname(S()->find('viva:la:vida'));
		
		$this->assertContains( $location.'/style1.css', $arr, 'ignore out of bounds removal' );
		$this->assertContains( $location.'/stylesheets/style12.css', $arr, 'ignore out of bounds removal' );
		
		$location_mayer = dirname(S()->find('john:mayer'));
		$this->assertContains( $location_mayer.'/style3.css', $arr, 'ignore out of bounds removal' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		$return = $tag->removeStylesheet(0);
		$arr = $tag->stylesheet();
		$this->assertEquals( 2, count($arr), 'stylesheet array empty (1)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
        
		$return = $tag->removeStylesheet(1);
		$arr = $tag->stylesheet();
		$this->assertEquals( 1, count($arr), 'stylesheet array empty (13)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
        
		$return = $tag->removeStylesheet(0);
		$arr = $tag->stylesheet();
		$this->assertEquals( 0, count($arr), 'stylesheet array empty (12)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );


		$tag->stylesheet('style4.css', 'style5.css');
		// Remove stylesheets individually by name		
		$return = $tag->removeStylesheet('style4.css');
		$return = $tag->removeStylesheet('style5.css');
		
		$arr = $tag->stylesheet();
		$this->assertEquals( 0, count($arr), 'stylesheet array empty (4,5)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// Handle multiple removals by name
		$tag->stylesheet('style6.css', '/stylesheets/style16.css');
		$return = $tag->removeStylesheet('style6.css', 'stylesheets/style16.css');
		$arr = $tag->stylesheet();
		$this->assertEquals( 0, count($arr), 'stylesheet array empty (6, 16)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Using multiple indexes
		$tag->stylesheet('john:mayer:style8.css', 'viva:la:vida:style9.css');
		$return = $tag->removeStylesheet(0, 1);
		$arr = $tag->stylesheet();
		$this->assertEquals( 0, count($arr), 'stylesheet array empty (8, 9)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Handle when out of bounds or not in there.
		$return = $tag->removeStylesheet(2);
		$this->assertEquals( 0, count($arr), 'stylesheet array empty (OOB1)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		$tag->stylesheet('halo:/style7.css', 'john:mayer:/stylesheets/style19.css');
		$return = $tag->removeStylesheet(1,1);
		$arr = $tag->stylesheet();
		$this->assertEquals( 1, count($arr), 'stylesheet array empty (OOB2)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );	
		// Empty
		$tag->removeStylesheet(0);
		
		// Remove shortcutted stylesheet
		$tag->stylesheet('grid');
		$return = $tag->removeStylesheet('grid');
		$arr = $tag->stylesheet();
		$this->assertEquals( 0, count($arr), 'stylesheet array empty (grid)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
			
    }

    public function testScript() {
		$tag = S('when:love:comes:to:town');

		$arr = $tag->script();
		$this->assertTrue( is_array($arr), 'no params returns scripts array' );
		$this->assertTrue( empty($arr), 'script starts out empty');

		$return = $tag->script('script0.js');
		$arr = $tag->script();

		$this->assertContains($tag->location().'/script0.js', $arr, 'scripts contain script0.js' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->script('script1.js', '/script2.js');
		$arr = $tag->script();

		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($tag->location().'/script0.js', $arr, 'scripts contain script0.js' );
		$this->assertContains($tag->location().'/script1.js', $arr, 'scripts contain script2.js' );
		$this->assertContains($tag->location().'/script2.js', $arr, 'scripts contain script3.js' );

		// Took care of local scripts, now need to check scripts from other libraries		
		$return = $tag->script('halo://script4.js');
		$location = S()->location('halo');
		$arr = $tag->script();
		$this->assertContains($location.'/script4.js', $arr, 'scripts contain script4.js' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->script('viva:la:vida:script5.js');
		$location = S()->location('viva:la:vida');
		$arr = $tag->script();
		$this->assertContains($location.'/script5.js', $arr, 'scripts contain script5.js' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->script('tribal:runner:/scripts/script12.js');
		$location = S()->location('tribal:runner');
		$arr = $tag->script();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($location.'/scripts/script12.js', $arr, 'scripts contain script12.js' );

		// Now need to check for absolute paths
		$return = $tag->script('Scarlet/test-library-1/tribal/runner/scripts/script13.js');
		$location = S()->location('tribal:runner');
		$arr = $tag->script();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($location.'/scripts/script13.js', $arr, 'scripts contain script13.js' );

		$return = $tag->script('Users/Matt/Sites/Scarlet/tests/test-library-0/viva/la/vida/scripts/script14.js');
		$location = S()->location('viva:la:vida');
		$arr = $tag->script();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		$this->assertContains($location.'/scripts/script14.js', $arr, 'scripts contain script14.js' );

		// Allow for shortcuts that link to javascript file.
		$return = $tag->script('jquery');
		$arr = $tag->script();
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		// Defer responsibility till javascript can take care of it
		$this->assertContains('jquery', $arr, 'scripts contain jquery' );
		
    }

    public function testRemoveScript() {
		$tag = S('when:love:comes');

		$tag->script('script1.js', 'scripts/script12.js', 'when:love:comes:to:town:script3.js');

		// Show that it does nothing.
		$return = $tag->removeScript();
		$arr = $tag->script();

		$location = dirname(S()->find('when:love:comes'));

		$this->assertContains( $location.'/script1.js', $arr, 'ignore out of bounds removal' );
		$this->assertContains( $location.'/scripts/script12.js', $arr, 'ignore out of bounds removal' );

		$location2 = dirname(S()->find('when:love:comes:to:town'));
		$this->assertContains( $location2.'/script3.js', $arr, 'ignore out of bounds removal' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->removeScript(0);
		$arr = $tag->script();
		$this->assertEquals( 2, count($arr), 'script array empty (1)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->removeScript(1);
		$arr = $tag->script();
		$this->assertEquals( 1, count($arr), 'script array empty (13)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->removeScript(0);
		$arr = $tag->script();
		$this->assertEquals( 0, count($arr), 'script array empty (12)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );


		$tag->script('script4.js', 'script5.js');
		// Remove scripts individually by name		
		$return = $tag->removeScript('script4.js');
		$return = $tag->removeScript('script5.js');

		$arr = $tag->script();
		$this->assertEquals( 0, count($arr), 'script array empty (4,5)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Handle multiple removals by name
		$tag->script('script6.js', '/scripts/script16.js');
		$return = $tag->removeScript('script6.js', 'scripts/script16.js');
		$arr = $tag->script();
		$this->assertEquals( 0, count($arr), 'script array empty (6, 16)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Using multiple indexes
		$tag->script('john:mayer:script8.js', 'viva:la:vida:script9.js');
		$return = $tag->removeScript(0, 1);
		$arr = $tag->script();
		$this->assertEquals( 0, count($arr), 'script array empty (8, 9)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Handle when out of bounds or not in there.
		$return = $tag->removeScript(2);
		$this->assertEquals( 0, count($arr), 'script array empty (OOB1)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$tag->script('halo:/script7.js', 'john:mayer:/scripts/script19.js');
		$return = $tag->removeScript(1,1);
		$arr = $tag->script();
		$this->assertEquals( 1, count($arr), 'script array empty (OOB2)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		// Empty
		$tag->removeScript(0);
		
		// Remove shortcutted javascript
		$tag->script('jquery');
		$return = $tag->removeScript('jquery');
		$arr = $tag->script();
		$this->assertEquals( 0, count($arr), 'script array empty (jquery)' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
    }

    public function testAttach() {
		$tag = S('halo');		
		
        $arr = $tag->attach();
		$this->assertTrue( is_array($arr), 'no params returns attachment array' );
		$this->assertTrue( empty($arr), 'attachments starts out empty');
		
		////////////////////////////////////////
		// SCARLET_ATTACHMENT_DIR not defined //
		////////////////////////////////////////
		
		// Since not defined - should gracefully exit attach right away
		$return = $tag->attach('script5', 'script5.js');
		$arr = $tag->attach();
		$this->assertTrue( is_array($arr), 'no params returns attachment array' );
		$this->assertTrue( empty($arr), 'attachments starts out empty');
        $this->assertSame( $tag, $return, 'tag returned from attach' );
		
		$return = $tag->attach('script5');
		$this->assertSame( '', $return, 'null script5 returns blank link' );
		
		// Adding content shouldn't do anything
		$return = $tag->attach('script7', '<script>alert("hi!");</script>', true);
		$arr = $tag->attach();
		$this->assertTrue( is_array($arr), 'no params returns attachment array' );
		$this->assertTrue( empty($arr), 'attachments starts out empty');
        $this->assertSame( $tag, $return, 'tag returned from attach' );
		
		////////////////////////////////////////
		//   SCARLET_ATTACHMENT_DIR defined   //
		////////////////////////////////////////

		define('SCARLET_ATTACHMENT_DIR', realpath(dirname(__FILE__).'/../scarlet_attachments'));
		$loc = '/Scarlet/tests/scarlet_attachments';
		
		// Add a script to the attachment directory
		$return = $tag->attach('script5.js', 'john:mayer:script5.js');
		$arr = $tag->attach();
		$this->assertArrayHasKey('script5.js', $arr, 'attachment has script5 key');
		$this->assertEquals($loc.'/script5.js', $arr['script5.js'], 'script5.js link correct');
		$this->assertFileExists( $_SERVER['DOCUMENT_ROOT'].$loc.'/script5.js', 'attachment dir has script5.js');
        $this->assertSame( $tag, $return, 'tag returned from attach' );
		
		// Get link from attachment directory
		$link = $tag->attach('script5.js');
		$this->assertEquals($loc.'/script5.js', $link, 'script5.js link correctly recieved');
		
		// Put content through
		$return = $tag->attach('journey.txt', 'this is the lyrics to don\'t stop believing..', true);
		$arr = $tag->attach();
		$this->assertArrayHasKey('journey.txt', $arr, 'attachment has journey.txt key');
		$this->assertEquals($loc.'/journey.txt', $tag->attach('journey.txt'), 'journey.txt link correct');
		$this->assertFileExists( $_SERVER['DOCUMENT_ROOT'].$loc.'/journey.txt', 'attachment dir has journey.txt');
        $this->assertSame( $tag, $return, 'tag returned from attach' );
		
    }

    public function testDetach() {
		$tag = S('when:love:comes');
		
		$loc = '/Scarlet/tests/scarlet_attachments';
		
		$arr = $tag->attach();
		$this->assertTrue( is_array($arr), 'no params returns attachment array' );
		$this->assertTrue( empty($arr), 'attachments starts out empty');
        
		$tag->attach('script14.js', 'halo:scripts/script14.js');
		$tag->attach('script7.js', 'when:love:comes:/script7.js');
		$tag->attach('style0.css', 'when:love:comes:to:town:style0.css');
		$tag->attach('img2.jpg', 'viva:la:vida:img2.jpg');
		$tag->attach('Whatcha say.txt', 'ohm whatchu say? What did she say...?', true);
		
		// Do nothing
		$return = $tag->detach();
		$arr = $tag->attach();
		$this->assertEquals( 5, count($arr), 'there are 5 attachments' );
		$this->assertSame( $tag, $return, 'tag returned from attach' );
        
		// Remove one script
		$return = $tag->detach('style0.css');
		$arr = $tag->attach();
		$this->assertEquals( 4, count($arr), 'there are 4 attachments' );
		$this->assertArrayNotHasKey( 'style0.css', $arr, 'removed style0.css key' );
		$this->assertFileNotExists( $loc.'/style0.css', 'removed style0.css from attachments');
		$this->assertSame( $tag, $return, 'tag returned from attach' );
		
		// Remove multiple scripts
		$return = $tag->detach('script7.js', 'Whatcha say.txt', 'script14.js');
		$arr = $tag->attach();
		$this->assertEquals( 1, count($arr), 'there is 1 attachment' );
		
		$this->assertArrayNotHasKey( 'script7.js', $arr, 'removed script7.js key' );
		$this->assertFileNotExists( $loc.'/style0.css', 'removed style0.css from attachments');
		
		$this->assertArrayNotHasKey( 'Whatcha say.txt', $arr, 'removed Whatcha say.txt key' );
		$this->assertFileNotExists( $loc.'/Whatcha say.txt', 'removed Whatcha say.txt from attachments');
		
		$this->assertArrayNotHasKey( 'script14.js', $arr, 'removed script14.js key' );
		$this->assertFileNotExists( $loc.'/script14.js', 'removed script14.js from attachments');
		
		$this->assertSame( $tag, $return, 'tag returned from attach' );
		
		
		// Try to detach something that doesn't exist
		$return = $tag->detach('lovestory.mp3');
		$arr = $tag->attach();
		$this->assertEquals( 1, count($arr), 'there is 1 attachment still' );
		$this->assertSame( $tag, $return, 'tag returned from attach' );
		
		
    }

	private function get_set_test(Tag $tag, $function) {
		// Add args
		try {
			$return = $tag->$function('k1', 'v1');
			$this->assertSame( $tag, $return, 'returns the tag object' );
			$return = $tag->$function('k2', 'v2');
			$this->assertSame( $tag, $return, 'returns the tag object' );
			$return = $tag->$function('k3', 'v3');
			$this->assertSame( $tag, $return, 'returns the tag object' );
			$tag->$function(0, 'v0');
		} catch (Exception $e) {
			$this->fail('Key / Value failed!');
		}
		
		try {
			$args = $tag->$function();
		} catch (Exception $e) {
			$this->fail('Getting array failed!');
		}
		
		// Make sure args exist
		$this->assertArrayHasKey( 'k1', $args, 'Has k1' );
		$this->assertArrayHasKey( 'k2', $args, 'Has k2' );
		$this->assertArrayHasKey( 'k3', $args, 'Has k3' );
		$this->assertArrayHasKey( 0, $args, 'Has 0' );
		
		// Make sure keys have specified value
		$this->assertEquals( 'v1', $args['k1'], '$args["k1"] == v1' );
		$this->assertEquals( 'v2', $args['k2'], '$args["k2"] == v2' );
		$this->assertEquals( 'v3', $args['k3'], '$args["k3"] == v3' );
		$this->assertEquals( 'v0', $args[0], '$args[0] == v0' );

		// If args has one argument and its a string use as a key
		$this->assertEquals( 'v1', $tag->$function('k1'), '$tag->args(\'k1\') == "v1"' );
		$this->assertEquals( 'v2', $tag->$function('k2'), '$tag->args(\'k2\') == "v2"' );
		$this->assertEquals( 'v3', $tag->$function('k3'), '$tag->args(\'k3\') == "v3"' );
		$this->assertEquals( 'v0', $tag->$function(0), '$tag->args(0) == "v0"' );
		
		// Allow mappings
		try {
			$return = $tag->$function(array('k4' => 'v4', 'k5' => 'v5', 'k6' => 'v6', 1 => 'v1'));
			$this->assertSame( $tag, $return, 'returns the tag object' );
		} catch(Exception $e) {
			$this->fail("Mapping Failed!");
		}

		$this->assertEquals( 'v4', $tag->$function('k4'), '$tag->args(\'k4\') == "v4"' );
		$this->assertEquals( 'v5', $tag->$function('k5'), '$tag->args(\'k5\') == "v5"' );
		$this->assertEquals( 'v6', $tag->$function('k6'), '$tag->args(\'k6\') == "v6"' );
		$this->assertEquals( 'v1', $tag->$function(1), '$tag->args(1) == "v1"' );
		
		// Allow for replacement
		$tag->$function('k10', 'v10');
		$this->assertArrayHasKey( 'k10', $tag->$function(), 'Has k10' );
		$this->assertEquals( 'v10', $tag->$function('k10'), '$tag->args(\'k10\') == "v10"' );
		
		$tag->$function('k10', 'v10_new');
		$this->assertArrayHasKey( 'k10', $tag->$function(), 'Has k10' );
		$this->assertEquals( 'v10_new', $tag->$function('k10'), '$tag->args(\'k10\') == "v10_new"' );
		
		$tag->$function('k20', 'v20');
		$tag->$function(array('k21'=>'v21', 'k22'=>'v22'));
		
		$this->assertArrayHasKey( 'k20', $tag->$function(), 'Has k20' );
		$this->assertArrayHasKey( 'k21', $tag->$function(), 'Has k21' );
		$this->assertArrayHasKey( 'k22', $tag->$function(), 'Has k22' );
		$this->assertEquals( 'v20', $tag->$function('k20'), '$tag->args(\'k20\') == "v20"' );
		$this->assertEquals( 'v21', $tag->$function('k21'), '$tag->args(\'k21\') == "v21"' );
		$this->assertEquals( 'v22', $tag->$function('k22'), '$tag->args(\'k22\') == "v22"' );
		
		$tag->$function(array('k20' => 'v20_new', 'k21'=>'v21_new', 'k22'=>'v22_new'));
		$this->assertEquals( 'v20_new', $tag->$function('k20'), '$tag->args(\'k20\') == "v20_new"' );
		$this->assertEquals( 'v21_new', $tag->$function('k21'), '$tag->args(\'k21\') == "v21_new"' );
		$this->assertEquals( 'v22_new', $tag->$function('k22'), '$tag->args(\'k22\') == "v22_new"' );
		

    }

    public function testAttr() {
		// Generic get_set_test.
        $this->get_set_test($this->object, 'attr');

		$tag = $this->object;

		$t = $this->rasterize($tag);
		
		$this->assertEquals( 'v20_new', $t->attr('k20'), 'tag has attr k20 = v20_new' );
		$this->assertEquals( 'v21_new', $t->attr('k21'), 'tag has attr k21 = v21_new' );
		$this->assertEquals( 'v22_new', $t->attr('k22'), 'tag has attr k22 = v22_new' );
		$this->assertEquals( 'v5', $t->attr('k5'),  'tag has attr k5 = v5'  );

		// Ensure that if the key doesn't exist it returns false
		$this->assertEquals("", $this->object->attr('k7'), 'k7 doesn\'t exist!' );
		$this->assertEquals("",  $this->object->attr(2), 'args(2) doesn\'t exist!' );		
			
    }

    public function testRemoveAttr() {
        $tag = $this->object;

		$tag->attr('k1', 'v1');
		$tag->attr(array('k2' => 'v2', 'k3' => 'v3', 'k4' => 'v4'));
		
		// Remove no keys
		$return = $tag->removeAttr();
		$arr = $tag->attr();
		$this->assertEquals( 4, count($arr), 'No keys removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// Remove one key
		$return = $tag->removeAttr('k2');
		$args = $tag->attr();
		$this->assertArrayNotHasKey('k2', $args, 'Does not have k2' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		$return = $tag->removeAttr('k4');
		$args = $tag->attr();
		$this->assertArrayNotHasKey('k4', $args, 'Does not have k4' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// Test if key doesn't exist
		$arr = $tag->attr();
		$return = $tag->removeAttr('k5');
		$this->assertEquals( count($arr), count($tag->attr()), 'No keys removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// Remove multiple keys
		$return = $tag->removeAttr('k2', 'k3', 'k1');
		$args = $tag->attr();
		$this->assertArrayNotHasKey('k2', $args, 'Does not have k2' );
		$this->assertArrayNotHasKey('k3', $args, 'Does not have k3' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// Test empty
		$arr = $tag->attr();
		$this->assertEquals(0, count($arr), 'attributes array empty');
		
		// Test as HTML
		$t = $this->rasterize($tag);
		
		$this->assertEquals( '', $t->attr('k3'), 'k3 attribute doesn\'t exist' );
		$this->assertEquals( '', $t->attr('k2'), 'k2 attribute doesn\'t exist' );
		$this->assertEquals( '', $t->attr('k1'), 'k1 attribute doesn\'t exist' );
		
    }

    public function testAddClass() {
        $str = $this->object->attr('class');
		$this->assertEquals('', $str , 'classes starts out empty');

		$tag = $this->object->addClass('class1');
		$arr = explode(" ", $this->object->attr('class'));
		
		$this->assertContains('class1', $arr, 'classes contain class1' );
		$this->assertSame( $this->object, $tag, 'returned value is a tag' );

		$tag = $this->object->addClass('class2', 'class3');
		$arr = explode(" ", $this->object->attr('class'));
		
		$this->assertSame( $this->object, $tag, 'returned value is a tag' );
		$this->assertContains( 'class1', $arr, 'classes contain class1' );
		$this->assertContains( 'class2', $arr, 'classes contain class2' );
		$this->assertContains( 'class3', $arr, 'classes contain class3' );
		
		// Test as HTML
		$t = $this->rasterize($this->object);
		
		$this->assertTrue( $t->hasClass('class1'), 'tag has class1' );
    	$this->assertTrue( $t->hasClass('class2'), 'tag has class2' );
		$this->assertTrue( $t->hasClass('class3'), 'tag has class3' );
		$this->assertFalse( $t->hasClass('class4'), 'tag doesn\'t have class4');
	}

    public function testRemoveClass() {
        $tag = $this->object;
		$tag->addClass('class1', 'class2', 'class3', 'class4', 'class5');
		$this->assertEquals( 5, count(explode(" ", $tag->attr('class'))), 'Start with 5 classes' );
		
		// No class
		$return = $tag->removeClass();
		$this->assertEquals( 5, count(explode(" ", $tag->attr('class'))), 'No classes removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// 1 class
		$return = $tag->removeClass('class2');
		$arr = explode(' ', $tag->attr('class'));
		$this->assertNotContains( 'class2', $arr, 'Removed class2' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		$return = $tag->removeClass('class5');
		$arr = explode(' ', $tag->attr('class'));
		$this->assertNotContains( 'class5', $arr, 'Removed class5' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// Multiple classes
		$return = $tag->removeClass('class1', 'class3', 'class4');
		$arr = explode(' ', $tag->attr('class'));
		$this->assertNotContains( 'class1', $arr, 'Removed class1' );
		$this->assertNotContains( 'class3', $arr, 'Removed class3' );
		$this->assertNotContains( 'class4', $arr, 'Removed class3' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );
		
		// No classes left
		$this->assertEquals( '', $tag->attr('class'), 'No classes left' );
		
		// Test as HTML
		$t = $this->rasterize($this->object);
		
		$this->assertFalse( $t->hasClass('class1'), 'tag doesn\'t have class1' );
    	$this->assertFalse( $t->hasClass('class2'), 'tag doesn\'t have class2' );
		$this->assertFalse( $t->hasClass('class3'), 'tag doesn\'t have class3' );
		$this->assertFalse( $t->hasClass('class4'), 'tag doesn\'t have class4');
		
    }

    public function testStyle() {
        // Generic get_set_test.
        $this->get_set_test($this->object, 'style');

		// Ensure that if the key doesn't exist it returns false
		$this->assertEquals("", $this->object->style('k7'), 'k7 doesn\'t exist!' );
		$this->assertEquals("",  $this->object->style(2), 'args(2) doesn\'t exist!' );
		
		// Test as HTML - Not yet implemented in phpQuery - looks okay rasterized though.
		// $t = $this->rasterize($this->object , true);
		// 		echo $t->css('k22');
    }

    public function testRemoveStyle() {
        $tag = $this->object;

		$tag->style('k1', 'v1');
		$tag->style(array('k2' => 'v2', 'k3' => 'v3', 'k4' => 'v4'));

		// Remove no keys
		$return = $tag->removeStyle();
		$arr = $tag->style();
		$this->assertEquals( 4, count($arr), 'No keys removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Remove one key
		$return = $tag->removeStyle('k2');
		$args = $tag->style();
		$this->assertArrayNotHasKey('k2', $args, 'Does not have k2' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->removeStyle('k4');
		$args = $tag->style();
		$this->assertArrayNotHasKey('k4', $args, 'Does not have k4' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Test if key doesn't exist
		$arr = $tag->style();
		$return = $tag->removeStyle('k5');
		$this->assertEquals( count($arr), count($tag->style()), 'No keys removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Remove multiple keys
		$return = $tag->removeStyle('k2', 'k3', 'k1');
		$args = $tag->style();
		$this->assertArrayNotHasKey('k2', $args, 'Does not have k2' );
		$this->assertArrayNotHasKey('k3', $args, 'Does not have k3' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Test empty
		$arr = $tag->style();
		$this->assertEquals(0, count($arr), 'styles array empty');
    }

    public function testWrap() {
       	$tag = $this->object;

		$return = $tag->wrap('div');
		$t = $this->rasterize($tag);
		$this->assertTrue( $t->is('div'), 'tag is a div' );
		$this->assertSame( $tag, $return, 'Returns tag object' );

		$return = $tag->wrap('img', '/');
		$t = $this->rasterize($tag);
		$this->assertTrue( $t->is('img'), 'tag is a div' );
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
		$return = $tag->wrap(false);
		$t = $this->rasterize($tag);
		$this->assertEquals(0, $t->size(), 'no tag' );
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
		$return = $tag->wrap('form');
		$t = $this->rasterize($tag);
		$this->assertTrue( $t->is('form'), 'tag is a form' );
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
		$return = $tag->wrap(false, false);
		$t = $this->rasterize($tag);
		$this->assertEquals(0, $t->size(), 'no tag' );
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
		$return = $tag->wrap(true);
		$t = $this->rasterize($tag);
		$this->assertTrue( $t->is('div'), 'tag is a div' );
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
		$return = $tag->wrap(true, true);
		$t = $this->rasterize($tag);
		$this->assertTrue( $t->is('div'), 'tag is a div' );
		$this->assertSame( $tag, $return, 'Returns tag object' );

		// These are hard to test, but they give the correct result
		$return = $tag->wrap('form', false);
		$t = $this->rasterize($tag);
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
		$return = $tag->wrap(false, 'span');
		$t = $this->rasterize($tag);
		$this->assertSame( $tag, $return, 'Returns tag object' );
		
    }

	/*
		TODO Lazy right now - before & after need to be able to accept tags..
		will get around to testing & implementing - NBD 
	*/
    public function testBefore() {
       	$tag = $this->object;

		$return = $tag->before();
		$this->assertSame( $tag, $return, 'Returns tag object' );
		$return = $tag->before('<div>cool</div>');
		$this->assertSame( $tag, $return, 'Returns tag object' );
       	
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testAfter() {
       	$tag = $this->object;

		$return = $tag->after();
		$this->assertSame( $tag, $return, 'Returns tag object' );
		$return = $tag->after('<div>cool</div>');
		$this->assertSame( $tag, $return, 'Returns tag object' );
       	
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testArg() {
        // Generic get_set_test.
        $this->get_set_test($this->object, 'style');

		// Ensure that if the key doesn't exist it returns false
		$this->assertEquals("", $this->object->style('k7'), 'k7 doesn\'t exist!' );
		$this->assertEquals("",  $this->object->style(2), 'args(2) doesn\'t exist!' );
    }

    public function testRemoveArg() {
		$tag = $this->object;

		$tag->arg('k1', 'v1');
		$tag->arg(array('k2' => 'v2', 'k3' => 'v3', 'k4' => 'v4'));

		// Remove no keys
		$return = $tag->removeArg();
		$arr = $tag->arg();
		$this->assertEquals( 4, count($arr), 'No keys removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Remove one key
		$return = $tag->removeArg('k2');
		$args = $tag->arg();
		$this->assertArrayNotHasKey('k2', $args, 'Does not have k2' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		$return = $tag->removeArg('k4');
		$args = $tag->arg();
		$this->assertArrayNotHasKey('k4', $args, 'Does not have k4' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Test if key doesn't exist
		$arr = $tag->arg();
		$return = $tag->removeArg('k5');
		$this->assertEquals( count($arr), count($tag->arg()), 'No keys removed' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Remove multiple keys
		$return = $tag->removeArg('k2', 'k3', 'k1');
		$args = $tag->arg();
		$this->assertArrayNotHasKey('k2', $args, 'Does not have k2' );
		$this->assertArrayNotHasKey('k3', $args, 'Does not have k3' );
		$this->assertSame( $tag, $return, 'returned value is a tag' );

		// Test empty
		$arr = $tag->arg();
		$this->assertEquals(0, count($arr), 'args array empty');
    }

    /**
     * @todo Implement testDefaults().
     */
    public function testDefaults() {
        // Remove the following lines when you implement this test.
        $this->markTestSkipped(
			'Test data was lost for this one - passed before, not too worried - I\'ll take care of this later.'
        );
    }

    /**
     * @todo Implement testGive().
     */
    public function testGive() {
        S('box:rounded')->give('rounded.css', 'roundness', '20px');
		S('box:rounded')->give('sayHello.js', 'wahoo', 'You poisoned the waterhole!');
		// echo S('css');
		// echo S('javascript');

		$this->markTestSkipped('
			Need more libraries to really test this one.
		');
    }

    public function testId() {
        $tag = $this->object;

		$id = $tag->id();
		$this->assertEquals( 6, strlen($id), 'id set initially to random unique id' );
		$t = $this->rasterize($tag);
		$this->assertEquals($tag->id(), $t->attr('id'), 'tag has correct id');
		
		$return = $tag->id('hello');
		$id = $tag->id();
		$this->assertEquals( 'hello', $id, 'retrieved correct id' );
		$this->assertSame( $tag, $return, 'returned tag object' );
		$t = $this->rasterize($tag);
		$this->assertEquals($tag->id(), $t->attr('id'), 'tag has correct id');

		$return = $tag->id('howdy');
		$id = $tag->id();
		$this->assertEquals( 'howdy', $id, 'retrieved correct id' );
		$this->assertSame( $tag, $return, 'returned tag object' );
		$t = $this->rasterize($tag, true);
		$this->assertEquals($tag->id(), $t->attr('id'), 'tag has correct id');

    }

    public function testHeight() {
        $tag = $this->object;

		$height = $tag->height();
		$this->assertEquals( '', $height, 'nothing set for message initially' );
        $t = $this->rasterize($tag, true);
		
		// Adding endings
		$return = $tag->height('7px');
		$height = $tag->height();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '7px', $height, 'got right height' );
		$t = $this->rasterize($tag, true);
		
		$return = $tag->height('70em');
		$height = $tag->height();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '70em', $height, 'got right height' );
		$t = $this->rasterize($tag, true);
		
		// Using numbers
		$return = $tag->height(10);
		$height = $tag->height();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '10px', $height, 'got right height' );
		$t = $this->rasterize($tag, true);
		
		$return = $tag->height('20');
		$height = $tag->height();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '20px', $height, 'got right height' );
		$t = $this->rasterize($tag, true);
		
    }

    public function testWidth() {
		$tag = $this->object;

		$width = $tag->width();
		$this->assertEquals( '', $width, 'nothing set for message initially' );
		$t = $this->rasterize($tag, true);

		// Adding endings
		$return = $tag->width('7px');
		$width = $tag->width();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '7px', $width, 'got right width' );
		$t = $this->rasterize($tag, true);

		$return = $tag->width('70em');
		$width = $tag->width();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '70em', $width, 'got right width' );
		$t = $this->rasterize($tag, true);

		// Using numbers
		$return = $tag->width(10);
		$width = $tag->width();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '10px', $width, 'got right width' );
		$t = $this->rasterize($tag, true);

		$return = $tag->width('20');
		$width = $tag->width();
		$this->assertSame( $tag, $return, 'returned tag object' );
		$this->assertEquals( '20px', $width, 'got right width' );
		$t = $this->rasterize($tag, true);
    }

	private function rasterize(Tag $t, $debug = false) {
		// Add id
		$t->attr('id', $t->id());
		$html = $t->__tostring();
		
		if($debug) {
			echo htmlspecialchars($html);echo "<br/>";
		}
		
		$doc = phpQuery::newDocument($html);
		phpQuery::selectDocument($doc);
		
		// Use phpQuery - random sampling of attributes
		$t = pq('#'.$t->id());
		
		return $t;
	}
}
?>
