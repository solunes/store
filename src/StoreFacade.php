<?php
namespace Solunes\Store;

use Illuminate\Support\Facades\Facade;

class StoreFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'store';
	}
}