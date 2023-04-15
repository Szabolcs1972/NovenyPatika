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

//$error variable to display error messages
$error = "";

//$save - show message to save profile visibility settings
$save = "";

//print $_SESSION values to check or see
//print_r($_SESSION);

//include CUSTOMER CLASS & php to read DATA file customer.txt
include_once 'customer.php';
include_once 'readDatafile.php';

// variables to display visible user information according to settings in visibility.txt
$userNameTable = "";
$userEmailTable = "";
$userProfilePicTable = "";
$userCommentTable = "";

//load visibility information according to settings of the users from visibility.txt
//we call getData() function of readDatafile.php

$visibilitySettings = getData("../visibility.txt");

foreach ($visibilitySettings as $visible) {
    //print $visible."<br>";
    $getVisibilitySettings[] = explode(",",$visible);
}
//print_r($visibilitySettings);

//get data of the users, we call getData() function with different path as parameter
//the path of customer.txt is different from here

$users = getData("../customer.txt");
//print_r($users);
//print $users[$userNumber]->getUser();


//$message variable to display some messages for the user
$message = "Sikeres bejelentkezés!<br>".$users[$userNumber]->getUser()."<br>";


//generate profile picture path array for all customer

$profilePicPaths = [];
for ($k = 0; $k < (count($users)-1) ; $k++) {

    //if no profile picture has been selected, we use nobody.jpg as default
    //profile picture path string should be longer than 5 characters of "/pics"

    if (strlen($users[$k]->getAvatar() === "pics/")) $profilePicPaths[$k] = "../pics/nobody.jpg";
    else {
        $profilePicPaths[$k] = "../".$users[$k]->getAvatar();
    }
}



//MODIFY USER DATA AND PROFILE IMAGE
$isFormOK = true;
$isModifyOK = false;

if (isset($_POST['modify']) && ($_POST['modify'] === "Fiók adatainak módosítása")) {   //modify button has been pressed

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

        /*
        $message .= $path."<br>";
        $message .= $_FILES['avatar']['name']."<br>";
        str_contains($_FILES['avatar']['type'],"image") ? $message .= "Igaz<br>" : $message.= "Hamis<br>";
        ($_FILES['avatar']['name'] !==  $users[$userNumber]->getAvatar()) ? $message .= "Igaz<br>" : $message.= "Hamis<br>";
         */

        if ($_FILES['avatar']['name'] !== "" && str_contains($_FILES['avatar']['type'],"image") && $_FILES['avatar']['name'] !==  $users[$userNumber]->getAvatar()) {
            $message .= "Igaz"."<br>";


            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $path)) {
                $message .= "Ez is igaz"."<br>";
                $users[$userNumber]->setAvatar($path);
                $message .= "A profil képedet kicseréltük! (A következő bejelentkezésnél látszik!)<br>";
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
            $message .= "A fájl bezárása megtörtént!<br>";
        }
    }


}
if (isset($_POST['delete']) && ($_POST['delete'] === "Fiók törlése")) {   //delete user button has been pressed
    //remove the customer object from the $users array
    array_splice($users,$userNumber,1);

    //save

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

if (isset($_POST['quit']) && ($_POST['quit'] === "Kijelentkezés")) {   //quit button has been pressed
    //print $message .= "Kijelentkezés gomb aktív"."<br>";
    //print $error .= "Kijelentkezés gomb aktív"."<br>";

    unset($users);
    unset($_SESSION['user']);
    session_unset();
    session_destroy();
    header("Location: ../index.php");
}

if (isset($_POST['visibilitySetting']) && ($_POST['visibilitySetting'] === "Mentés")) {   //
    /*
    print $message .= "A Mentés gombot megnyomták! ".$userNumber."<br>";
    print_r($_POST)."<br>";
    print "<br>";
    //print $error .= "Kijelentkezés gomb aktív"."<br>";

    print "before"."<br>";

    print_r($getVisibilitySettings[$userNumber]);
    print "<br>";
     */

    if (isset($_POST['visibleUserName']) && ($_POST['visibleUserName'] == "true")) {
        //print "visibleUsername - átadva"."<br>";
        //print "<br>";
        $getVisibilitySettings[$userNumber][0] = 1;
    } else {$getVisibilitySettings[$userNumber][0] = 0;}

    if (isset($_POST['visibleEmail']) && ($_POST['visibleEmail']  == "true")) {
        //print "visibleEmail - átadva"."<br>";
        //print "<br>";
        $getVisibilitySettings[$userNumber][1] = 1;
    } else {$getVisibilitySettings[$userNumber][1] = 0;}

    if (isset($_POST['visibleProfilePic']) && ($_POST['visibleProfilePic']  == "true")) {
        //print "visibleProfilePics - átadva"."<br>";
        //print "<br>";
        $getVisibilitySettings[$userNumber][2] = 1;
    } else {$getVisibilitySettings[$userNumber][2] = 0;}

    if (isset($_POST['visibleComment']) && ($_POST['visibleComment']) == "true") {
        //print "visibleComment - átadva"."<br>";
        //print "<br>";
        $getVisibilitySettings[$userNumber][3] = 1;
    } else {$getVisibilitySettings[$userNumber][3] = 0;}

    /*
    print_r($getVisibilitySettings[$userNumber]);
    print "<br>";
    print "after"."<br>";
    */

   //save

   try {
       $file = fopen('../visibility.txt', "w");
   } catch (Exception $exception) {
       $error .= $exception.getMessage()."<br>";
   }
   for ($i = 0; $i < count($users)-1; $i++) {

       fwrite($file,serialize(implode(",",$getVisibilitySettings[$i])) . "\n");
   }



   fclose($file);
    $save .= "A beállításaidat mentettük!"."<br>";
    $save .= ($getVisibilitySettings[$userNumber][0] == 1) ? "A felhasználó neved: Látható"."<br>" : "A felhasználó neved: Nem látható"."<br>";
    $save .= ($getVisibilitySettings[$userNumber][1] == 1) ? "Az e-mail címed: Látható"."<br>" : "Az e-mail címed: Nem látható"."<br>";
    $save .= ($getVisibilitySettings[$userNumber][2] == 1) ? "A profil képed: Látható"."<br>" : "A profil képed: Nem látható"."<br>";
    $save .= ($getVisibilitySettings[$userNumber][3] == 1) ? "A megjegyzésed: Látható"."<br>" : "A megjegyzésed: Nem látható"."<br>";



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

<div class="row">
    <div class="col-8 col-s-8">
        <main>
            <section>
                <h2 id="elso">Felhasználók</h2>
               <table>
                   <thead><tr><th>Sorszám</th><th>Felhasználónév</th><th>E-mail</th><th>Profilkép</th><th>Megjegyzés</th></tr></thead>
                   <tbody>
                   <?php

                   //get visibility settings of users to display in the Users table
                   for ($j = 0; $j < (count($users)-1); $j++) {

                       if ($getVisibilitySettings[$j][0] == 1) {
                           $userNameTable = $users[$j]->getUser();
                       } else {$userNameTable = "";}
                       if ($getVisibilitySettings[$j][1] == 1) {
                           $userEmailTable = $users[$j]->getEmail();
                       } else {$userEmailTable = "";}
                       if ($getVisibilitySettings[$j][2] == 1) {
                           $userProfilePicTable = $profilePicPaths[$j];
                       }  else {$userProfilePicTable = "";}
                       if ($getVisibilitySettings[$j][3] == 1) {
                           $userCommentTable = $users[$j]->getComment();
                       } else {$userCommentTableTable = "";}

                       /*
                       ($getVisibilitySettings[$j][0] == 1) ? print "Igaz"."<br>" : print "Hamis"."<br>";
                       ($getVisibilitySettings[$j][1] == 1) ? print "Igaz"."<br>" : print "Hamis"."<br>";
                       ($getVisibilitySettings[$j][2] == 1) ? print "Igaz"."<br>" : print "Hamis"."<br>";
                       ($getVisibilitySettings[$j][3] == 1) ? print "Igaz"."<br>" : print "Hamis"."<br>";
                       print "<br>";
                       */

                       //print the Users table
                       print "<tr><td>".$j."</td><td>".$userNameTable."</td><td>".$userEmailTable."</td><td><img src='".$userProfilePicTable."' width='80%' height='auto'></td><td>".$userCommentTable."</td></tr>";
                   }

                   ?>
                   </tbody>
               </table>
            </section>

        </main>
    </div>




    <div class="col-4 col-s-4">
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
                <div>
                    <div class="responsive">
                    <img src="<?php echo $profilePicPaths[$userNumber];?>" width=50% height=auto>
                    </div>
                    <div class="aside">
                        <form action="" method="post" enctype="multipart/form-data">
                            <fieldset>
                            <legend>Felhasználói adatok módosítása:</legend>
                            <label for="username">Új/régi felhasználó név (*):</label><br/>
                            <input type="text" id="username" name="user" value="<?php print $_SESSION['user'][0];?>"/><br/><br/>
                            <label for="email">Új/régi e-mail cím (*):</label><br/>
                            <input type="email" id="email" name="email" value="<?php print $_SESSION['user'][1];?>"/><br/><br/>
                            <label for="passwd">Új/régi jelszó (*):</label><br/>
                            <input type="password" id="passwd" name="pw"/><br/><br/>
                            <label for="pwAgain">Új/régi jelszó ismét (*):</label><br/>
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
                            <label for="username">Felhasználó név (*):</label><br/>
                            <input type="text" id="username" name="user" value="<?php print $_SESSION['user'][0];?>"/><br/><br/>
                            <label for="email">E-mail cím (*):</label><br/>
                            <input type="email" id="email" name="email" value="<?php print $_SESSION['user'][1];?>"/><br/><br/>
                            <label for="passwd">Jelszó (*):</label><br/>
                            <input type="password" id="passwd" name="pw"/><br/><br/>
                            <label for="pwAgain">Jelszó ismét (*):</label><br/>
                            <input type="password" id="pwAgain" name="pwAgain"/><br/><br/>
                            <input type="submit" name="delete" value="Fiók törlése"/>
                        </fieldset>
                    </form>
                    <hr/>
                        <hr/>
                        <form action="" method="post">
                            <fieldset>
                                <legend>Adataim láthatóságának beállítása:<br>(Jelöld be, ha látni szeretnéd!)</legend>
                                <label for="visibleUserName">Felhasználó név:</label>
                                <input type="checkbox" id="visibleUserName" name="visibleUserName" value="true"/><br/>
                                <label for="visibleEmail">E-mail:</label>
                                <input type="checkbox" id="visibleEmail" name="visibleEmail" value="true"/><br/>
                                <label for="visibleProfilePic">Profilkép:</label>
                                <input type="checkbox" id="visibleProfilePic" name="visibleProfilePic" value="true"/><br/>
                                <label for="visibleComment">Megjegyzés:</label>
                                <input type="checkbox" id="visibleComment" name="visibleComment" value="true"/><br/>

                                <input type="submit" name="visibilitySetting" value="Mentés"/>
                            </fieldset>
                            <?php echo $save;
                            /*
                            print "<pre>";
                            print_r($_POST);
                            print_r($_FILES);
                            print "</pre>";*/
                            ?>
                        </form>
                    <form action="" method="post">
                        <fieldset>
                            <legend>Kijelentkezés:</legend>
                            <input type="submit" name="quit" value="Kijelentkezés"/>
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
