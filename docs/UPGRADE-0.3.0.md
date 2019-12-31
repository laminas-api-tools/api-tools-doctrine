# Module changes

- Install and configure api-tools-doctrine-querybuilder according the documentation
- Make sure to add the use with api-tools doctrine part!

# Configuration adjustments

- Add 'entity_identifier_name' key to the api-tools-rest config. This is the name of the identifier property of your entity.
- Change key 'api-tools-collection-query' to 'api-tools-doctrine-query-provider'
- Change key 'query_provider' in api-tools > doctrine-connected to 'query_providers' according to https://github.com/laminas-api-tools/api-tools-doctrine#query-providers. Example:

```
'query_providers' => array(
    'default' => 'default_odm',
    'fetch_all' => 'Key.in.api-tools-doctrine-query-provider',
),
```

# Class adjustments

- Change interface of query provider classes to: Laminas\ApiTools\Doctrine\Server\Query\Provider\QueryProviderInterface or extend the DefaultOrm / DefaultOdm. (Note: you could also use the defaults from the querybuilder module)
- Prepend the $resourcEvent to the createQuery and change logic if needed


# Implementation adjustments
- Change the 'query' parameter to 'filters' (in $_GET)
- Change the 'sort' parameter to 'order-by' (in $_GET)

