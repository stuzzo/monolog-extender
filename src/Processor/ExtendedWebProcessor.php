<?php

namespace Stuzzo\Monolog\Processor;

use Monolog\Processor\WebProcessor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Add Request data to the record generated by logger
 *
 * @author Alfredo Aiello <stuzzo@gmail.com>
 */
class ExtendedWebProcessor extends WebProcessor
{
	
	public function __construct($serverData = null, $extraFields = null)
	{
		parent::__construct($serverData, $extraFields);
		$this->extraFields = array_merge($this->extraFields, [
			'protocol' => 'REQUEST_SCHEME',
		]);
	}
	
	public function __invoke(array $record)
	{
		$record = parent::__invoke($record);
		
		return $this->addRequestData($record);
	}
	
	protected function addRequestData($record)
	{
		$request = $GLOBALS['request'];
		if (empty($request)) {
			return $record;
		}
		
		if ($request instanceof Request) {
			return $this->addSymfonyRequestData($request, $record);
		}
		
		/*
		 * Here you can add other request classes
		 */
	}
	
	protected function addSymfonyRequestData(Request $request, $record)
	{
		$record['headers'] = $request->headers->all();
		$record['files']   = $request->files->all();
		if ('POST' === $request->getMethod()) {
			$record['data'] = $request->request->all();
		} else {
			$record['data'] = $request->query->all();
		}
		
		return $record;
	}
}
