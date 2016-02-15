# ProxyScheduler

## Usage
To use this package just call the following script in your terminal. It uses composer
and adds this package to your requirements:

> composer require inkrement/proxyscheduler

Then import the Scheduler and create a new instance:

```
use Inkrement\ProxyScheduler\ProxyScheduler;
use Inkrement\ProxyScheduler\DataAbstraction\CSVAdapter;

$dao = new CSVAdapter('proxy_list.csv');
$scheduler = new ProxyScheduler($dao);
$proxy = $scheduler->getNext();
```

## CSV Proxy List Format
The first 3 fields are mandatory (ip, port and type), the others are optional, but are
important for some scheduling algorithms (e.g. hit, miss and rating). The default
delimiter is a semicolon but it can be changed with an additional parameter.

## Further Information
Run phpunit tests:

```
composer test
```
