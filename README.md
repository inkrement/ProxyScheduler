# ProxyScheduler

## Usage
To use this package just call the following script in your terminal. It uses composer
and adds this package to your requirements:

> composer require inkrement/proxyscheduler

Then import the Scheduler and create a new instance:

```
use Inkrement\ProxyScheduler\ProxyScheduler;
use Inkrement\ProxyScheduler\DataAbstraction\CSVAdapter;

$dao = new CSVAdapter(__DIR__.'/test_multipleentries.csv');
$scheduler = new ProxyScheduler($dao);
$proxy = $scheduler->getNext();
```

## Further Information
Run phpunit tests:

```
composer test
```
