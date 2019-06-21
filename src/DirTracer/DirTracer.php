<?php

namespace DirTracer;

class DirTracer
{

	private $curDir;
	private $baseActualPath;
	private $baseDestinationPath;
	private $structuredFiles;
	private $allowedExtensions;

	public function __construct($folderToScan, array $allowedExtensions = [])
	{
		$folderToScan = str_replace('../', '', $folderToScan);
		$this->curDir = getcwd();
		$this->baseActualPath = '/' . $folderToScan;
		$this->baseDestinationPath = $this->curDir . $this->baseActualPath;
		$this->allowedExtensions = $allowedExtensions;
		$this->structuredFiles = $this->getStructureRecursive($folderToScan);
	}



	public function getStructureRecursive($tutorial)
	{
		$dirContent = scandir($tutorial);
		// return $tutorial.'dsdsd'.$dirContent[5];
		// return $dirContent;
		$returnArray = [];
		$allFilesFolders = array_filter($dirContent, function ($e) use ($tutorial) {
			if ($e != '.' && $e != '..') {
				return true;
			} else  return false;
		});
		foreach ($allFilesFolders as $item) {
			if (is_dir($tutorial . '/' . $item))
				array_push($returnArray, (object)["name" => $item, "type" => "folder", "content" => $this->getStructureRecursive($tutorial . '/' . $item)]);
			else
		 if (isset(pathinfo($item)['extension']) && in_array(pathinfo($item)['extension'], $this->allowedExtensions)) {
				array_push($returnArray, (object)["name" => $item, "type" => "file", "content" => $this->getBaseAddress() . str_replace($this->baseDestinationPath, '', $tutorial . '/' . $item)]);
			}
		}
		return $returnArray;
	}

	function jsonResponse()
	{
		return json_encode($this->structuredFiles);
	}

	function buildMenu()
	{
		return $this->_buildMenu($this->structuredFiles);
	}

	private function _buildMenu($files)
	{
		$toBeReturned = '';
		$toBeReturnedArray = [];

		foreach ($files as $key => $item) {
			$toBePushed = '';
			if ($item->type == 'file')
				$toBePushed = '<li><a href="' . $item->content . '">' . $item->name . '</a></li>';
			else {
				$toBePushed = '<li>' . $item->name . '<ul>' . $this->_buildMenu($item->content) . '</ul></li>';
			}
			array_push($toBeReturnedArray, $toBePushed);
		}
		$toBeReturned = implode($toBeReturnedArray);
		return $toBeReturned;
	}

	function getBaseAddress()
	{
		$parsedPath = pathinfo($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		return ('http://' . $parsedPath['dirname'] . '/' . $parsedPath['basename'] . '/');
	}
}
