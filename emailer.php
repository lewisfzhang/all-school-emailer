<?php
    error_reporting(E_ERROR | E_PARSE); //doesn't report small errors
    require('PHPMailer/PHPMailerAutoload.php'); //PHPMailer file
    $db = new SQLite3('masterStudent16-17.sqlite3'); //connect

    function sendMail($to, $subject, $message){ //send email
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->Port = 25;
        $mail->CharSet = 'UTF-8';

        //Set initial mail headers
        $mail->From = "carillon@bcp.org";
        $mail->FromName = "The Carillon";
        $mail->AddBCC('carillon@bcp.org');
        $mail->AddAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->IsHTML(true);
                
        return $mail->send(); //will return true if sending worked
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Emailer</title>
        <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css"> <!--W3.CSS stylesheet-->
    </head>
    <body>
        <form class="w3-container" method="post">
            <input type="submit" name="sendEmail" value="Send Email" class="w3-btn w3-theme">
        </form>
        <?php
            if(isset($_POST['sendEmail'])){
                $statement = $db -> prepare('SELECT * FROM master'); 
                $result = $statement->execute();

                //create an array for all of the column values
                $emailArray[] = [];
                $firstNameArray[] = [];

                $surveyURL = 'bit.ly/2bLZQK9'

                while($row = $result->fetchArray(SQLITE3_ASSOC)){
                    array_push($emailArray, $row['email']); //set the values in to the array
                }
                while($row = $result->fetchArray(SQLITE3_ASSOC)){
                    array_push($firstNameArray, $row['First']); //set the values in to the array
                }

                $index = 0; //the current index the emailer is on
                foreach($emailArray as $email){
                    $firstName = $firstNameArray[$index];

                    //send an email
                    $emailMessage = 
                    "Hello $firstName, <br><br>
                    Welcome to Bellarmine! We hope you enjoyed your summer and are having fun with your new Surface. <br><br>

We’re Cameron, Brendan, Rikki, and Matt from The Carillon Yearbook, and we want to share what we’re all about and why you might consider joining us. <br><br>

We’re a close group of over 50 Bells based in the basement of O’Donnell Hall. We occupy two classrooms worth of space in a high-tech office with a TV, a projector, surround-sound audio, microwave, fridge, $50k+ in photography equipment, dual-monitor high-performance workstations, full Adobe Creative Cloud access…the list goes on. <br><br>

We work after school throughout the year to create The Carillon Yearbook . We are nationally recognized by scholastic press associations around the country and win prestigious awards on a yearly basis. <br><br>

If you are interested in experiencing a business-like environment, shooting events with professional camera equipment, writing interesting and fun articles on your fellow Bells, and learning how to design commercial-standard spreads, please don’t miss the opportunity to join what we think is the best place on campus to learn, grow with and showcase these interests. Consider joining The Carillon staff! <br><br>

Please put your name down at this page or reply to this email so we can keep you updated about our recruitment process. You’re not committing to anything by signing up, so you've got nothing to lose. <br>

Come by Andrade theater in the library at lunch or after school on August 30 or September 1 to meet the leadership staff and learn more about the Carillon Yearbook. <br>

We’ll see you soon, <br><br>

Cameron Ormiston, Brendan Lim, Rikki Mukherjee, Matt Lowe <br>
Editors-in-Chief <br>
The Carillon";
 
                    if(sendMail($email, "Your Future at the Carillon", $emailMessage)){ //if mail is sent successfully
                        echo "Mail sent to $email <br>";
                    }
                    else{ //if send fails
                        echo "Oh no! Sending a reminder email has failed! Plase contact <a href='mailto:carillon@bcp.org'>carillon@bcp.org</a> so we can fix the problem.";
                    }
                    $index++; //increment index
                }
            }
        ?>
    </body>
</html>
