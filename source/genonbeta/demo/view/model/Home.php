<?php

namespace genonbeta\demo\view\model;

use Configuration;
use genonbeta\content\OutputWrapper;
use genonbeta\database\model\sqlite3\SQLite3Loader;
use genonbeta\net\UrlResolver;
use genonbeta\provider\AssetResource;
use genonbeta\provider\ResourceManager;
use genonbeta\system\EnvironmentVariables;
use genonbeta\system\System;
use genonbeta\system\helper\CurrentManifest;
use genonbeta\util\ResourceHelper;
use genonbeta\util\Log;
use genonbeta\view\ViewSkeleton;

use genonbeta\demo\config\MainConfig;
use genonbeta\demo\language\Turkish;
use genonbeta\demo\view\model\pattern\GBasicSkeleton;
use genonbeta\demo\view\model\pattern\LogList;

class Home extends ViewSkeleton
{
	const TAG = "Home";

	public function onCreate(array $methods)
	{
		$log = new Log(self::TAG);
		$res = ResourceManager::getResource(MainConfig::DB_INDEX_NAME);

		$listPattern = new LogList($this);
		$queuedString = new \genonbeta\util\QueuedString();

		$queuedString->put("my");
		$queuedString->put("name");
		$queuedString->put("is");
		$queuedString->put("veli");

		$queuedString->useSeperator(" ");

		$log->d($queuedString->getString());

		try
		{
			$sdbLoader = new SQLite3Loader($res->findByName("tr_en"));
			$sdb = $sdbLoader->getDbInstance();
			$result = $sdb->query("SELECT * FROM `tr_en`");
			$cursor = $result->getCursor();

			$log->i("Veritabanında ".$cursor->getCount()." adet kelime bulunuyor");
		}
		catch(\Exception $e)
		{
		}

		$this->loadLanguage(new Turkish());
		$this->setUrlResolver(new UrlResolver(EnvironmentVariables::get("workerAddress"), CurrentManifest::getViewIndex()));

		$log->d($this->getString("t", ["naber lan"]));

		$log->i("<a href=\"" . $this->getUri("about", "?isOkay=true")."\">Goto about page</a>");

		$this->drawPattern(new GBasicSkeleton($this), "system_html", array(GBasicSkeleton::TITLE => "Home", GBasicSkeleton::BODY => $listPattern->drawAsAdapter(Log::getLogs())));
	}

	public function onOutputWrapper()
	{
		return new OutputWrapper();
	}
}
