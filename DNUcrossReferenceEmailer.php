<?php
    //DO NOT EVER RUN THIS OR COPY ANY CODE FROM IT, IT CREATES AN INFINITE LOOP
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
        //$mail->AddBCC('kevin.gottlieb19@bcp.org');
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
        <title>DO NOT USE: Parent Emailer</title>
        <link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css"> <!--W3.CSS stylesheet-->
    </head>
    <body>
        <h1>DO NOT EVER RUN THIS OR COPY ANY CODE FROM IT, IT CREATES AN INFINITE LOOP</h1>
        <form class="w3-container" method="post">
            <input type="submit" name="sendEmail" value="Send Email" class="w3-btn w3-theme">
        </form>
        <?php
            if(isset($_POST['sendEmail'])){
                $statement = $db -> prepare('SELECT Email FROM rosterEmails;'); 
                $result = $statement->execute();

                //create an array for all of the column values
                $emailArray[] = [];
                $contactEmailArray[] = [];

                $emailSubject = 'Back to School Night';

                while($row = $result->fetchArray(SQLITE3_ASSOC)){
                    array_push($emailArray, $row['Email']); //set the values in to the array
                }
                $index = 0; //the current index the emailer is on
                foreach($emailArray as $email){
                    $email = $emailArray[$index]; //'Kevin.Gottlieb19@bcp.org'; //

                    $statement = $db -> prepare('SELECT Contact1Email, Contact2Email, Contact3Email, Contact4Email FROM master WHERE StudentEmail = :email;'); 
                    $statement -> bindValue(':email', $email);
                    $result = $statement->execute();
                    unset($contactEmailArray); //I forgot this line which caused the infinite loop
                    $contactEmailArray[] = [];
                    while($row = $result->fetchArray(SQLITE3_ASSOC)){
                        if(isset($row['Contact1Email'])){
                            array_push($contactEmailArray, $row['Contact1Email']); //set the values in to the array
                        }
                        if(isset($row['Contact2Email'])){
                            array_push($contactEmailArray, $row['Contact2Email']); //set the values in to the array
                        }
                        if(isset($row['Contact3Email'])){
                            array_push($contactEmailArray, $row['Contact3Email']); //set the values in to the array
                        }
                        if(isset($row['Contact4Email'])){
                            array_push($contactEmailArray, $row['Contact4Email']); //set the values in to the array
                        }
                    }

                    foreach($contactEmailArray as $contactEmail){
                        //send an email
                        $emailMessage = 
                        "Dear Parents, <br><br>

We hope you had a restful summer and that the return to the school year has been pleasant and smooth. Last year was a great year for The Carillon as we finished the book earlier than before– a testament to the amount of work your sons put in last year. We cannot thank you enough, for lending your time, snacks, and, of course, your sons for the creation of this book. <br><br>

As you know, Back to School Night is coming up this Thursday and we would love to invite you to the office in the basement of O’Donnell to see where your sons spend their time before school, during lunch, and after school. We will give you a tour of the office, show past books, and more! We will also be available to answer any questions you might have. <br><br>

No worries if you do not have a free period, we will be open for visitors before and after Back to School Night so come in anytime you can. <br><br>

We can’t wait to see you there! <br><br>

Cameron <br>
The Carillon
";
 
                        if(sendMail($contactEmail, $emailSubject, $emailMessage)){ //if mail is sent successfully
                            echo "Mail sent to $contactEmail <br>";
                        }
                        else{ //if send fails
                            echo "Oh no! Sending a reminder email to $email has failed! Plase contact <a href='mailto:carillon@bcp.org'>carillon@bcp.org</a> so we can fix the problem.";
                        }
                        //echo "Mail sent to $contactEmail <br>";
                    }
                    $index++; //increment index
                }
            }
        ?>
    </body>
</html>