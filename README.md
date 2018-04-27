# Laravel Kendo Selver Filters

This Helper process Kendo Grid filters params and returns the Laravel filters equivalents ready to use in your query objects

```php
// In your controller

$query = Model::whereNull('deleted_at');

if($request->has('filter')) {
	$filters = Filters::processFilters($request->filter['filters']);
	foreach ($filters as $filter) {
		$query->where($filter['field'], $filter['operator'], $filter['value']);
	}
}

$rows = $query->get();
```
