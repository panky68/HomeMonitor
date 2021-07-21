<?php
    //Include required phpmailer files
    require 'PHPMailer/PHPMailer.php'; 
    require 'PHPMailer/SMTP.php';
    require 'PHPMailer/Exception.php';

    //Define Name Space
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    include 'core/init.php';
    if(userLoggedIn() === true){                //has a user logged on?
        $page = 'contact'; 
        include 'includes/overall/header.php';  //include header

        // if(empty($_POST) === false){
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            function getCaptcha($SecretKey){
                $Response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response={$SecretKey}"); //from URL, get recapture success value and score
                $Result = json_decode($Response);   //decode, JSON structure response from website
                return $Result;                     //return json object
            }
            $Return = getCaptcha($_POST['reCAPTCHA']); //Get forms token value from hidden input, call function to get the success and score rating of submit request
        
            if($Return->success === true && $Return->score > 0.5){ //if Success = true and $Return object score is over .5
                $fistName_err = $lastName_err = $email_err = $phone_err = $enquiry_err = ''; //initialise error flags
                $mailErrorFlag1 = $mailErrorFlag2 = false;  //initialise mail error flag

                //get data from form
                $firstName = $_POST['firstName'];   //get name of customer
                $lastName = $_POST['lastName'];     //get name of customer
                $fromEmail = $_POST['email'];       //get customers email
                $phone = $_POST['phone'];           //get customers phone
                $enquiry = $_POST['message'];       //get customers query

                //validate the First name input
                if(empty($firstName)){
                    $fistName_err = 'First Name is required';
                }else{
                    testInput($firstName);   //prevent sql injection
                    if(!preg_match("/^[a-zA-Z ]*$/", $firstName)){   //check only letters and white spaces allowed
                        $fistName_err = 'Only letters and white space allowed';
                    }else if(strlen($firstName) > 32){
                        $fistName_err = 'Too Many Charactors';
                    }
                }
                //validate the Last name input
                if(empty($lastName)){
                    $lastName_err = 'Last Name is required';
                }else{
                    testInput($lastName);   //prevent sql injection
                    if(!preg_match("/^[a-zA-Z ]*$/", $lastName)){   //check only letters and white spaces allowed
                        $lastName_err = 'Only letters and white space allowed';
                    }else if(strlen($lastName) > 32){
                        $lastName_err = 'Too Many Charactors';
                    }
                }
                //validate the Email input
                if(empty($fromEmail)){
                    $email_err = 'Email is required';
                }else{
                    testInput($fromEmail);    //prevent sql injection
                    if(!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)){ //check email format
                        $email_err = 'Invalid Email Format';
                    }else if(strlen($fromEmail) > 64){      //check input not more than 64 chars
                        $email_err = 'Too Many Charactors';
                    }
                }
                //validate the phone input
                if(empty($phone)){
                    $phone_err = 'Telephone is required';
                }else{
                    testInput($phone);                           //prevent sql injection
                    if(!preg_match("/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i", $phone)){     //check for valid phone number
                        $phone_err = 'Invalid Phone Number';
                    }else if(strlen($phone) > 15){          //check input not more than 15 chars
                        $phone_err = 'Too Many Digits';
                    }
                }
                //validate the enquiry input
                if(empty($enquiry)){
                    $enquiry_err = 'Please enter a message';
                }
                //Configure and send email
                if($fistName_err == '' && $lastName_err == '' && $email_err == '' && $phone_err == '' && $enquiry_err == ''){ //if there are no validation errors
                    $mail1 = new PHPMailer();       //new instance of PHPMailer
                    $mail1->isSMTP();               //set mailer to use smtp
                    $mail1->Host = 'smtp.live.com'; //smtp host
                    $mail1->SMTPAuth = true;        //enable smtp athorisation
                    $mail1->SMTPSecure = 'tls';     //type of encryption is TLS
                    $mail1->Port = 587;             //port to connect smtp
                    $mail1->Username = 'panky_68@hotmail.com'; //hotmail username
                    $mail1->Password = 'Liam68par';    //hotmail password
                    
                    //mail to Panky on google mail
                    $to_panky = 'panky1968@googlemail.com';
                    $subject_panky = 'Query from Panky\'s Home Monitoring system';
                    $body_panky = "Contact from\n".$name."\n".$phone."\n".$fromEmail."\n"."'\n\n".$enquiry;

                    $mail1->setFrom('panky_68@hotmail.com'); //Sender's Email Address (ie panky email)
                    $mail1->Subject = $subject_panky; //Email Subject
                    $mail1->Body =  $body_panky; //Email Body is plain text (can use html format)
                    $mail1->addAddress($to_panky); //recipent address
                    $mailErrorFlag1 = $mail1->send();    //send mail, true = successful, false = unsuccessful
                    $mail1->smtpClose(); //closing stmp connection
                    $mail1->ClearAllRecipients();
                    
                    if($mailErrorFlag1 == true){    //check email sent a success
                        header("Location: contact.php?email_sent");     //redirect back with email sent 
                    }else{
                        header("Location: contact.php?email_failed");   //redirect back with email failed 
                    }
                }
            }
        }
    ?>
    <div class="container">
        <h2 class="pageHeader">Contact Page</h2>
        <?php echo $name; ?>
        
        <!-- Contact Form -->
        <form action="contact.php" method="POST" class="contactForm">
            <div class="row">
                <div class="col-6">
                    <!-- First Name Input -->
                    <div class="form-group">
                        <label for="firstName" class="contactLabel">First Name *</label>    <!-- Input Label -->
                        <input type="text" id="firstName" name="firstName" class="form-control contactInputs <?php if(!empty($fistName_err)){ echo 'input-error';} ?>" value="<?php if(isset($firstName)){ echo $firstName;}?>">
                        <?php if(isset($fistName_err)){ ?>  <!-- if firstname input error -->
                            <div>
                                <p class="contactFormErrMsg"><?php echo $fistName_err; ?></p>   <!-- firstname error message -->
                            </div>
                        <?php } else {?>
                            <div>
                                <p></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- Last Name Input -->
                <div class="col-6">
                    <div class="form-group">
                        <label for="lastName" class="contactLabel">Last Name *</label>  <!-- Input Label -->
                        <input type="text" id="lastName" name="lastName" class="form-control contactInputs <?php if(!empty($lastName_err)){ echo 'input-error';} ?>" value="<?php if(isset($lastName)){ echo $lastName;}?>">
                        <?php if(isset($lastName_err)){ ?>  <!-- if lastname input error -->
                            <div>
                                <p class="contactFormErrMsg"><?php echo $lastName_err; ?></p>   <!-- lastname error message -->
                            </div>
                        <?php } else {?>
                            <div>
                                <p></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Email Input -->
            <div class="form-group">
                <label for="email" class="contactLabel">Email  *</label>
                <input type="text" id="email" name="email" class="form-control contactInputs <?php if(!empty($email_err)){ echo 'input-error';} ?>" value="<?php if(isset($fromEmail)){ echo $fromEmail;}?>">
                <?php if(isset($email_err)){ ?>     <!-- if email input error -->
                    <div>
                        <p class="contactFormErrMsg"><?php echo $email_err; ?></p>  <!-- email error message -->
                    </div>
                <?php } else {?>
                    <div>
                        <p></p>
                    </div>
                <?php } ?>
            </div>

            <!-- Telephone Input -->
            <div class="form-group">
                <label for="phone" class="contactLabel">Telephone  *</label>
                <input type="text" id="phone" name="phone" class="form-control contactInputs <?php if(!empty($phone_err)){ echo 'input-error';} ?>" value="<?php if(isset($phone)){ echo $phone;}?>">
                <?php if(isset($phone_err)){ ?>     <!-- if phone number input error -->
                    <div>
                        <p class="contactFormErrMsg"><?php echo $phone_err; ?></p>  <!-- phoone number error message -->
                    </div>
                <?php } else {?>
                    <div>
                        <p></p>
                    </div>
                <?php } ?>
            </div>

            <!-- Enquiry Input -->
            <div class="form-group">
                <label for="message" class="contactLabel">Message *</label>
                <textarea id="message" name="message" rows="4" class="form-control contactInputs <?php if(!empty($enquiry_err)){ echo 'input-error';} ?>" value="<?php if(isset($enquiry)){ echo $enquiry;}?>"></textarea>
                <?php if(isset($enquiry_err)){ ?>       <!-- if enquiry input error -->
                    <div>
                        <p class="contactFormErrMsg"><?php echo $enquiry_err; ?></p>    <!-- enquiry error message -->
                    </div>
                <?php } else {?>
                    <div>
                        <p></p>
                    </div>
                <?php } ?>
            </div>

            <!-- Hidden ReCapture Input -->
            <input type="hidden" id="reCAPTCHA" name="reCAPTCHA">

            <!-- Submit button -->
            <div class="form-group text-center">    <!-- position submit button centrally -->
                <button class="btn btn-primary contact-us-form-btn" type="submit" name="submit" role="button">Send Message</button>
            </div>

            <!-- Email Message -->
            <?php
            if(isset($_GET['email_sent'])){ ?>  <!-- email sent success in URL -->
                <div class="alert alert-success">Your Message has been sent</div>   <!-- success message -->
            <?php
            }else if(isset($_GET['email_failed'])){ ?>  <!-- email sent failed in URL -->
                <div class="alert alert-danger">Unfortunately, your Message cannot be been sent</div>   <!-- fail message -->
            <?php
            }?>
        </form>
    </div>
    <?php include 'includes/overall/footer.php'; ?>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo SITE_KEY; ?>', {action: 'homepage'}).then(function(token) {
                document.getElementById('reCAPTCHA').value=token; //insert token into forms hidden Recapture input
            });
        });
    </script>
<?php 
    }else{
        header('Location: loginform.php');  //redirect user to login page if user not logged in
    }
?>