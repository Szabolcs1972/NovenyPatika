<?php
//include CUSTOMER CLASS & php to read DATA file customer.txt
include_once 'php/customer.php';
include_once 'php/readDatafile.php';

//get data of the users, we call getData() function with different path of customer.txt as parameter
$users = getData("customer.txt");

//some service codes, to see what's happening :)
//--------------------------------------------------
//print_r($users);

/*
for ($i = 0; $i < (count($users)-1); $i++) {
    print $users[$i]->getUser()."<br>";
    //print "<p>" . $i . "</p><br>";
}
*/
//---------------------------------------------------

// REGISTRATION
$error = "";                    // registration error message
$isFormOK = true;               // form is ready to save


if (isset($_POST['registration'])) {   // registration button has been pressed
    if (!isset($_POST['fullName']) || trim($_POST['fullName']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtad meg a nevedet!<br>";
    }
    if (!isset($_POST['user']) || trim($_POST['user']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtál meg felhasználó nevet!<br>";
    }
    if (!isset($_POST['pw']) || trim($_POST['pw']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtál meg jelszót!<br>";
    }
    if (!isset($_POST['pwAgain']) || trim($_POST['pwAgain']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Ismételd meg a jelszót!!<br>";
    }
    if ($_POST['pw'] !== $_POST['pwAgain']) {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> A két jelszó nem egyezik meg!<br>";
    }
    if (!isset($_POST['birth']) || $_POST['birth'] === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtad meg a születési dátumot!<br>";
    }
    if (!isset($_POST['email']) || trim($_POST['email']) === "") {
        $isFormOK = false;
        $error .= "<strong>Hiba!</strong> Nem adtál meg e-mail cimet!!<br>";
    }

    //somehow we have a last empty line in the array of the Customer objects after the read of the file
    //don't try to apply Customer class functions, it is causing Fatal error...
    for ($j = 0; $j < (count($users) - 1); $j++) {
        if ($_POST['user'] === $users[$j]->getUser()) {
            $isFormOK = false;
            $error .= "<strong>Hiba!</strong> Ez a felhasználónév már létezik!<br>";
            break;
        } elseif ($_POST['email'] === $users[$j]->getEmail()) {
            $isFormOK = false;
            $error .= "<strong>Hiba!</strong> Ez az email cím már létezik!<br>";
            break;
        }
    }


    ($isFormOK) ? $error .= "A regisztráció rendben van!<br>" : $error .= "A regisztráció sikertelen!<br>";
    if ($isFormOK) {

        $path = "pics/" . $_FILES['avatar']['name'];

        if ($_FILES['avatar']['name'] !== "" && str_contains($_FILES['avatar']['type'],"image")) {

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
                $error .= "A fájl sikeresen átmozgatásra került!<br>";
            } else {
                $error .= "Hiba történt a fájl átmozgatása során!<br>";
            }
        }

        $customer = new customer($_POST['fullName'], $_POST['user'], password_hash($_POST['pw'], PASSWORD_DEFAULT), $_POST['birth'], $_POST['email'], $_POST['firm'], $_POST['area'], $path, $_POST['comment']);
        try {
            $file = fopen('customer.txt', "a");
        } catch (Exception $exception) {
            $error .= $exception.getMessage()."<br>";
        }

        fwrite($file, serialize($customer) . "\n");
        fclose($file);
    }


}

// LOGIN
$isUserNameOK = false;
$isEmailOK = false;
$isPassWordOK = false;
$loginError = "";

if (isset($_POST['login'])) {
    //somehow we have a last empty line in the array of the Customer objects after the read of the file
    //don't try to apply Customer class functions, it is causing Fatal error...
    if ($_POST['username'] !== "") {
        if ($_POST['email'] !== "") {
            if ($_POST['passwd'] !== "") {

        for ($j = 0; $j < (count($users) - 1); $j++) {
            //$loginError .= $users[$j]->getUser()."<br>";
            if ($_POST['username'] === $users[$j]->getUser()) {
                $isUserNameOK = true;
                if (password_verify($_POST['passwd'],$users[$j]->getPw())) {
                    $isPassWordOK = true;
                    if ($_POST['email'] === $users[$j]->getEmail()) {
                        $isEmailOK = true;

                        session_start();
                        $_SESSION['user'] = [0=>$_POST['username'],1=>$_POST['email'],2=>$users[$j]->getAvatar(),3=>$j];
                        header("Location: /php/logged.php");

                    }

                }

            }

        }
        if (!$isUserNameOK) $loginError .= "<strong>Hiba!</strong> Nem megfelelő a felhasználónév!<br>";
        if (!$isPassWordOK) $loginError .= "<strong>Hiba!</strong> Nem megfelelő a jelszó!<br>";
        if (!$isEmailOK) $loginError .= "<strong>Hiba!</strong> Nem megfelelő az e-mail cím!<br>";

            } else {
                $loginError .= "<strong>Hiba!</strong> Nem írtad be a jelszavadat!<br>";
            }
    } else {
        $loginError .= "<strong>Hiba!</strong> Nem írtad be az email címedet!<br>";
    }

} else {
        $loginError .= "<strong>Hiba!</strong> Nem írtad be a felhasználónevedet!<br>";
    }
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
    <link rel="icon" href="pics/icon.jpg"/>
    <link rel="stylesheet" href="css/styles.css">
    <title>Növénypatika</title>
</head>
<body>

<div id="header" class = "slider">
    <h1>Növénypatika</h1>
</div>


<div class="row">
    <div class="col-3 col-s-4 navigation">
        <nav>


        <ul>
            <li>
                <a href="index.html" class = "activePage">Kezdőlap</a>
            </li>
            <li>
                <a href="html/redpepper.html">Fűszerpaprika</a>
            </li>
            <li>
                <a href="html/peach.html">Őszibarack</a>
            </li>
            <li>
                <a href="html/rose.html">Rózsa</a>
            </li>
            <li>
                <a href="html/gerbera.html">Gerbera</a>
            </li>
            <li>
                <a href="html/tomato.html">Paradicsom</a>
            </li>
        </ul>
        </nav>
    </div>

<!--
    <div id="nav">

    </div>
-->
<div class="col-5 col-s-8">
        <main>
        <section>
        <h2 id="elso">Cégünkről</h2>
        <img class="logo" alt="A Növénypatika logója" src="pics/icon.jpg"/>
        <p>Morbi pretium tincidunt suscipit. Integer non felis neque. Ut posuere facilisis ligula sed ullamcorper. Ut tempor sit amet ipsum vestibulum tincidunt. Etiam fringilla tincidunt lectus, sit amet maximus eros accumsan vitae. Nullam sit amet dictum nibh. Phasellus mollis venenatis nulla, a porta ligula molestie nec. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec commodo, elit nec auctor sollicitudin, lacus lectus placerat velit, id sodales nulla dolor molestie nibh. Sed elit orci, iaculis vitae justo eget, consectetur ullamcorper dolor. Donec massa quam, finibus sit amet magna vel, tincidunt rhoncus mi. Integer bibendum ac nibh non dapibus. Curabitur sollicitudin imperdiet libero, id condimentum dolor facilisis eget. Cras eget placerat elit. Curabitur non turpis nec neque pulvinar consectetur. Etiam hendrerit ante eget viverra dapibus.</p>
        <p>Duis at interdum eros. Aliquam vel magna vehicula est tincidunt tristique quis ut odio. Morbi viverra consectetur tristique. Maecenas ut arcu ante. Praesent tempor, justo quis egestas tincidunt, odio mi sodales nibh, eget accumsan sem magna id arcu. Curabitur eu aliquet nunc. Phasellus et velit turpis. Sed placerat eros eu urna cursus bibendum. Aenean venenatis lacinia orci, eu ullamcorper nisi commodo vel. Maecenas ut condimentum mauris.</p>

        <p>Mauris lobortis facilisis finibus. Mauris efficitur purus diam, eu mollis augue sodales et. Sed id turpis eros. Fusce non imperdiet orci. Suspendisse at sagittis metus. In orci purus, varius varius feugiat ut, faucibus et lectus. Cras ante felis, ultricies sed vehicula et, ultricies ac sem.</p>
        </section>
        <article>
        <h2>Növényvédőszer kereső</h2>
        <form action="" method="post">
            <fieldset>
            <!--
                <legend>Növényvédőszer kereső</legend>
            -->
                <label for="crop" id="crop-label">Kultúra:</label><br/>
                <select name="crop" id="crop">
                    <option value="redpepper">Fűszerpaprika</option>
                    <option value="peach">Őszibarack</option>
                    <option value="rose">Rózsa</option>
                    <option value="tomato" selected>Paradicsom</option>
                </select>
                <hr/>
                <span id="radio-pesticides">Szerek típusa:</span><br/>
                <label for="fungicid">Gombaölőszer</label>
                <input type="radio" id="fungicid" name="fungicid" value="fungicid"/><br/>
                <label for="insecticid">Rovarölőszer</label>
                <input type="radio" id="insecticid" name="insecticid" value="insecticid" checked/><br/>
                <label for="herbicid">Gyomirtószer</label>
                <input type="radio" id="herbicid" name="herbicid" value="herbicid"/> <br/>
                <hr/>
                <input type="submit" name="szerkereso" value="Keres"/>

            </fieldset>
        </form>
        </article>
        <article>
            <h2>Ctamsclart</h2>
            <p>Morbi pretium tincidunt suscipit. Integer non felis neque. Ut posuere facilisis ligula sed ullamcorper. Ut tempor sit amet ipsum vestibulum tincidunt. Etiam fringilla tincidunt lectus, sit amet maximus eros accumsan vitae. Nullam sit amet dictum nibh. Phasellus mollis venenatis nulla, a porta ligula molestie nec. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Donec commodo, elit nec auctor sollicitudin, lacus lectus placerat velit, id sodales nulla dolor molestie nibh. Sed elit orci, iaculis vitae justo eget, consectetur ullamcorper dolor. Donec massa quam, finibus sit amet magna vel, tincidunt rhoncus mi. Integer bibendum ac nibh non dapibus. Curabitur sollicitudin imperdiet libero, id condimentum dolor facilisis eget. Cras eget placerat elit. Curabitur non turpis nec neque pulvinar consectetur. Etiam hendrerit ante eget viverra dapibus.</p>
            <p>Duis at interdum eros. Aliquam vel magna vehicula est tincidunt tristique quis ut odio. Morbi viverra consectetur tristique. Maecenas ut arcu ante. Praesent tempor, justo quis egestas tincidunt, odio mi sodales nibh, eget accumsan sem magna id arcu. Curabitur eu aliquet nunc. Phasellus et velit turpis. Sed placerat eros eu urna cursus bibendum. Aenean venenatis lacinia orci, eu ullamcorper nisi commodo vel. Maecenas ut condimentum mauris.</p>
        </article>
        <hr/>
        </main>
</div>

<div class="col-4 col-s-12">
<div class="aside">
    <aside>
    <address>Cím: Szeged, Dorozsmai út. X.</address>
    <p>Tel: 62-457-893</p>
    <p>novenypatika@novenypatika.eu</p>

    <h2>Jelentkezz be, vagy regisztrálj!</h2>
    <form action="" method="post">
        <fieldset>
            <legend>Bejelentkezés:</legend>
            <label for="username">Felhasználó név (*):</label><br/>
            <input type="text" id="username" name="username" value="<?php if (isset($_POST['username'])) echo $_POST['username'];?>"/><br/><br/>
            <label for="email">E-mail cím (*):</label><br/>
            <input type="email" id="email" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];?>"/><br/><br/>
            <label for="passwd">Jelszó (*):</label><br/>
            <input type="password" id="passwd" name="passwd"/><br/><br/>
            <input type="submit" name="login" value="Bejelentkezés"/>
        </fieldset>
        <p class="error"><?php echo $loginError;
        /*
        print "<pre>";
        print_r($_POST);
        print_r($_FILES);
        print "</pre>";*/
            ?></p>
    </form>

        <hr/>
    <form action="" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>Regisztráció</legend>
            <label for="fullName">Név:</label><br/>
            <input type="text" id="fullName" name="fullName" size="20" value="<?php if (isset($_POST['fullName'])) echo $_POST['fullName'];?>"/><br/><br/>
            <label for="user">Felhasználónév (*):</label><br/>
            <input type="text" id="user" name="user" value="<?php if (isset($_POST['user'])) echo $_POST['user'];?>"/><br/><br/>
            <label for="pw">Jelszó (*):</label><br/>
            <input type="password" id="pw" name="pw" value="<?php if (isset($_POST['pw'])) echo $_POST['pw'];?>"/><br/><br/>
            <label for="pwAgain">Jelszó ismét (*):</label><br/>
            <input type="password" id="pwAgain" name="pwAgain" value="<?php if (isset($_POST['pwAgain'])) echo $_POST['pwAgain'];?>"/><br/><br/>
            <label for="birth">Születési dátum (*):</label><br/>
            <input type="date" id="birth" name="birth" min="1910-01-01" value="<?php if (isset($_POST['birth'])) echo $_POST['birth'];?>"/><br/><br/>
            <label for="mail">E-mail (*):</label><br/>
            <input type="email" id="mail" name="email" value="<?php if (isset($_POST['email'])) echo $_POST['email'];?>"/><br/><br/>


        <label for="education">Növényvédelmi végzettség:</label><br/>
        <select id="education">
            <option value="white">Fehérkönyves (I.)</option>
            <option value="green">Zöldkönyves (II.)</option>
            <option value="none" selected>Nincs (III.)</option>
        </select><br/><br/><hr/><br/>

       Cégforma<br/>
        <label for="op1">Őstermelő:</label>
        <input type="radio" id="op1" name="firm" value="farmer"/><br/>
        <label for="op2">Kft., Bt.:</label>
        <input type="radio" id="op2" name="firm" value="enterprise"/><br/>
        <label for="op3">Magánszemély:</label>
        <input type="radio" id="op3" name="firm" value="individual" checked/> <br/>
            <br/><br/><hr/><br/>
        Ágazat<br/>
        <label for="arable">Szántóföld:</label>
        <input type="checkbox" id="arable" name="arable" value="arable"/><br/>
        <label for="garden">Kertészet:</label>
        <input type="checkbox" id="garden" name="garden" value="garden"/><br/>
        <label for="forestry">Erdészet:</label>
        <input type="checkbox" id="forestry" name="forestry" value="forestry"/><br/><br/>
            <label for="area">Gazdaság mérete (ha):</label><br/>
            <input type="number" id="area" name="area"/><br/><br/>
            <br/><br/><hr/><br/>
        <label>Profilkép feltöltése:</label><br/><br/>
        <input type="file" name="avatar"/><br/><br/>

        <label for="comment">Megjegyzés<br/>(max. 150 karakter):</label> <br/>
        <textarea id="comment" name="comment" maxlength="150"></textarea> <br/>
            <br/><br/><hr/><br/>
        <input type="reset" name="reset-btn" value="Adatok törlése"/>
        <input type="submit" name="registration" value="Regisztráció"/>
        </fieldset>
        <p class="error"><?php echo $error;
        /*
        print "<pre>";
        print_r($_POST);
        print_r($_FILES);
        print "</pre>"; */
            ?></p>
    </form>
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