<?php
include "lib/functions.php";
require __DIR__ . '/lib/dotenv.php';
require __DIR__ . '/SourceQuery/bootstrap.php';


use DevCoder\DotEnv;
use xPaw\SourceQuery\SourceQuery;

define('SQ_TIMEOUT', 1);
define('SQ_ENGINE', SourceQuery :: SOURCE);

(new DotEnv(".env"))->load();


// Page Generator Time Start Script
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;


// Config to your database - Edit this!
$dbhost = getEnvironmentValue("MYSQL_HOST");            // Server IP/Domain of where the datab-base resides.
$dbdatabase = getEnvironmentValue("MYSQL_DB");            // Data-base Name.
$dbuser = getEnvironmentValue("MYSQL_USER");                // Username.
$dbpassword = getEnvironmentValue("MYSQL_PASSWORD");                    // Password.
$webname = getEnvironmentValue("COMMUNITY_NAME");        // Title of Community/Server/Website/Domain, pick one.
?>
<?php
// MySQL Connect/Query
$connection = new mysqli($dbhost, $dbuser, $dbpassword, $dbdatabase);
if ($connection->connect_error) {
    die("DB Connection failed: " . $connection->connect_error);
}


?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $webname ?></title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="css/global.css?v=<?php echo filemtime("css/global.css"); ?>">
    <link rel="stylesheet" href="https:////cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="headercontent"> <!-- open headercontent -->
<nav class="navbar navbar-dark bg-dark">
  <span class="navbar-brand mb-0 h1">Hafuga Gameserver</span>
</nav>
</div>
  <table class="table table-dark servertable">
        <thead>
        <tr>
            <th>#</th>
            <th>IP Adress</th>
            <th>Server Name</th>
            <th>Gamemode</th>
            <th>Map</th>
            <th>Players</th>
        </tr>
        </thead>
        <tbody>

        <?php
        //Server Source Query
        $query = "SELECT * FROM servers";
        $result = $connection->query($query);
        while ($row = mysqli_fetch_assoc($result)) {

            $fullip = explode(":", $row['IPAddress']);
            $ip = $fullip[0];
            $port = $fullip[1];


            $Timer = MicroTime(true);
            $Query = new SourceQuery();

            $Info = array();
            $Rules = array();
            $Players = array();

            try {
                $Query->Connect($ip, $port, SQ_TIMEOUT, SQ_ENGINE);

                $Info = $Query->GetInfo();
                $Players = $Query->GetPlayers();
            } catch (Exception $e) {
                $Exception = $e;
            }

            $Query->Disconnect();

            ?>
            <tr>
                <td width="30"><img src="css/img/gameicon/gmod.png"/></td>
                <td><a href="steam://connect/<?php echo $ip; ?>:<?php echo $port; ?>"><?php echo $ip; ?>
                        :<?php echo $port; ?></a></td>
                <td><?php echo $row['HostName']; ?></td>
                <td><?php echo htmlspecialchars($Info['ModDesc']); ?></td>
                <td><?php echo htmlspecialchars($Info['Map']); ?></td>
                <td><?php echo htmlspecialchars($Info['Players']) . " / " . htmlspecialchars($Info['MaxPlayers']); ?></td>
            </tr>
        <?php } ?>


        </tbody>
    </table>

<footer class="footer mt-auto py-3 bg-dark">

    <span id="credits" class="text-muted">Landingpage by <a
                href="https://github.com/1day2die">1Day2Die</a></span>

</footer>


</body>

<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
        crossorigin="anonymous"></script>

</html>