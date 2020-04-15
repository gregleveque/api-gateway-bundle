# gateway-bundle

Gie Gateaway Bundle 

## Installation

```bash
composer require gregleveque/gateway-bundle
```

in AppKernel.php add

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new Gie\GatwayBundle\GieGatewayBundle(),
        // ...
    ];
}
```

## Configuration

```yaml
gie_gateway:
  redis_dsn: 127.0.0.1 # default value
  routes:
    name:
      target: https://www.target.com
      query:
        a:
          - first
          - second
        b: 3
      headers:
        Content-Type: application/json
      ttl: 60 # default value
```
When using routes defined in configuration, the client parameters **ALWAYS** overrides configuration parameters.
For querystring, if the variable is an array, the client replace the first value by default.
* If you need to replace a configured value fron an array, use the index of the value in the querystring
```
...&a[1]=replaced
```

* If you need to add a new value in the array, use an unused index
```
...&a=[3]=added
```

## Client usage

* Base route:
```
/api/gateway
```

* Configured routes:
```
/api/gateway/{route-name}
```

When using the default route or configured routes, there is 2 custom headers:

* X_GATEWAY_FORWARD: List of headers to forward separated by comma.
* X_GATEWAY_TTL: Change the default TTL of the response (default 60s)

