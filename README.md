# Rigid Type

- create highly specific types simply by extending a class and defining the fields on your entity
- can be used to validate the data structure and completeness of incoming data (consuming an API via http client)
- can be used for documentation and auto-complete of your own (php) API methods 

### 1) Create some new Types
```php
namespace MyApp\Types;

public class Address extends \EasybellLibs\RigidType\RigidType
{
    public string $street;
    public int    $number;
}
```
```php
namespace MyApp\Types;

public class Invoice extends \EasybellLibs\RigidType\RigidType
{
    public int $amount;
    public ?string $description;
    public object $article;
    public \MyApp\Types\Address $address;
}
```

### 2) Use types in APIs to guarantee structure and facilitate exploration
```php
use MyApp\Types\Invoice;

class MyApi {
    public function getInvoice($id): ?Invoice
    {
        $data = $this->doSomeApiCall($id);
        
        if (!$data) {
            return null; // only if you allow empty responses
        }
        
        // the constructor will throw a TypeValidationException if the provided does not match the Type definition
        return new Invoice($data);
    }
}
```

Instead of returning a generic array or stdClass, the users of our library can now look up the return type and know exactly what fields and types to expect.

### 3) Use Types in your code
```php
public function show($invoiceId)
{
    // let's pretend to display the invoice we need to query an API first
    $invoice = app(MyApi::class)->getInvoice($invoiceId);
    
    if (!$invoice) {
        return response()->json([], 404);
    }
    
    // below this line, all properties are *guaranteed*
    // to exist on the object as defined in its Type, and are auto-suggested by the IDE.
    // Nesting RigidTypes (including validation) is possible.
    
    $amount = $invoice->amount;
    $street = $invoice->address->street;
}
```

### 4) Concerns
- Only use RigidTypes when you trust the underlying API. If the API changes, then the Types will most likely break, until they match the API definition again. (This is the whole point of this library)
- Current implementation uses public properties instead of getters. Object data is not safe from accidental manipulation.
