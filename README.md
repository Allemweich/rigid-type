# Rigid Type

## Core Idea
Provide input validation as well as code exploration for your APIs.

In other words, turn this:
```php
public function getCustomer(string $id): ?object
{
    return Http::get('/customers/' . $id)->json();
}

public function getCustomers(): array
{
    return Http::get('/customers')->json();
}
```

Into this:
```php
public function getCustomer(string $id): ?Customer
{
    return new Customer(Http::get('/customers/' . $id)->json());
}

public function getCustomers(): CustomerCollection
{
    return new CustomerCollection(Http::get('/customers')->json());
}
```

## Benefits
- Fail Fast: If the external json API does not return structures as defined
in your types, a TypeValidationException with a detailed message occurs
- Documentation: Users of your API client can just click on a return
type and see how exactly it is composed
- Code Exploration: Get auto-suggestion everywhere, while coding

## Installation
1) Install the package
```shell
composer require allemweich/rigid-type
```

## Usage
First, there should be some good idea where to store the classes you are about to create:
- in applications: `app/services/someApi/types`
- or in packages: `src/types`

1) Create a type
```php
class Post extends RigidType
{
    public int    $id;
    public string $title;
}
```
2) Use the type
```php
public function getPost(int $id): Post
{
    $data = Http::get('/posts/' . $id)->throw()->json();
    
    return new Post($data);
}
```

## Collections
It is very likely that you need to return lists of objects.
This comes to mind:
```php
function getPosts(): PostCollection
{
    $data = Http::get('/posts')->json();
    
    return new PostsCollection($data);
}
```
The package provides a RigidCollection class that can be extended
to get a Collection that works nicely with RigidTypes.

PostCollection, that uses our previously defined Post type:
```php
/**
 * @method Post first(callable $callback = null, $default = null)
 * @method Post get($key, $default = null)
 */
class PostCollection extends RigidCollection
{
    public function targetClass(): string
    {
        return Post::class;
    }
}
```

**targetClass()**: this method controls which type the input
items will be cast to. It MUST be implemented on each collection (but don't worry, the IDE will force you to)

**return type annotations** for get() and first(): these are optional, but more or less the whole point of
the type specific collection. (If you want to avoid annotations, you can also redefine the get() and first() method
with the correct **nullable** return type baked in)

### Or use default collections
If you don't want to create a new Collection class for each type,
you can use the BatchCaster to validate the input, before passing it into a
default collection:
```php
/** @return Collection|Posts[] */
function getPosts(): Collection
{
    $data = Http::get('/posts')->json();
    
    $casted = app(BatchCaster::class)->cast(Product::class, $data);
    
    return collect($data);
}
```
I trust you can figure out the pros and cons of each approach yourself.

## Hardening / Softening

### Nullable Fields
If null values are expected on a field, you **must** declare it:
```php
public ?string     $firstName;
public string|null $lastName;
```

### Ambiguous Fields
Union types work as usual
```php
public string|float $price;
```

### Incomplete Input
If you want to **reject** inputs that do not contain every single field,
add this to your class:
```php
protected bool $allowIncompleteInput = false;
```
This would be useful, if you never want to assume
a `null` without explicitly receiving it, but on the downside, all incomplete
inputs will cause exceptions.

By default, incomplete input is accepted and missing fields will simply be set to null 
(**note that this will still cause an error if the field is not nullable**).

### Undocumented Values
If you do not want unknown input values to be added to your objects automatically,
use this on your class:
```php
protected bool $includeUnknownValues = false;
```
By default, the entire input will be consumed and added to the object,
even if some fields are not declared on the class.

## Advanced Usage
### Nested Types
You can refer to RigidType and RigidCollection definitions within RigidType definitions
```php
// class Address extends RigidType
// class ArticleCollection extends RigidCollection

class Invoice extends RigidType
{
    public int               $id;
    public Address           $address;
    public ArticleCollection $articles;
    public object            $customer;
}
```
note: objects that do not conform to RigidType are cast to the generic `object` instead and must be declared accordingly.

## Mutation testing
- `dc run app bash`
- `apt update && apt install php7.4-pcov -y`
- `php -dpcov.enabled vendor/bin/infection`
