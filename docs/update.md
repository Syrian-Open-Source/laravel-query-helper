Update
-----------
Suppose we have a group of users who have an id and a name and we have an array to update each user with a new name 
as in the following example
```shell
    $usersDataBeforeUpdate = [
        ['id'  => 1, 'name' => 'example before update 1'],
        ['id'  => 2, 'name' => 'example before update 2'],
        ['id'  => 3, 'name' => 'example before update 2'],
     ];
    $usersDataToUpdate = [
        ['id'  => 1, 'name' => 'example after update 1'],
        ['id'  => 2, 'name' => 'example after update 2'],
        ['id'  => 3, 'name' => 'example after update 2'],
    ];

    // so the traditional way to that,
    // loop through $usersDataBeforeUpdate and fetch each user with specific (id, name)
    // and update his name with new name
    // and this will execute a query for each record, for the above array, the request will execute 3 queries
    //  what if we want to update 1000 records previously? 
    
    foreah($usersDataToUpdate as $user){
        User::find($user['id'])->update(['name' => $user['name']]);
    }

```
So the query helper will help you optimize this process, see the following explanation:
```php
    $usersDataBeforeUpdate = [
        ['id'  => 1, 'name' => 'example before update 1'],
        ['id'  => 2, 'name' => 'example before update 2'],
        ['id'  => 3, 'name' => 'example before update 2'],
     ];

    $usersDataToUpdate = [
        ['id'  => 1, 'name' => 'example after update 1'],
        ['id'  => 2, 'name' => 'example after update 2'],
        ['id'  => 3, 'name' => 'example after update 2'],
    ];

    $ids = [];
    $values = [];
    $cases = [];
    $tableName = 'users';
    $columnToUpdate = 'name';
    foreach($usersDataToUpdate as $user){

       array_push($ids , $user['id']);

       array_push($values , $user['name']);

       // if you want to set your cases in query.
       $cases[] = "WHEN id = {$user['id']} then ".$user['name'];
    }

    // if you want to set your cases in query.
    $cases = implode(' ', $cases);

    QueryHelperFacade::updateInstance()
            ->setIds($ids)
            ->setValues($values)
            ->setTableName($tableName) // change this parameter value to your database table name.
            ->setField($columnToUpdate) // change this parameter value to your database column name.
            ->bindIdsWithValues()
            ->executeUpdateMultiRows();

    // this will execute only one query.
```
What if you want to put your own Cases ?  **okay we support that**.
```php

    $query = QueryHelperFacade::updateInstance()
            ->setIds($ids)
            ->setCasues($cases)
            ->setTableName($tableName) // change this parameter value to your database table name.
            ->setField($columnToUpdate) // change this parameter value to your database column name.
            ->executeUpdateMultiRows();
```
What if you want dump the query which will execute ?  **okay we support that**.
```php

    $query = QueryHelperFacade::updateInstance()
            ->setIds($ids)
            ->setValues($values)
            ->setTableName($tableName) // change this parameter value to your database table name.
            ->setField($columnToUpdate) // change this parameter value to your database column name.
            ->buildStatement()
            ->getQuery();
    dd($query);

```
What if you want to reduce these lines in one line ?  **okay we support that**.
```php

    $query = QueryHelperFacade::updateInstance()
            ->fastUpdate($tableName , $ids , $values , $columnToUpdate);
    dd($query);

```
