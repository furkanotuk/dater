# dater

This PHP class enables you to create a simple appointment system for your projects. 
Once you establish the database connection, using the "run" function will create the necessary tables. 
You only need to invoke the "run" function once.


# Example

```

$dater = new Dater();
$dater->setDatabase($database_variable);
$dater->addUser($user_id);
$dater->run();

// You should use this function after run();

$dater->defineTime($user_id,'monday','12:00');

// ...

if($_POST){
  $customer_id = $_POST['customer_id'];
  $user_id = $_POST['user_id'];
  $date = $_POST['date'];
  $dater->book($customer_id,$user_id,$date);
}

```
