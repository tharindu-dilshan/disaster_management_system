<?php

/**
 * Handles the user registration
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 */
class Registration
{
    /**
     * @var object $db_connection The database connection
     */
    private $db_connection            = null;
    /**
     * @var bool success state of verification
     */
    public $verification_successful   = false;
    /**
     * @var bool success state of registration
     */
    public  $registration_successful  = false;
    /**
     * @var array collection of error messages
     */
    public  $errors                   = array();
    /**
     * @var array collection of success / neutral messages
     */
    public  $messages                 = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        if(!isset($_SESSION)){
            session_start();
        }

        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST["register"])) {
            $this->registerNewUser($_POST['user_name'], $_POST['user_email'], $_POST['user_password_new'], $_POST['user_password_repeat']);
        }
    }

    /**
     * Checks if database connection is opened and open it if not
     */
    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;
        } else {
            // create a database connection, using the constants from config/config.php
            try {
                // Generate a database connection, using the PDO connector
                // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
                // Also important: We include the charset, as leaving it out seems to be a security issue:
                // @see http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers#Connecting_to_MySQL says:
                // "Adding the charset to the DSN is very important for security reasons,
                // most examples you'll see around leave it out. MAKE SURE TO INCLUDE THE CHARSET!"
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = "Database error";
                return false;
            }
        }
    }

    /**
     * handles the entire registration process. checks all error possibilities, and creates a new user in the database if
     * everything is fine
     */
    private function registerNewUser($user_name, $user_email, $user_password, $user_password_repeat)
    {
        // we just remove extra space on username and email
        $user_name  = trim($user_name);
        $user_email = trim($user_email);

		$user_fullname = $_POST['user_fullname'];
		$user_phone = preg_replace("/[^0-9,.]/", "", $_POST['user_phone']);

        // check provided data validity
        // TODO: check for "return true" case early, so put this first
        if (empty($user_name)) {
            $this->errors[] = "Username field cannot be empty";
        } elseif (empty($user_password) || empty($user_password_repeat)) {
            $this->errors[] = "Password field cannot be empty";
        } elseif ($user_password !== $user_password_repeat) {
            $this->errors[] = "Repeat password is not the same";
        } elseif (strlen($user_password) < 6) {
            $this->errors[] = "Password is too short";
        } elseif (strlen($user_name) > 24 || strlen($user_name) < 2) {
            $this->errors[] = "Username bad length";
        } elseif (!preg_match('/^[a-z\d]{6,24}$/i', $user_name)) {
            $this->errors[] = "Invalid Username. Username must be between 6-24 characters a-z";
        } elseif (!preg_match('/^[\d]{8,10}$/i', $user_phone)) {
            $this->errors[] = "Invalid phone provided. Only digits 8-10 characters long";
        } elseif (empty($user_email)) {
            $this->errors[] = "Email field empty";
        } elseif (strlen($user_email) > 64) {
            $this->errors[] = "Email address too long";
        } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email address";;

        // finally if all the above checks are ok
        } else if ($this->databaseConnection()) {
            // check if username or email already exists
            $query_check_user_name = $this->db_connection->prepare('SELECT user_name, user_email FROM web_users WHERE user_name=:user_name OR user_email=:user_email');
            $query_check_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $query_check_user_name->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_check_user_name->execute();
            $result = $query_check_user_name->fetchAll();

            // if username or/and email find in the database
            // TODO: this is really awful!
            if (count($result) > 0) {
                for ($i = 0; $i < count($result); $i++) {
                    $this->errors[] = ($result[$i]['user_name'] == $user_name) ? "Username is taken" : "This email address is already registered";
                }
            } else {
                // check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                // want the parameter: as an array with, currently only used with 'cost' => XX.
                $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
                // generate random hash for email verification (40 char string)
                $user_activation_hash = sha1(uniqid(mt_rand(), true));

                // write new users data into database
                $query_new_user_insert = $this->db_connection->prepare('INSERT INTO web_users (user_name, user_password_hash, user_email, user_registration_ip, user_registration_datetime, user_fullname, user_phone) VALUES(:user_name, :user_password_hash, :user_email, :user_registration_ip, now(), :user_fullname, :user_phone)');
                $query_new_user_insert->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_new_user_insert->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                $query_new_user_insert->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                $query_new_user_insert->bindValue(':user_registration_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                $query_new_user_insert->bindValue(':user_fullname', $user_fullname, PDO::PARAM_STR);
                $query_new_user_insert->bindValue(':user_phone', $user_phone, PDO::PARAM_STR);
                $query_new_user_insert->execute();


                $this->verification_successful = true;
                $this->messages[] = "Signup Successfull!";
            }
        }
    }

}
