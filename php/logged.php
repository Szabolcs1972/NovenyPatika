<?php
session_start();
//prevent go back after logout
if (!isset($_SESSION['user'])) header("Location: ../index.php");

/* INFORMATION - the values of $_SESSION array from index.php
//$_SESSION['user'] = [0=>$_POST['username'],1=>$_POST['email'],2=>$users[$j]->getAvatar(),3=>$j];
*/

//number of the logged-in user in the $users[] array, it is easier to write...
$userNumber = $_SESSION['user'][3];
//print $userNumber;

//print the number of the user in the data file, if you would like to see
//$message = "Sikeres bejelentkezés!".$_SESSION['user'][3]."<br>";

//$message variable to display some messages for the user
$message = "Sikeres bejelentkezés!"."<br>";

//$error variable to display error messages
$error = "";

//print $_SESSION values to check or see
//print_r($_SESSION);

//include CUSTOMER CLASS & php to read DATA file customer.txt
include_once 'customer.php';
include_once 'readDatafile.php';

//get data of the users, we call getData() function with different path as parameter
//the path of customer.txt is different from here

$users = getData("../customer.txt");
//print_r($users);
//print $users[$userNumber]->getUser();


//$profilePicPath is storing the path for profile pictures
$profilePicPath = "../".$_SESSION['user'][2];

//if you would like to see the variable....
//$message .= $profilePicPath;

//if no profile picture has been selected, we use nobody.jpg as default
//profile picture path string should be longer than 5 characters of "/pics"

if (strlen($_SESSION['user'][2]) < 6) $profilePicPath = "../pics/nobody.jpg";
//$profilePicPath = "../pics/nobody.jpg";
//$message .= $profilePicPath;


//MODIFY USER DATA AND PROFILE IMAGE
$isFormOK = true;
$isModifyOK = false;

if (isset($_POST['modify'])) {   //modify button has been pressed

    if (!isset($_POST['user']) || trim($_POST['user']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtad meg a felhasználó nevet, vagy az új felhasználó nevedet!<br>";
    }
    if (!isset($_POST['pw']) || trim($_POST['pw']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtál meg jelszót, vagy az új jelszavad!<br>";
    }
    if (!isset($_POST['pwAgain']) || trim($_POST['pwAgain']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Ismételd meg a jelszót!<br>";
    }
    if ($_POST['pw'] !== $_POST['pwAgain']) {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> A két jelszó nem egyezik meg!<br>";
    }
    if (!isset($_POST['email']) || trim($_POST['email']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtad meg az új, vagy a régi e-mail cimedet!!<br>";
    }

    //to modify the username
        if ($_POST['user'] !== $users[$userNumber]->getUser()) {
            $users[$userNumber]->setUser($_POST['user']);
            $isModifyOK = true;
            $message .= "A felhasználónév módosult!<br>";

        }
    //to modify the password
    if (!password_verify($_POST['pw'],$users[$userNumber]->getPw()) && !password_verify($_POST['pwAgain'],$users[$userNumber]->getPw())) {
        $users[$userNumber]->setPw(password_hash($_POST['pw'], PASSWORD_DEFAULT));
        $isModifyOK = true;
        $message .= "A jelszó módosult!<br>";

    }

    //to modify the email
    if ($_POST['email'] !== $users[$userNumber]->getEmail()) {
        $users[$userNumber]->setEmail($_POST['email']);
        $isModifyOK = true;
        $message .= "Az email cím módosult!<br>";

    }



    ($isFormOK) ? $error .= "Az adatok rendben vannak!" : $error .= "Az adatok hibásak!";
    if ($isFormOK) {

        $path = "../pics/" . $_FILES['avatar']['name'];

        if ($_FILES['avatar']['name'] !== "" && in_array("image", $_FILES['avatar']) && $_FILES['avatar']['name'] !==  $users[$userNumber]->getAvatar()) {

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
                $users[$userNumber]->setAvatar($path);
                $message .= "A profil képedet kicseréltük!<br>";
                $isModifyOK = true;
            } else {
                $error .= "Hiba történt a fájl átmozgatása során!<br>";
            }
        }
        if ($isModifyOK) {
            try {
                $file = fopen('../customer.txt', "w");
            } catch (Exception $exception) {
                $error .= $exception.getMessage()."<br>";
            }
            for ($i = 0; $i < (count($users)-1); $i++) {
                fwrite($file,serialize($users[$i]) . "\n");
            }

            fclose($file);
        }
    }


}
if (isset($_POST['delete'])) {   //delete user button has been pressed
    $users[$userNumber]->setFullName("");
    $users[$userNumber]->setUser("");
    $users[$userNumber]->setPw("");
    $users[$userNumber]->setBirth("");
    $users[$userNumber]->setEmail("");
    $users[$userNumber]->setFirm("");
    $users[$userNumber]->setArea("");
    $users[$userNumber]->setAvatar("");
    $users[$userNumber]->setComment("");

    try {
        $file = fopen('../customer.txt', "w");
    } catch (Exception $exception) {
        $error .= $exception.getMessage()."<br>";
    }
    for ($i = 0; $i < (count($users)-1); $i++) {
        fwrite($file,serialize($users[$i]) . "\n");
    }

    fclose($file);

    unset($users);
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Location: ../index.php");

}

if (isset($_POST['quit'])) {   //quit button has been pressed
    //print $message .= "Kijelentkezés gomb aktív"."<br>";
    //print $error .= "Kijelentkezés gomb aktív"."<br>";

    unset($users);
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Location: ../index.php");
}


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
                <form action="" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <legend>Felhasználói adatok módosítása:</legend>
                        <label for="username">Új felhasználó név:</label><br/>
                        <input type="text" id="username" name="user" value="<?php print $_SESSION['user'][0];?>"/><br/><br/>
                        <label for="email">E-mail cím:</label><br/>
                        <input type="email" id="email" name="email" value="<?php print $_SESSION['user'][1];?>"/><br/><br/>
                        <label for="passwd">Jelszó:</label><br/>
                        <input type="password" id="passwd" name="pw"/><br/><br/>
                        <label for="pwAgain">Jelszó ismét:</label><br/>
                        <input type="password" id="pwAgain" name="pwAgain"/><br/><br/>
                        <label>Profilkép feltöltése:</label><br/><br/>
                        <label>A jelenlegi profil képed: <?php print $_SESSION['user'][2];?></label><br/><br/>
                        <input type="file" name="avatar"/><br/><br/>
                        <input type="submit" name="modify" value="Fiók adatainak módosítása"/>
                    </fieldset>
                    <p class="error"><?php echo $error;
                    /*
                    print "<pre>";
                    print_r($_POST);
                    print_r($_FILES);
                    print "</pre>";*/
                    ?></p>
                </form>
                <hr/>
                    <form action="" method="post">
                        <fieldset>
                            <legend>Felhasználói fiók törlése:</legend>
                            <label for="username">Felhasználó név:</label><br/>
                            <input type="text" id="username" name="user" value="<?php print $_SESSION['user'][0];?>"/><br/><br/>
                            <label for="email">E-mail cím:</label><br/>
                            <input type="email" id="email" name="email" value="<?php print $_SESSION['user'][1];?>"/><br/><br/>
                            <label for="passwd">Jelszó:</label><br/>
                            <input type="password" id="passwd" name="pw"/><br/><br/>
                            <label for="pwAgain">Jelszó ismét:</label><br/>
                            <input type="password" id="pwAgain" name="pwAgain"/><br/><br/>
                            <input type="submit" name="delete" value="Fiók törlése"/>
                        </fieldset>
                        <p class="error"><?php echo $error;
                            /*
                            print "<pre>";
                            print_r($_POST);
                            print_r($_FILES);
                            print_r($users[$userNumber]);
                            print "</pre>";*/
                            ?></p>
                    </form>
                    <hr/>
                    <form action="" method="post">
                        <fieldset>
                            <legend>Kijelentkezés:</legend>
                            <input type="submit" name="quit" value="Kijelentkezés"/>
                        </fieldset>
                        <p class="error"><?php echo $error;
                            /*
                            print "<pre>";
                            print_r($_POST);
                            print_r($_FILES);
                            print "</pre>";*/
                            ?></p>
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
