AEON SWITCH
===

[![Travis](https://travis-ci.org/codeChap/Aeon.svg?style=flat-square)](https://travis-ci.org/codeChap/Aeon)

3RD Party Electricity Integration Package

## What does this do?

Allows you to purchase electricity though Blue Label Telekoms API.

## Integration

The package supports the following API requests

    Authentication and meter confirmation
    Get Voucher
    Sold Voucher
    Reprint Voucher
    Account information

## Usage

Use [composer](http://getcomposer.org) to install it or simply include the files somewhere:

```
    $config = array(
            'ip' => '196.38.158.118',
            'port' => '7898',
            'DeviceId' => '2002',
            'DeviceSer' => 'xxxx',
            'UserPin' => 'xxxx'
        );

    $meterNumber = '01060029501';
    $rands = '120';
    $reference = 'Testing Normal voucher request';

    // Request a voucher
    $aeon = new CodeChap\Aeon($this->config);
    $aeon[] = new CodeChap\Request\Electricity($meterNumber, $rands, $reference);
    $aeon->execute();

    // If the above is accpeted you can ask the user to make payment with what ever methods you choose //

    // Confirm the sale
    $aeon = new CodeChap\Aeon($this->config);
    $aeon[] = new CodeChap\Request\Confirm($meterNumber);
    $aeon->execute();

```

## Todo

    Support the following requests and or transaction types
    
    Fault report
    Fault list
    Log fault

## Questions

Ask me on twitter if you have any questions: [@codeChap](http://twitter.com/codechap)

## Disclaimer:

Use this library at your own risk, I take no responsibility what so ever for the use of it.