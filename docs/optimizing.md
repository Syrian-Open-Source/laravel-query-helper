Optimizing
-----------
In some databases, you can't do any process that require more than 65K of parameters,
so we have to chunk your large query to smaller pieces, and we can do that for you ar effective way.
```php
    // Suppose we have a group of users, let say's we have 100k items to insert.
    $users = [
    ['name' => 'example 1'],
    ['name' => 'example 2'],
    ['name' => 'example 3'],
    ['name' => 'example 4'],
    ...
    ];   
    QueryHelperFacade::updateInstance()
        ->setAllowedWhereInQueryNumber(2000) // chunk size and you can update the default value from query_helper.php config file.
        ->checkIfQueryAllowed($users , function ($data){
            User::insert($data);
        });
```
