<?php
namespace jorgealexandro\KendoFilters;
use Carbon\Carbon;

Class Filters
{
	public static function processFilters($filters = [], $dateFields = [])
	{
		$processedFilters = [];

		foreach ($filters as $filter) {
			if(array_search($filter['field'], $dateFields) != false) {
				$filter['value'] = new Carbon($filter['value']);
			}
			else {
				switch ($filter['operator']) {
					case 'startswith':
						$filter['value'] = $filter['value'] . '%';
						break;

					case 'endswith':
						$filter['value'] = '%' . $filter['value'];
						break;

					case 'contains':
					case 'doesnotcontain':
						$filter['value'] = '%' . $filter['value'] . '%';
						break;

					default:
						$filter['value'] = $filter['value'];
						break;
				}                   
				
			}

			switch ($filter['operator']) {
				case 'eq':
					$filter['operator'] = '=';
					break;

				case 'neq':
					$filter['operator'] = '!=';
					break;

				case 'gte':
					$filter['operator'] = '>=';
					break;

				case 'gt':
					$filter['operator'] = '>';
					break;

				case 'lte':
					$filter['operator'] = '<=';
					break;

				case 'lt':
					$filter['operator'] = '<';
					break;

				case 'startswith':
				case 'contains':
				case 'endswith':
					$filter['operator'] = 'LIKE';
					break;

				case 'doesnotcontain':
					$filter['operator'] = 'NOT LIKE';
					break;
				default:
					break;
			}
			array_push($processedFilters, $filter);
		}
		return $processedFilters;
	}

	public static function addFilters($query, $filters = [], $dateFields = []) {
		$filters = Filters::processFilters($filters, $dateFields);
		foreach ($filters as $filter) {
			if(count(explode('.', $filter['field'])) > 1) {
				$fieldDetail = explode('.', $filter['field']);
				$query->whereHas($fieldDetail[0], function($query) use($fieldDetail, $filter) {
					$query->where($fieldDetail[1], $filter['operator'], $filter['value']);
				});
			}
			else {
				$query->where($filter['field'], $filter['operator'], $filter['value']);
			}
		}

		return $query;
	}
}