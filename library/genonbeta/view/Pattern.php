<?php

namespace genonbeta\view;

use genonbeta\controller\RealtimeDataProcess;
use genonbeta\io\File;
use genonbeta\provider\AssetResource;
use genonbeta\provider\ResourceManager;
use genonbeta\provider\resource\ResourceVariable;
use genonbeta\system\EnvironmentVariables;
use genonbeta\system\UniversalMessageFilter;
use genonbeta\view\PatternFilter;

class Pattern implements RealtimeDataProcess
{
	private $pattern;
	
	function __construct($pattern)
	{
		$this->pattern = UniversalMessageFilter::applyFilter($pattern, PatternFilter::TYPE_CODE);
	}
	
	public static function getPattenFromFile(File $file)
	{
		if (!$file->isFile() || !$file->isReadable())
			return false;
			
		return new Pattern($file->getIndex());
	}
	
	public static function getPatternFromResource($resourceName, $patternId)
	{
		if(!ResourceManager::resourceExists($resourceName))
			return false;
		
		$resource = ResourceManager::getResource($resourceName);
		
		if(!$resource->doesExist($patternId))
			return false;
		
		$resourcePath = $resource->findByName($patternId);
		$resourceFile = new File($resourcePath);
		
		return new Pattern($resourceFile->getIndex());
	}
	
	public static function getPatternAssetResource($resource)
	{
		if (!$file = AssetResource::openResource($resource)->getResourceFile())
			return false;
			
		return self::getPattenFromFile($file);
	}
	
	public function onFlush(array $args)
	{
		return $this->pattern;
	}
	
	public function __toString()
	{
		return $this->pattern;
	}
}
