<?php


class Document extends Dom
{
	function setTitle(string $title)
	{
		$this->doc('title')->text($title);
	}

	function setMetaDescription(string $description)
	{
		$this->doc('meta[name="description"]')->attr('content')->content($description);
	}

	function setTheme(string $theme)
	{
		$this->doc('body')->className('theme-a')->visibility(iHTML\QueryClass::VISIBLE);
	}

	function isLogged(bool $isLogged)
	{
		$this->doc('body')->className('is-logged')->visibility($isLogged ? iHTML\QueryClass::VISIBLE : iHTML\QueryClass::HIDDEN);
	}
}


$doc = new Document;
$doc->setTitle('');
$doc->setMetaDescription('');
$doc->setTheme('');
$doc->isLogged(true);

class Widget
{

}

class Searchbox extends Widget
{

}

class Adv extends Widget
{

}


$side = new Sidebar;
$side->addWidget(int $i|Widget $widget);
$side->removeWidget(int $i|Widget $widget);

class Content extends Dom
{
	function setTitle(string $title)
	{
		$this->doc('title')->text($title);
	}

	function setContent(string $content)
	{
		$this->doc('meta[name="description"]')->attr('content')->content($description);
	}

	function setTags(array $tags)
	{
		$this->doc('meta[name="description"]')->attr('content')->content($description);
	}
}

$doc = new Content;
$doc->setTitle('');
$doc->setContent('');
$doc->setTags('');






