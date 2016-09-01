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
                $statement = $db -> prepare('SELECT StudentEmail, StudFirstName FROM master'); 
                $result = $statement->execute();

                //create an array for all of the column values
                $emailArray[] = [];
                $firstNameArray[] = [];

                $surveyURL = 'https://goo.gl/forms/ZZL52PwWKb8afWat2';
                $emailSubject = 'The Startup on Campus';

                while($row = $result->fetchArray(SQLITE3_ASSOC)){
                    array_push($emailArray, $row['StudentEmail']); //set the values in to the array
                    array_push($firstNameArray, $row['StudFirstName']); //set the values in to the array
                }

                //take off extra first element
                unset($emailArray[0]);
                unset($firstNameArray[0]);

                $index = 0; //the current index the emailer is on
                foreach($emailArray as $email){
                    $email = $emailArray[$index]; //'Kevin.Gottlieb19@bcp.org'; //
                    $firstName = $firstNameArray[$index]; //'Kevin'; //

                    //send an email
                    $emailMessage = 
                    "Hi $firstName, <br><br>
                    I hope you enjoyed your summer and are having a good time figuring out the Surface. <br><br>
 
I’m Kevin Gottlieb from the newest startup right here on campus: The Carillon Yearbook Software and Technology Team! <br><br>

Last year was our team’s very first year as a staff on the yearbook and this year we have ambitious goals set out to both impact the Carillon’s internal operations and Bellarmine as a whole. At such an early stage, each of you that decides to join will have an enormous impact on how you want to help the Carillon and our school. <br><br>

We work out of a high-tech office in the basement of O’Donnell Hall occupying two classrooms worth of space with a TV, dual-monitor high-performance workstations, a projector, surround-sound audio, a microwave, a fridge, and much more! You’ll find yourself in a professional programming environment that looks conspicuously like that of many of the hottest unicorns in the Valley. Each project gives you a chance to make your school and your yearbook better as you work alongside creative designers, photographers, and journalists. <br><br>

Many of the projects we completed last year were web apps utilizing languages such as PHP, SQL, JavaScript, JQuery, and a litany of open-source, professional libraries. This year, we’re looking to branch out into mobile development, as well. <br><br>

However, no need to fear if you don’t have any programming experience! Some is preferred, but hard work and initiative will allow you to gain all of the experience you need through your projects. Also, we will meet during lunch, so no need to worry about after school conflicts. <br><br>

Please consider joining the Carillon Software and Technology family! <br><br>

 
If you're interested, please put your name down <a href=$surveyURL>at this page</a> or reply to this email so we can keep you updated about our recruitment process. You’re not committing to anything by signing up, so you've got nothing to lose. <br><br>
 
Come by Andrade theater in the library at lunch or after school on <strong>Thursday, September 1</strong> to meet the leadership staff and learn more about the Carillon Yearbook. <br><br>
 
We will also be open during Back to School Night if you would like to check our office out. <br><br>
 
See you soon, <br><br>
 
Kevin Gottlieb ‘19 <br>
Director of Technology <br>
The Carillon";
 
                    if(sendMail($email, $emailSubject, $emailMessage)){ //if mail is sent successfully
                        echo "Mail sent to $firstName at $email <br>";
                    }
                    else{ //if send fails
                        echo "Oh no! Sending a reminder email to $email has failed! Plase contact <a href='mailto:carillon@bcp.org'>carillon@bcp.org</a> so we can fix the problem.";
                    }
                    $index++; //increment index
                }
            }
        ?>
    </body>
</html>