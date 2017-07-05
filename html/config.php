function connect_db()
{
    $con=mysqli_connect("[host]","[user]","[pass]","[db]");
    if (mysqli_connect_errno()) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }
    return $con;
}
