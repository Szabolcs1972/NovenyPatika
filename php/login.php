<?php
$message = "Sikeres bejelentkezés!"."<br>";
session_start();
include_once 'customer.php';

// READ USER DATAFILE
$users = [];
$file = fopen("customer.txt","r");

/* $i = 0;
$egy = unserialize(fgets($file)); */

while (!feof($file)) {
    $users[] = unserialize(fgets($file));
    //print_r(unserialize(fgets($file)));
    //print $i;
    //$i++;
}

fclose($file);

//print_r($_SESSION);
$profilePicPath = $_SESSION['user'][2];
if ($profilePicPath == "") $profilePicPath = "../pics/nobody.jpg";
//$profilePicPath = "../pics/nobody.jpg";
//$message .= $profilePicPath;
?>

<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Lovai Dániel Ákos, Dr. Szlávik Szabolcs"/>
    <meta name="description" content="Növénypatika"/>
    <meta name="keywords" content="kártevők, betegségek, permetszerek"/>
    <link rel="icon" href="../pics/icon.jpg"/>
    <link rel="stylesheet" href="../css/styles.css">
    <title>Profilom</title>
</head>

<body>
<div id="header" class = "slider">
    <h1>Növénypatika</h1>
</div>

    <div class="col-6 col-s-8">
        <main>
            <section>
                <h2 id="elso">Rendelés</h2>
                <p>Morbi pretium tincidunt suscipit. Integer non felis neque. Ut posuere facilisis ligula sed ullamcorper. Ut tempor sit amet ipsum vestibulum tincidunt. Etiam fringilla tincidunt lectus, sit amet maximus eros accumsan vitae. Nullam sit amet dictum nibh. Phasellus mollis venenatis nulla, a porta ligula molestie nec. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec commodo, elit nec auctor sollicitudin, lacus lectus placerat velit, id sodales nulla dolor molestie nibh. Sed elit orci, iaculis vitae justo eget, consectetur ullamcorper dolor. Donec massa quam, finibus sit amet magna vel, tincidunt rhoncus mi. Integer bibendum ac nibh non dapibus. Curabitur sollicitudin imperdiet libero, id condimentum dolor facilisis eget. Cras eget placerat elit. Curabitur non turpis nec neque pulvinar consectetur. Etiam hendrerit ante eget viverra dapibus.</p>
                <p>Duis at interdum eros. Aliquam vel magna vehicula est tincidunt tristique quis ut odio. Morbi viverra consectetur tristique. Maecenas ut arcu ante. Praesent tempor, justo quis egestas tincidunt, odio mi sodales nibh, eget accumsan sem magna id arcu. Curabitur eu aliquet nunc. Phasellus et velit turpis. Sed placerat eros eu urna cursus bibendum. Aenean venenatis lacinia orci, eu ullamcorper nisi commodo vel. Maecenas ut condimentum mauris.</p>

                <p>Mauris lobortis facilisis finibus. Mauris efficitur purus diam, eu mollis augue sodales et. Sed id turpis eros. Fusce non imperdiet orci. Suspendisse at sagittis metus. In orci purus, varius varius feugiat ut, faucibus et lectus. Cras ante felis, ultricies sed vehicula et, ultricies ac sem.</p>
            </section>

        </main>
    </div>

    <div class="col-6 col-s-4">
        <div class="aside">
            <aside>
                <h2>Profilom</h2>
                <?php echo $message;
                /*
                print "<pre>";
                print_r($_POST);
                print_r($_FILES);
                print "</pre>";*/
                ?>
                <div class="responsive"></div>
                <img src="<?php echo $profilePicPath;?>" width=100% height=auto>
                </div>
                <div class="aside">
                <form action="" method="post">
                    <fieldset>
                        <legend>Felhasználói adatok módosítása:</legend>
                        <label for="username">Új felhasználó név:</label><br/>
                        <input type="text" id="username" name="username" value="<?php print $_SESSION['user'][0];?>"/><br/><br/>
                        <label for="email">E-mail cím:</label><br/>
                        <input type="email" id="email" name="email" value="<?php print $_SESSION['user'][1];?>"/><br/><br/>
                        <label for="passwd">Jelszó:</label><br/>
                        <input type="password" id="passwd" name="passwd"/><br/><br/>
                        <label for="pwAgain">Jelszó ismét:</label><br/>
                        <input type="password" id="pwAgain" name="pwAgain"/><br/><br/>
                        <label>Profilkép feltöltése:</label><br/><br/>
                        <label>Jelenlegi kép: <?php print $_SESSION['user'][2];?></label><br/><br/>
                        <input type="file" name="avatar"/><br/><br/>
                        <input type="submit" name="login" value="Módosítás!"/>
                    </fieldset>

                </form>

                </div>
            </aside>
        </div>
    </div>
</div>

<div class="footer">
    Kulcsszavak: <b>rovarok</b>, <b>betegségek</b>, <b>növényvédő szerek</b>
    <br/>
    <span>Lovai & Szlávik, 2023<br/> CC0 1.0</span>
</div>




</body>



</html>
