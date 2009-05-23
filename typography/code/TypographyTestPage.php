<?php
/**
 * Add a page to your site that allows you to view all the html that can be used in the typography section - if applied correctly.
 */


class TypographyTestPage extends Page {

	static $icon = "typography/images/treeicons/TypographyTestPage";

	static $auto_include = false;

	static function setAutoInclude($value) {
		self::$auto_include = $value;
	}

	static $defaults = array(
	"URLSegment" => "typo",
	"ShowInMenus" => false,
	"ShowInSearch" => false,
	"Title" => "Typography Test",
	"Content" => '

<p>
	Below is a variety of styles that you may use in your SilverStripe Content Management System.
	The styles should look the same (or similar) in your Content Management System as they do on this page.
	Below, each special formats are interspersed with a paragraph as this is a more normal typographic setting than having heading after heading after heading.
	Each start of a new section will tell you what to look for.
	Make sure to also check for the within paragraph formatting:
</p>
<ul>
	<li><strong>bold 1</strong>, <b>bold 2</b></li>
	<li><u>underlined</u></li>
	<li><em>italics 1</em> or <i>italics 2</i> </li>
	<li><a href="home">internal link</a></li>
	<li><a href="http://www.sunnysideup.co.nz">external link</a></li>
</ul>
<h2>paragraph with images and linked images</h2>
<p>
	In in purus eget mauris fringilla placerat.
	Proin pellentesque fermentum dui.
	Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
	Proin ultrices sagittis metus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
	Proin ultrices nulla id enim.
	Nullam mi.
	<a href="home"><img src="cms/images/loading.gif" alt="loading image" width="150" height="150" /></a>
	In ipsum arcu, sodales commodo, elementum at, euismod nec, felis.
	Vestibulum laoreet, felis at vulputate posuere, tellus lorem ornare ante, eget commodo magna metus vitae mauris.
	Aliquam et enim.
	Integer vel erat sit amet nulla feugiat scelerisque.
	Fusce ornare molestie mauris.
	Aliquam a leo quis eros mollis varius.
	Quisque egestas velit ac dui.
	Quisque eu purus vel risus tincidunt dictum.
	Curabitur sit amet turpis id leo vestibulum imperdiet.
	Suspendisse mollis ultrices nulla.
	Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
	Proin ultrices sagittis metus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
	Proin ultrices nulla id enim.
	Nullam mi.
</p>

<p>
	In in purus eget mauris fringilla placerat.
	Proin pellentesque fermentum dui.
	Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
	Proin ultrices sagittis metus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
	Proin ultrices nulla id enim.
	Nullam mi.
	In ipsum arcu, sodales commodo, elementum at, euismod nec, felis.
	Vestibulum laoreet, felis at vulputate posuere, tellus lorem ornare ante, eget commodo magna metus vitae mauris.
	Aliquam et enim.
	Integer vel erat sit amet nulla feugiat scelerisque.
	Fusce ornare molestie mauris.
	<img src="cms/images/loading.gif" alt="loading image" width="150" height="150" class="right" />
	Aliquam a leo quis eros mollis varius.
	Quisque egestas velit ac dui.
	Quisque eu purus vel risus tincidunt dictum.
	Curabitur sit amet turpis id leo vestibulum imperdiet.
	Suspendisse mollis ultrices nulla.
	Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
	Proin ultrices sagittis metus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
	Proin ultrices nulla id enim.
	Nullam mi.
</p>

<p>
	In in purus eget mauris fringilla placerat.
	Proin pellentesque fermentum dui.
	Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
	Proin ultrices sagittis metus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
	Proin ultrices nulla id enim.
	Nullam mi.
	In ipsum arcu, sodales commodo, elementum at, euismod nec, felis.
	Vestibulum laoreet, felis at vulputate posuere, tellus lorem ornare ante, eget commodo magna metus vitae mauris.
	Aliquam et enim.
	Integer vel erat sit amet nulla feugiat scelerisque.
	Fusce ornare molestie mauris.
	Aliquam a leo quis eros mollis varius.
	Quisque egestas velit ac dui.
	Quisque eu purus vel risus tincidunt dictum.
	Curabitur sit amet turpis id leo vestibulum imperdiet.
	Suspendisse mollis ultrices nulla.
	<img src="cms/images/loading.gif" alt="loading image" width="150" height="150" class="left" />
	Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
	Proin ultrices sagittis metus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
	Proin ultrices nulla id enim.
	Nullam mi.
</p>
<h1>heading one above colour table</h1>
<p>
	The table below can show the ten main colours for your site.
	In order to do so, they must be specified by a coder in your typography.css file (using td.colour1{background-color: YourColour1Here;}, td.colour2{background-color: YourColour2Here;}, etc...).
</p>
<table id="colourTable" summary="colour table">
	<tbody>
		<tr>
			<td class="colourCell colour1">&nbsp;</td>
			<td class="colourCell colour2">&nbsp;</td>
			<td class="colourCell colour3">&nbsp;</td>
		</tr>
		<tr>
			<td class="colourCell colour4">&nbsp;</td>
			<td class="colourCell colour5">&nbsp;</td>
			<td class="colourCell colour6">&nbsp;</td>
		</tr>
		<tr>
			<td class="colourCell colour7">&nbsp;</td>
			<td class="colourCell colour8">&nbsp;</td>
			<td class="colourCell colour9">&nbsp;</td>
		</tr>
		<tr>
			<td class="colourCell colour10">&nbsp;</td>
			<td class="colourCell colour11">&nbsp;</td>
			<td class="colourCell colour12">&nbsp;</td>
		</tr>
	</tbody>
</table>
<h1>example of heading 1</h1>
<p>
 This is an example of a couple of left-align paragraphs with <strong>bold</strong>, <u>underlined</u>, and <em>italics</em> in it.
 It also contains some inline formatting styles (such as bold and italics - see below) Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
 justo quam fermentum ligula, vel hendrerit est sem a orci.
 porttitor nec, fringilla et, massa.
 Integer turpis.
 Etiam sed dolor.
 Aenean non tellus.
 Fusce cursus ornare tortor.
 Nullam risus.
 Suspendisse quam.
 Donec varius semper ipsum.
 Morbi iaculis dolor eget elit.
 Cras velit dui, rhoncus ut, placerat non, porta a, neque.
 Morbi luctus eros ac mauris.
 Cras sed quam.
 Etiam sed quam sit amet nisl viverra iaculis.
 Etiam mattis, est eu ornare varius, dui ligula mattis erat, in condimentum orci turpis et neque.
 Integer odio sapien, pulvinar quis, consequat a, pretium at, tortor.
 <em>italicized</em> Nunc mattis blandit erat.
 <u>Underline</u> Phasellus auctor,
 <strong>bold</strong>, Donec.
 Aliquam a leo quis eros mollis varius.
 Quisque egestas velit ac dui.
 Quisque eu purus vel risus tincidunt dictum.
 Curabitur sit amet turpis id leo vestibulum imperdiet.
 Suspendisse mollis ultrices nulla.
 Donec at sapien eget turpis dictum tempus.
 Nulla rutrum, leo nec ornare rhoncus, augue neque venenatis urna, sit amet consequat leo nisl ut erat.
 Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean vitae nunc sed magna accumsan consectetuer.
 Vestibulum volutpat.
</p>
<h2>example of heading 2</h2>
<p>
 Below is an unordered (bulleted) list
</p>
<ul>
 <li>
 Cras luctus fringilla lorem.
 Donec scelerisque augue non orci.
 </li>
 <li>
	Phasellus felis nunc, mollis nec, laoreet a, facilisis a, dui.
	Vivamus venenatis malesuada tortor.
	Curabitur aliquam sapien ac risus.
	Integer elementum.
	Vestibulum ornare felis sed quam.
	Donec tempor scelerisque nisi.
	Nulla facilisi.
	Donec porttitor.
	Morbi et sapien.
 </li>
 <li>
	In feugiat consectetuer lectus.
	Cras lacinia elit nec libero.
 </li>
 <li>
 Sed id ante in nisi faucibus tristique.
 Suspendisse laoreet.
 </li>
</ul>
<p>
 In in purus eget mauris fringilla placerat.
 Proin pellentesque fermentum dui.
 Donec tortor sapien, condimentum a, iaculis at, faucibus id, pede.
 Proin ultrices sagittis metus.
 Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
 Proin ultrices nulla id enim.
 Nullam mi.
 In ipsum arcu, sodales commodo, elementum at, euismod nec, felis.
 Vestibulum laoreet, felis at vulputate posuere, tellus lorem ornare ante, eget commodo magna metus vitae mauris.
 Aliquam et enim.
 Integer vel erat sit amet nulla feugiat scelerisque.
 Fusce ornare molestie mauris.
 Integer erat.
 Integer pulvinar cursus dolor.
 Nunc interdum.
</p>

<h3>example of heading 3</h3>
<p>
 Below is an ordered (numbered) list:
</p>
<ol>
 <li>
	Cras luctus fringilla lorem.
	Donec scelerisque augue non orci.
 </li>
 <li>
	Phasellus felis nunc, mollis nec, laoreet a, facilisis a, dui.
	Vivamus venenatis malesuada tortor.
	Curabitur aliquam sapien ac risus.
	Integer elementum.
	Vestibulum ornare felis sed quam.
	Donec tempor scelerisque nisi.
	Nulla facilisi.
	Donec porttitor.
	Morbi et sapien.
 </li>
 <li>
	In feugiat consectetuer lectus.
	Cras lacinia elit nec libero.
 </li>
 <li>
	Sed id ante in nisi faucibus tristique.
	Suspendisse laoreet.
 </li>
</ol>
<p>
 Vivamus id diam nec quam bibendum dapibus.
 Nam non nunc ac metus dapibus varius.
 Phasellus tempor metus nec quam ornare tempor.
 Maecenas vitae ligula.
 Maecenas id mi.
 Mauris ut justo.
 Integer et est.
 Integer tempus convallis est.
 Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nunc id nulla non mauris lacinia varius.
 Suspendisse potenti.
 Nam accumsan elit eu orci.
 Donec mollis, libero et porttitor imperdiet, est lorem pulvinar libero, id commodo quam mi vitae elit.
 Quisque ac odio id ante semper tincidunt.
 Quisque ultrices eros egestas augue.
 Sed augue purus, sagittis et, bibendum quis, tincidunt ut, risus.
 Ut gravida sodales nisi.
 Morbi pede.
</p>
<h4>example of heading 4</h4>
<p>
 Below is an example of a simple table.
 There is a myriad of more sophisticated formatting you can apply to tables.
 Shown here are cell headings and standard cells.
</p>
<table summary="test table">
 <tbody>
	<tr>
	 <th>table col heading 1 + row heading 0</th>
	 <th>table heading 1</th>
	 <th>table heading 2</th>
	 <th>table heading 3</th>
	</tr>
	<tr>
	 <th>table row heading 1</th>
	 <td>cell 1</td>
	 <td><p>paragraph in a cell</p></td>
	 <td><ul><li>list item in a cell</li><li>list item in a cell</li><li>list item in a cell</li></ul></td>
		</tr>
	<tr>
	 <th>table row heading 2</th>
	 <td>cell 1</td>
	 <td><p>paragraph in a cell Sed augue purus, sagittis et, bibendum quis, tincidunt ut, risus.</p></td>
	 <td>cell 3</td> </tr>
	<tr>
	 <th>table row heading 3</th>
	 <td>cell 1</td>
	 <td>cell 2</td>
	 <td>cell 3</td>
	</tr>
 </tbody>
</table>

<h5>example of heading 5 - other formats</h5>
<p>
 Here are some examples of other formats.
 Firstly, here is the address format (you will have to add linebreaks):
</p>
<address>
 Sunny Side Up
 <br /> PO Box 3058
 <br /> Dunedin
 <br /> New Zealand
 <br />
</address>
<p>Next is the pre-formatted one:</p>
<pre>
 Sed in sem. Proin augue sapien, sollicitudin nec, vestibulum mollis, aliquam eget, leo.
</pre>
<p>
 Here is a list of terms
</p>
<dl>
 <dt>term 1</dt>
 <dt>term 2</dt>
 <dt>term 3</dt>
 <dt>term 4</dt>
 <dt>term 5</dt>
</dl>
<p>
 And here is a list of definitions and terms:
</p>
<dl>
 <dd>definition 1:</dd>
	<dt> Sed in sem. Proin augue sapien, sollicitudin nec, vestibulum mollis, aliquam eget, leo. </dt>
 <dd>definition 2:</dd>
	<dt>Nam tincidunt augue quis neque.</dt>
 <dd>definition 3:</dd>
	<dt> Sed in sem. Proin augue sapien, sollicitudin nec, vestibulum mollis, aliquam eget, leo.</dt>
 <dd>definition 4:</dd>
	<dt>Nam tincidunt augue quis neque.</dt>
 <dd>definition 5:</dd>
	<dt>Sed in sem. Proin augue sapien, sollicitudin nec, vestibulum mollis, aliquam eget, leo.</dt>
</dl>
<p>
 below you can find an example of an indented paragraph:
</p>
<blockquote>
 <p>
	Ut vulputate ante.
	Maecenas nec est.
	Nullam id leo in sapien commodo hendrerit.
	Nam tincidunt augue quis neque.
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
	Nulla lobortis. In ante nunc, consequat non, placerat vel, auctor ac, magna.
	In eget mi.
	Sed hendrerit.
	Integer fringilla, metus in adipiscing venenatis, nisi metus scelerisque magna, id fringilla dolor diam ac ligula.
	Nulla consequat nunc id sem.
 </p>
</blockquote>

<h6>example of heading 6</h6>
<p style="text-align: center;">
 Here is an example of a centered paragraph.
 Duis libero enim, dapibus sed, iaculis et, rutrum ac, metus.
 Donec convallis molestie risus.
 Etiam ut diam at tellus consequat euismod.
 Nullam odio tortor, cursus quis, interdum eu, faucibus quis, enim.
 Suspendisse eros mi, porta sit amet, luctus a, malesuada non, ligula.
 Nam velit lectus, ultrices id, vestibulum id, malesuada id, lectus.
 Aliquam erat.
 Etiam facilisis.
 Vivamus lorem lectus, fringilla at, rutrum vel, suscipit non, velit.
 Nullam lorem neque, suscipit ac, faucibus ut, dictum a, enim.
 Phasellus vestibulum augue et tellus.
 Suspendisse sollicitudin sem vitae magna.
 Sed dignissim nisi eget odio.
 Nullam massa.
 Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
 Nullam sed tortor.
 Aenean tellus est, bibendum non, fringilla in, eleifend vitae, erat.
</p>
<h1>
 <b>
	<u>
	 <i>
		Note that this page is NOT shown in any searches or menus.
	 </i>
	</u>
 </b>
</h1>',
	);

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(self::$auto_include) {
			$check = DataObject::get_one("TypographyTestPage");
			if(!$check) {
				$page = new TypographyTestPage();
				$page->ShowInMenus = 0;
				$page->ShowInSearch = 0;
				$page->ShowInSearch = 0;
				$page->Title = "typography test page";
				$page->MetaTitle = "typography test page";
				$page->PageTitle = "typography test page";
				$page->Sort = 99999;
				$page->URLSegment = "typo";
				$page->writeToStage('Stage');
				$page->publish('Stage', 'Live');
				Database::alteration_message("TypographyTestPage","created");
			}
		}
	}

}

class TypographyTestPage_Controller extends Page_Controller {

	function init() {
		parent::init();
		Requirements::javascript("jsparty/jquery/jquery.js");
		Requirements::javascript('typography/javascript/typography.js');
	}

	function Form() {
		$array = array();
		$array[] = "green";
		$array[] = "yellow";
		$array[] = "blue";
		$array[] = "pink";
		$array[] = "orange";
		$form = new Form(
			$controller = $this,
			$name = "TestForm",
			$fields = new FieldSet(
				// List the your fields here
				new HeaderField($name = "HeaderField1", $title = "HeaderField Level 1", 1),
				new TextField($name = "TextField", $title = "Text Field Example"),
				new TextareaField($name = "TextareaField", $title = "Textarea Field", 5, 5),
				new EmailField("EmailField", "Email address"),
				new HeaderField($name = "HeaderField2", $title = "HeaderField Level 2", 2),
				new DropdownField($name = "DropdownField",$title = "Dropdown Field",$source = Geoip::getCountryDropDown()),
				new OptionsetField($name = "OptionsetField",$title = "Optionset Field",$array),
				new CheckboxSetField($name = "CheckboxSetField",$title = "Checkbox Set Field",$array),
				new HeaderField($name = "HeaderField3", $title = "HeaderField Level 3", 3),
				new NumericField($name = "NumericField", $title = "Numeric Field "),
				new DateField($name = "DateField", $title = "Date Field"),
				new CheckboxField($name = "CheckboxField", $title = "Checkbox Field")
			),
			$actions = new FieldSet(
					// List the action buttons here
					new FormAction("signup", "Sign up")

			),
			$requiredFields = new RequiredFields(
					// List the required fields here: "Email", "FirstName"
			)
		);
		return $form;
	}

	function TestForm() {
		die("thank you for signing up to twenty years of free chocolate");
	}

}


