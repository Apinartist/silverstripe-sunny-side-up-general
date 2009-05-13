<?php
/**
 * @package sapphire
 * @subpackage tests
 */
class RSSFeedTest extends SapphireTest {
	
	function testRSSFeed() {
		$list = new DataObjectSet();
		$list->push(new RSSFeedTest_ItemA());
		$list->push(new RSSFeedTest_ItemB());
		$list->push(new RSSFeedTest_ItemC());

		$origServer = $_SERVER;
		$_SERVER['HTTP_HOST'] = 'www.example.org';
		$_SERVER['SCRIPT_NAME'] = '/sapphire/main.php';
		
		$rssFeed = new RSSFeed($list, "http://www.example.com", "Test RSS Feed", "Test RSS Feed Description");
		$content = $rssFeed->feedContent();

		//Debug::message($content);
		$this->assertContains('<link>http://www.example.org/item-a/</link>', $content);
		$this->assertContains('<link>http://www.example.com/item-b.html</link>', $content);
		$this->assertContains('<link>http://www.example.com/item-c.html</link>', $content);

		$this->assertContains('<title>ItemA</title>', $content);
		$this->assertContains('<title>ItemB</title>', $content);
		$this->assertContains('<title>ItemC</title>', $content);

		$this->assertContains('<description>ItemA Content</description>', $content);
		$this->assertContains('<description>ItemB Content</description>', $content);
		$this->assertContains('<description>ItemC Content</description>', $content);


		// Feed #2 - put Content() into <title> and AltContent() into <description>
		$rssFeed = new RSSFeed($list, "http://www.example.com", "Test RSS Feed", "Test RSS Feed Description", "Content", "AltContent");
		$content = $rssFeed->feedContent();

		$this->assertContains('<title>ItemA Content</title>', $content);
		$this->assertContains('<title>ItemB Content</title>', $content);
		$this->assertContains('<title>ItemC Content</title>', $content);

		$this->assertContains('<description>ItemA AltContent</description>', $content);
		$this->assertContains('<description>ItemB AltContent</description>', $content);
		$this->assertContains('<description>ItemC AltContent</description>', $content);
		
		$_SERVER = $origServer;
	}
	
}

class RSSFeedTest_ItemA extends ViewableData {
	// RSS-feed items must have $casting/$db information.
	static $casting = array(
		'Title' => 'Varchar',
		'Content' => 'Text',
		'AltContent' => 'Text',
	);
	
	function Title() {
		return "ItemA";
	}
	function Content() {
		return "ItemA Content";
	}
	function AltContent() {
		return "ItemA AltContent";
	}
	
	function Link() {
		return "item-a/";
	}
}

class RSSFeedTest_ItemB extends ViewableData {
	// ItemB tests without $casting

	function Title() {
		return "ItemB";
	}
	function AbsoluteLink() {
		return "http://www.example.com/item-b.html";
	}
	function Content() {
		return "ItemB Content";
	}
	function AltContent() {
		return "ItemB AltContent";
	}
}

class RSSFeedTest_ItemC extends ViewableData {
	// ItemC tests fields - Title has casting, Content doesn't.
	static $casting = array(
		'Title' => 'Varchar',
		'AltContent' => 'Text',
	);

	public $Title = "ItemC";
	public $Content = "ItemC Content";
	public $AltContent = "ItemC AltContent";

	function Link() {
		return "item-c.html";
	}
	function AbsoluteLink() {
		return "http://www.example.com/item-c.html";
	}
}