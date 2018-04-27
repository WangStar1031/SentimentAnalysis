<?php
class Connection
{
    protected $mysqli;

  

    function __construct()
    {
        $this->db_connect();
    }

    function db_connect()
    {
        $mysqli = new mysqli(CONFIG_SERVER, CONFIG_DB_USER, CONFIG_DB_PWD, CONFIG_DB);
        $this->mysqli = $mysqli;

    }

    function db_close()
    {
        $this->mysqli->close();
    }

    
    function Login($email, $password)
    {
        // Usando statement sql 'prepared' non sarà possibile attuare un attacco di tipo SQL injection.
        $objReturn[] = null;
        $user_active = '';
        $user_id = '';
        $user_name = '';
        $user_password = '';
        $user_password_salt = '';
        $user_msg_tmp = '';
        $tipo_fk = '';

        $stmt = $this->mysqli->prepare("SELECT SQL_NO_CACHE user_active,user_id,user_name,user_password,user_password_salt,tipo_fk FROM tb_users WHERE user_email = ? LIMIT 1");

        if ($stmt)
        {
            
                $stmt->bind_param('s', $email); // esegue il bind del parametro '$email'.
            

            $stmt->execute(); // esegue la query appena creata.
            $stmt->store_result();
            $stmt->bind_result($user_active, $user_id, $user_name, $user_password, $user_password_salt, $tipo_fk, $user_msg_tmp); // recupera il risultato della query e lo memorizza nelle relative variabili.
            $stmt->fetch();

            $password = hash('sha512', $password . $user_password_salt);

            if ($stmt->num_rows == 1)
            {
                    //20161216 sono quelli ok o in purgatorio
                    if ($user_active == '1' || $user_active == '2')
                    {
                        if ($user_password == $password)
                        { // Verifica che la password memorizzata nel database corrisponda alla password fornita dall'utente.
                            // Password corretta!
                            $user_browser = $_SERVER['HTTP_USER_AGENT']; // Recupero il parametro 'user-agent' relativo all'utente corrente.

                            $user_id = preg_replace("/[^0-9]+/", "", $user_id); // ci proteggiamo da un attacco XSS
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['tipo_fk'] = $tipo_fk;
                            $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $user_name); // ci proteggiamo da un attacco XSS
                            $_SESSION['username'] = $user_name;
                            $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                            $_SESSION['user_msg_tmp'] = $user_msg_tmp;

                            $objReturn[0] = 'ok';
                            $objReturn[1] = '';
                            $objReturn[2] = 'Bentornato <b>' . $user_name . '!</b> ';
                        }
                        else
                        {
                            $objReturn[0] = 'ko';
                            $objReturn[1] = '';
                            $objReturn[2] = ' Attenzione, La password che hai inserito non è corretta. <a href="index.php?action=sendpassword">Password persa?</a> ' . $obj[2];
                        }
                    }
                    else if ($user_active == '0')
                    {
                        $objReturn[0] = 'ko';
                        $objReturn[1] = '';
                        $objReturn[2] = 'Attenzione, ti sei iscritto, ma non hai completato la procedura di conferma!<br><br>Attenzione, se non hai ricevuto l\'email, verifica nella casella di SPAM del tuo account di posta elettronica. <a href="contact.php?msg=Non posso entrare nel sito, perchè non ho completatato la procedura d\'iscrizione.  Questa è l\'email con cui mi sono registrato: &email='.$email.'">Contatta l\'amministratore.</a>. ';

                    }
                    else
                    {

                        $objReturn[0] = 'ko';
                        $objReturn[1] = '';
                        $objReturn[2] = 'Attenzione, non esiste nessun utente con l\'email e password inseriti. <a href="index.php?action=sendpassword">Password persa?</a>. ';

                    }
               
            }
            else
            {
                $objReturn[0] = 'ko';
                $objReturn[1] = '';
                $objReturn[2] = 'Attenzione, non esiste nessun utente con l\'email e password inseriti. <a href="index.php?action=register">Iscriviti!</a>. ';


            }

        }

        return $objReturn;
    }

    function Register($objMail, $objUtility, $user_email, $user_password)
    {


        $objReturn[] = null;
        if (isset($user_password, $user_email) && trim($user_email) <> '' && trim($user_password) <> '') {

            //1.
            $returnRegisterControl = $this->RegisterControl($user_email, $user_password);

            if ($returnRegisterControl[0] == 'ko')
            {
                return $returnRegisterControl;
            }
            else
            {
                    $id = $returnRegisterControl[1];

                    $template = "base";
                    $titoloMail = "Conferma la tua iscrizione a " . CONFIG_NAME_WEBSITE;
                    $oggetto = "Conferma la tua iscrizione a " . CONFIG_NAME_WEBSITE;

                    $url = CONFIG_PATH . '/template/index.php?action=confirm_user&control=' . $objUtility->str_crypt($id . '|' . $user_email . '|' . date("Y-n-j"), CONFIG_KEY_CRYPT);


                    $testo = 'Ci siamo quasi, ma se non sei stato tu a richiedere l\'iscrizione a ' . CONFIG_NAME_WEBSITE . ', ignora questa email (NON sarai piu\' disturbato).<br>';
                    $testo .= '<b>In caso contrario</b> e\' necessaria la tua conferma seguendo il link:<br><br>';
                    //$testo .= '<a href="'.$url.'>'.$url.'</a><br><br>';

                    $testo .= '<table border="0" cellpadding="0" cellspacing="0" width="35%" style="background-color:#D67118;border-radius:6px;"><tbody><tr><td align="center" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;">';
                    echo $testo .= '<a target="_blank" href="' . $url . '" style="color:#FFFFFF; text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:700;line-height:150%;" target="_blank">Conferma</a></td></tr></tbody></table>';

                    $body = '';
                    $footer = '';


                    //3.
                    $returnSM = $objUtility->sendMail($objMail, $user_email, CONFIG_EMAIL_NEWSLETTER, $oggetto, $testo, $titoloMail, $template, $body, $footer);
                    if ($returnSM[0] == 'ok') {
                        $objReturn[0] = 'ok';
                        $objReturn[1] = '';
                        $objReturn[2] = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b>Iscrizione avvenuta!</b>b> Segui le istruzioni che ti abbiamo inviato a questa email: ' . $user_email.'<br>ATTENZIONE, se non ricevi l\'email nella tua posta, potrebbe essere finita nella cartella del tuo SPAM. <br> Vai alla pagina di <a href="'.CONFIG_PATH.'/template/index.php?action=login&redirect=L35lc2N1cnNtc3cvaW5kZXgucGhw"">login</div>!</div>';


                    } else {
                        $objReturn[0] = 'ko';
                        $objReturn[1] = '';
                        $objReturn[2] = $returnSM[2];
                        
                    }



            }
        } else {
            $objReturn[0] = 'ko';
            $objReturn[1] = '';
            $objReturn[2] = 'Opsss, compila tutti i campi nel form!';
        }

        return $objReturn;

    }

    function RegisterControl($email, $password)
    {

        $returnArray[] = null;

        if ($stmt = $this->mysqli->prepare("SELECT SQL_NO_CACHE user_email FROM users WHERE user_email = ? LIMIT 1")) {

            $stmt->bind_param('s', $email); // esegue il bind del parametro '$email'.
            $stmt->execute(); // esegue la query appena creata.
            $stmt->store_result();
            //$stmt->bind_result($email); // recupera il risultato della query e lo memorizza nelle relative variabili.
            //$stmt->fetch();


            if ($stmt->num_rows == 1) {

                $returnArray[0] = 'ko';
                $returnArray[1] = '';
                $returnArray[2] = 'Opppsss, risulti gia\' registrato con questa email.';
                return $returnArray;
            } else {

                $user_name = explode("@", $email);
                $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
                $password = hash('sha512', $password . $random_salt);
                $user_ip = getenv('REMOTE_ADDR');

                if ($insert_stmt = $this->mysqli->prepare("INSERT INTO tb_users (user_ip,user_name, user_email, user_password, user_password_salt,user_data) VALUES (?, ?, ?, ?,?, ?)")) {
                    $data = date("Y-n-j H:i:s");
                    $insert_stmt->bind_param('ssssss', $user_ip, $user_name[0], $email, $password, $random_salt, $data);
                    // Esegui la query ottenuta.
                    if ($insert_stmt->execute() == 1) {
                        $returnArray[0] = 'ok';
                        $returnArray[1] = mysqli_insert_id($this->mysqli);
                        $returnArray[2] = '';

                    } else {
                        $returnArray[0] = 'ko';
                        $returnArray[1] = '';
                        $returnArray[2] = 'Errore inserimento (LoginUserRegister param)';
                    }


                } else {
                    $returnArray[0] = 'ko';
                    $returnArray[1] = '';
                    $returnArray[2] = 'Errore inserimento (LoginUserRegister insert)';
                }
            }

            $stmt->close();
        } else {
            $returnArray[0] = 'ko';
            $returnArray[1] = '';
            $returnArray[2] = 'Errore inserimento (LoginUserRegister verifica utente già registrato)';
        }

        return $returnArray;
    }

    function SendPassword($objMail, $objUtility, $user_email)
    {

        $objReturn[] = null;
        if (isset($user_email) && $user_email <> '') {

            $sql = "SELECT SQL_NO_CACHE user_id,user_name FROM  users WHERE user_email = '" . $user_email . "'";
            $result = $this->mysqli->query($sql);
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            $user_name = $row['user_name'];
            if ($result->num_rows > 0) {

                    //user_recupero_password
                    $objReturn[] = null;
                    $user_recupero_password = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
                    $stmt = $this->mysqli->prepare("UPDATE users SET  user_recupero_password=?  WHERE user_id=?");
                    $user_msg_tmp = null;
                    $stmt->bind_param('si', $user_recupero_password, $user_id);
                    $results = $stmt->execute();
                    if ($results) {

                        $template = "base";
                        $titoloMail = "Recupero password " . CONFIG_NAME_WEBSITE;
                        $oggetto = "Recupero password per accedere al sito: " . CONFIG_NAME_WEBSITE;

                        $url = CONFIG_PATH . '/template/index.php?action=confirm_password&control=' . $user_recupero_password;
//CONFIRM_PASSWORD

                        $testo = 'Ciao <b>' . $user_name . '</b>,<br>qualcuno ha richiesto il recupero della password per accedere a ' . CONFIG_NAME_WEBSITE . ', se non sei stato tu a richiederla ignora questa email.<br>';
                        $testo .= '<b>In caso contrario</b> e\' necessario seguire il seguente link:<br><br>';
                        //$testo .= '<a href="'.$url.'>'.$url.'</a><br><br>';

                        $testo .= '<table border="0" cellpadding="0" cellspacing="0" width="35%" style="background-color:#D67118;border-radius:6px;"><tbody><tr><td align="center" style="padding-top:10px;padding-bottom:10px;padding-right:10px;padding-left:10px;">';
                        $testo .= '<a target="_blank" href="' . $url . '" style="color:#FFFFFF; text-decoration:none;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:700;line-height:150%;" target="_blank">Conferma</a></td></tr></tbody></table>';
                        //$testo .= 'o copia e incolla il seguente indirizzo nella barra del browser:<br><h5>'.$url.'</h5>';
                        $body = '';
                        $footer = '';


                        //3.
                        $returnSM = $objUtility->sendMail($objMail, $user_email, CONFIG_EMAIL_NEWSLETTER, $oggetto, $testo, $titoloMail, $template, $body, $footer);
                        if ($returnSM[0] == 'ok') {
                            $objReturn[0] = 'ok';
                            $objReturn[1] = '';
                            $objReturn[2] = 'Recupero password avvenuta con successo! Segui le istruzioni che ti abbiamo inviato nell\' email: ' . $user_email;


                        } else {
                            $objReturn[0] = 'ko';
                            $objReturn[1] = '';
                            $objReturn[2] = $returnSM[2];


                        }


                    } else {
                        $objReturn[0] = 'ko';
                        $objReturn[1] = '';
                        $objReturn[2] = 'Attenzione!</b> Problemi tecnici. (' . $this->mysqli->errno . '). Se l\'errore persiste contattare l\'amministratore del sito. Grazie.';

                    $stmt->close();

                }
            } else {
                $objReturn[0] = 'ko';
                $objReturn[1] = '';
                $objReturn[2] = 'Non abbiamo trovato nessun utente con questa email: <b>' . $user_email . '</b>.  <a href="'.CONFIG_PATH . '/template/index.php?action=register">Iscriviti!</a>';
            }

        } else {
            $objReturn[0] = 'ok';
            $objReturn[1] = '';
            $objReturn[2] = 'Devi inserire un\'email valida.';
        }
        return $objReturn;
    }



}