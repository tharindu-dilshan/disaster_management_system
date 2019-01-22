<?php

/**
 * handles the user login/logout/session
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 */
class Login
{
    /**
     * @var object $db_connection The database connection
     */
    private $db_connection = null;
    /**
     * @var int $user_id The user's id
     */
    private $user_id = null;
    /**
     * @var string $user_name The user's name
     */
    private $user_name = "";
    /**
     * @var int $user_type The user's type
     * 0 for user, 1 for administrator
     */
    private $user_type = "";
    /**
     * @var string $user_email The user's mail
     */
    private $user_email = "";
    /**
     * @var boolean $user_is_logged_in The user's login status
     */
    private $user_is_logged_in = false;
    /**
     * @var boolean $user_is_admin The user's privilege status
     */
    private $user_is_admin = false;
    /**
     * @var string $user_gravatar_image_url The user's gravatar profile pic url (or a default one)
     */
    public $user_gravatar_image_url = "";
    /**
     * @var string $user_gravatar_image_tag The user's gravatar profile pic url with <img ... /> around
     */
    public $user_gravatar_image_tag = "";
    /**
     * @var boolean $password_reset_link_is_valid Marker for view handling
     */
    private $password_reset_link_is_valid  = false;
    /**
     * @var boolean $password_reset_was_successful Marker for view handling
     */
    private $password_reset_was_successful = false;
    /**
     * @var array $errors Collection of error messages
     */
    public $errors = array();
    /**
     * @var array $messages Collection of success / neutral messages
     */
    public $messages = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$login = new Login();"
     */
    public function __construct()
    {
        // create/read session
        session_start();

        // TODO: organize this stuff better and make the constructor very small
        // TODO: unite Login and Registration classes ?

        // check the possible login actions:
        // 1. logout (happen when user clicks logout button)
        // 2. login via session data (happens each time user opens a page on your php project AFTER he has successfully logged in via the login form)
        // 3. login via cookie
        // 4. login via post data, which means simply logging in via the login form. after the user has submit his login/password successfully, his
        //    logged-in-status is written into his session data on the server. this is the typical behaviour of common login scripts.

        // if user tried to log out
        if (isset($_GET["logout"])) {
            $this->doLogout();

        // if user has an active session on the server
        } elseif (!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)) {
            $this->loginWithSessionData();

            // checking for form submit from editing screen
            // user try to change his username
            if (isset($_POST["user_edit_submit_name"])) {
                $this->editUserName($_POST['user_name'], $_POST['user_name_old']);
            // user try to change his fullname
            } elseif (isset($_POST["user_edit_submit_fullname"])) {
                $this->editUserFullname($_POST['user_fullname'], $_POST['user_fullname_old'], $_POST['user_name']);
            // user try to change his phone
            } elseif (isset($_POST["user_edit_submit_phone"])) {
                $this->editUserPhone($_POST['user_phone'], $_POST['user_phone_old'], $_POST['user_name']);
            // user try to change his email
            } elseif (isset($_POST["user_edit_submit_email"])) {
                $this->editUserEmail($_POST['user_email'], $_POST['user_email_old']);
            // user try to change his password
            } elseif (isset($_POST["user_edit_submit_password"])) {
                $this->editUserPassword($_POST['user_password_old'], $_POST['user_password_new'], $_POST['user_password_repeat']);
            }

        // login with cookie
        } elseif (isset($_COOKIE['rememberme'])) {
            $this->loginWithCookieData();

        // if user just submitted a login form
        } elseif (isset($_POST["login"])) {
            if (!isset($_POST['user_rememberme'])) {
                $_POST['user_rememberme'] = null;
            }
            $this->loginWithPostData($_POST['user_email'], $_POST['user_password'], $_POST['user_rememberme']);
        }

        // get gravatar profile picture if user is logged in
        if ($this->isUserLoggedIn() == true) {
            $this->getGravatarImageUrl($this->user_email);
        }
    }

    /**
     * Checks if database connection is opened. If not, then this method tries to open it.
     * @return bool Success status of the database connecting process
     */
    private function databaseConnection()
    {
        // if connection already exists
        if ($this->db_connection != null) {
            return true;
        } else {
            try {
                // Generate a database connection, using the PDO connector
                // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
                // Also important: We include the charset, as leaving it out seems to be a security issue:
                // @see http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers#Connecting_to_MySQL says:
                // "Adding the charset to the DSN is very important for security reasons,
                // most examples you'll see around leave it out. MAKE SURE TO INCLUDE THE CHARSET!"
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
            } catch (PDOException $e) {
                $this->errors[] = "Database Error: " . $e->getMessage();
            }
        }
        // default return
        return false;
    }

    /**
     * Search into database for the user data of user_name specified as parameter
     * @return user data as an object if existing user
     * @return false if user_name is not found in the database
     * TODO: @devplanete This returns two different types. Maybe this is valid, but it feels bad. We should rework this.
     * TODO: @devplanete After some resarch I'm VERY sure that this is not good coding style! Please fix this.
     */
    private function getUserData($user_name)
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // database query, getting all the info of the selected user
            $query_user = $this->db_connection->prepare('SELECT * FROM web_users WHERE user_name = :user_name');
            $query_user->bindValue(':user_name', $user_name, PDO::PARAM_STR);
            $query_user->execute();
            // get result row (as an object)
            return $query_user->fetchObject();
        } else {
            return false;
        }
    }

    /**
     * Logs in with S_SESSION data.
     * Technically we are already logged in at that point of time, as the $_SESSION values already exist.
     */
    private function loginWithSessionData()
    {
        $this->user_name = $_SESSION['user_name'];
        $this->user_email = $_SESSION['user_email'];

        // set logged in status to true, because we just checked for this:
        // !empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)
        // when we called this method (in the constructor)
        $this->user_is_logged_in = true;

        $this->user_type = $_SESSION['user_type'];

        // Give user administrator privileges
        if ( $this->user_type==1 ) {
            $this->user_is_admin = true;
        }
    }

    /**
     * Logs in via the Cookie
     * @return bool success state of cookie login
     */
    private function loginWithCookieData()
    {
        if (isset($_COOKIE['rememberme'])) {
            // extract data from the cookie
            list ($user_id, $token, $hash) = explode(':', $_COOKIE['rememberme']);
            // check cookie hash validity
            if ($hash == hash('sha256', $user_id . ':' . $token . COOKIE_SECRET_KEY) && !empty($token)) {
                // cookie looks good, try to select corresponding user
                if ($this->databaseConnection()) {
                    // get real token from database (and all other data)
                    $sth = $this->db_connection->prepare("SELECT user_id, user_name, user_email, user_type, user_fullname, user_phone FROM web_users WHERE user_id = :user_id
                                                      AND user_rememberme_token = :user_rememberme_token AND user_rememberme_token IS NOT NULL");
                    $sth->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $sth->bindValue(':user_rememberme_token', $token, PDO::PARAM_STR);
                    $sth->execute();
                    // get result row (as an object)
                    $result_row = $sth->fetchObject();

                    if (isset($result_row->user_id)) {
                        // write user data into PHP SESSION [a file on your server]
                        $_SESSION['user_id'] = $result_row->user_id;
                        $_SESSION['user_name'] = $result_row->user_name;
                        $_SESSION['user_email'] = $result_row->user_email;
                        $_SESSION['user_type'] = $result_row->user_type;
                        $_SESSION['user_fullname'] = $result_row->user_fullname;
                        $_SESSION['user_phone'] = $result_row->user_phone;
                        $_SESSION['user_logged_in'] = 1;

                        // declare user id, set the login status to true
                        $this->user_id = $result_row->user_id;
                        $this->user_name = $result_row->user_name;
                        $this->user_email = $result_row->user_email;
                        $this->user_type = $result_row->user_type;
                        $this->user_is_logged_in = true;

                        // Give user administrator privileges
                        if ( $result_row->user_type==1 ) {
                            $this->user_is_admin = true;
                        }

                        // Cookie token usable only once
                        $this->newRememberMeCookie();
                        return true;
                    }
                }
            }
            // A cookie has been used but is not valid... we delete it
            $this->deleteRememberMeCookie();
            $this->errors[] = "Inva cookie";
        }
        return false;
    }

    /**
     * Logs in with the data provided in $_POST, coming from the login form
     * @param $user_name
     * @param $user_password
     * @param $user_rememberme
     */
    private function loginWithPostData($user_name, $user_password, $user_rememberme)
    {
       //require_once('/opt/lampp/htdocs/reportr/libraries/recaptchalib.php');
       //$privatekey = "6LfM3PESAAAAAEA8Op5uZwBPG5tteLYgfmO7kRWA";
       //$resp = recaptcha_check_answer ($privatekey,
                    //$_SERVER["REMOTE_ADDR"],
                    //$_POST["recaptcha_challenge_field"],
                    //$_POST["recaptcha_response_field"]);

        //if (!$resp->is_valid) {
        //// What happens when the CAPTCHA was entered incorrectly
            //$this->errors[] = "Incorrect captcha" ;
        //} else {
        //// Your code here to handle a successful verification

        if (empty($user_name)) {
            $this->errors[] = "The username/ email can't be empty.";
        } else if (empty($user_password)) {
            $this->errors[] = "Password can't be empty.";

        // if POST data (from login form) contains non-empty user_name and non-empty user_password
        } else {
            // user can login with his username or his email address.
            // if user has not typed a valid email address, we try to identify him with his user_name
            if (!filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
                // database query, getting all the info of the selected user
                $result_row = $this->getUserData(trim($user_name));

            // if user has typed a valid email address, we try to identify him with his user_email
            } else if ($this->databaseConnection()) {
                // database query, getting all the info of the selected user
                $query_user = $this->db_connection->prepare('SELECT * FROM web_users WHERE user_email = :user_email');
                $query_user->bindValue(':user_email', trim($user_name), PDO::PARAM_STR);
                $query_user->execute();
                // get result row (as an object)
                $result_row = $query_user->fetchObject();
            }

            // if this user not exists
            if (! isset($result_row->user_id)) {
                // was MESSAGE_USER_DOES_NOT_EXIST before, but has changed to MESSAGE_LOGIN_FAILED
                // to prevent potential attackers showing if the user exists
                $this->errors[] = "Incorrect Username or Password.";
            } else if (($result_row->user_failed_logins >= 2) && ($result_row->user_last_failed_login > (time() - 30))) {
                    $this->errors[] = "Later...";

            // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that user's password
            } else if (! password_verify($user_password, $result_row->user_password_hash)) {
                // increment the failed login counter for that user
                $sth = $this->db_connection->prepare('UPDATE web_users '
                        . 'SET user_failed_logins = user_failed_logins+1, user_last_failed_login = :user_last_failed_login '
                        . 'WHERE user_name = :user_name OR user_email = :user_name');
                $sth->execute(array(':user_name' => $user_name, ':user_last_failed_login' => time()));

                $this->errors[] = "Incorrect Username or Password.";
            } else {
                // write user data into PHP SESSION [a file on your server]
                $_SESSION['user_id'] = $result_row->user_id;
                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_type'] = $result_row->user_type;
                $_SESSION['user_fullname'] = $result_row->user_fullname;
                $_SESSION['user_phone'] = $result_row->user_phone;
                $_SESSION['user_logged_in'] = 1;

                // declare user id, set the login status to true
                $this->user_id = $result_row->user_id;
                $this->user_name = $result_row->user_name;
                $this->user_email = $result_row->user_email;
                $this->user_type = $result_row->user_type;
                $this->user_is_logged_in = true;

                // Give user administrator privileges
                if ( $this->user_type==1 ) {
                    $this->user_is_admin = true;
                }

                // reset the failed login counter for that user
                $sth = $this->db_connection->prepare('UPDATE web_users '
                        . 'SET user_failed_logins = 0, user_last_failed_login = NULL '
                        . 'WHERE user_id = :user_id AND user_failed_logins != 0');
                $sth->execute(array(':user_id' => $result_row->user_id));

                // if user has check the "remember me" checkbox, then generate token and write cookie
                if (isset($user_rememberme)) {
                    $this->newRememberMeCookie();
                } else {
                    // Reset remember-me token
                    $this->deleteRememberMeCookie();
                }

                // OPTIONAL: recalculate the user's password hash
                // DELETE this if-block if you like, it only exists to recalculate users's hashes when you provide a cost factor,
                // by default the script will use a cost factor of 10 and never change it.
                // check if the have defined a cost factor in config/hashing.php
                if (defined('HASH_COST_FACTOR')) {
                    // check if the hash needs to be rehashed
                    if (password_needs_rehash($result_row->user_password_hash, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR))) {

                        // calculate new hash with new cost factor
                        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));

                        // TODO: this should be put into another method !?
                        $query_update = $this->db_connection->prepare('UPDATE web_users SET user_password_hash = :user_password_hash WHERE user_id = :user_id');
                        $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                        $query_update->bindValue(':user_id', $result_row->user_id, PDO::PARAM_INT);
                        $query_update->execute();

                        if ($query_update->rowCount() == 0) {
                            // writing new hash was successful. you should now output this to the user ;)
                        } else {
                            // writing new hash was NOT successful. you should now output this to the user ;)
                        }
                    }
                }
            }
        }
    }//}

    /**
     * Create all data needed for remember me cookie connection on client and server side
     */
    private function newRememberMeCookie()
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // generate 64 char random string and store it in current user data
            $random_token_string = hash('sha256', mt_rand());
            $sth = $this->db_connection->prepare("UPDATE web_users SET user_rememberme_token = :user_rememberme_token WHERE user_id = :user_id");
            $sth->execute(array(':user_rememberme_token' => $random_token_string, ':user_id' => $_SESSION['user_id']));

            // generate cookie string that consists of userid, randomstring and combined hash of both
            $cookie_string_first_part = $_SESSION['user_id'] . ':' . $random_token_string;
            $cookie_string_hash = hash('sha256', $cookie_string_first_part . COOKIE_SECRET_KEY);
            $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

            // set cookie
            setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);
        }
    }

    /**
     * Delete all data needed for remember me cookie connection on client and server side
     */
    private function deleteRememberMeCookie()
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // Reset rememberme token
            $sth = $this->db_connection->prepare("UPDATE web_users SET user_rememberme_token = NULL WHERE user_id = :user_id");
            $sth->execute(array(':user_id' => $_SESSION['user_id']));
        }

        // set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
        // that's obivously the best practice to kill a cookie via php
        // @see http://stackoverflow.com/a/686166/1114320
        setcookie('rememberme', false, time() - (3600 * 3650), '/', COOKIE_DOMAIN);
    }

    /**
     * Perform the logout, resetting the session
     */
    public function doLogout()
    {
        //check if cookie exists fixes bug caused when testing on localhost ;)
        if(isset($_COOKIE['rememberme'])) {
            $this->deleteRememberMeCookie();
        }

        $_SESSION = array();
        session_destroy();

        $this->user_is_logged_in = false;
        $this->messages[] = "You have been logged out";
    }

    /**
     * Simply return the current state of the user's login
     * @return bool user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->user_is_logged_in;
    }

    /**
     * Simply return the current state of the user's privilege
     * @return bool user's types
     */
    public function isUserAdmin()
    {
        return $this->user_is_admin;
    }

    /**
     * Edit the user's name, provided in the editing form
     */
    public function editUserName($user_name, $user_name_old)
    {
        // prevent database flooding
        $user_name = substr(trim($user_name), 0, 64);
        $user_name_old = substr(trim($user_name_old), 0, 64);

        if (!empty($user_name) && $user_name == $user_name_old) {
            $this->errors[] = "Your old and new usernames are the same";

        // username cannot be empty and must be azAZ09 and 2-64 characters
        // TODO: maybe this pattern should also be implemented in Registration.php (or other way round)
        } elseif (empty($user_name) || !preg_match("/^(?=.{2,64}$)[a-zA-Z][a-zA-Z0-9]*(?: [a-zA-Z0-9]+)*$/", $user_name)) {
            $this->errors[] = "The username must be alphanumeric and between 2-64 characters.";

        } else {
            // check if new username already exists
            $result_row = $this->getUserData($user_name);

            if (isset($result_row->user_id)) {
                $this->errors[] = "Username Already exist.";
            } else {
                // write user's new data into database
                $query_edit_user_name = $this->db_connection->prepare('UPDATE web_users SET user_name = :user_name WHERE user_name = :user_name_old');
                $query_edit_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_edit_user_name->bindValue(':user_name_old', $user_name_old, PDO::PARAM_INT);
                $query_edit_user_name->execute();

                if ($query_edit_user_name->rowCount()) {
					if(strcmp($user_name_old, $_SESSION['user_name']) == 0)
						$_SESSION['user_name'] = $user_name;
                    $this->messages[] = "Username changed succesfully: " . $user_name;
                } else {
                    $this->errors[] = "Username change failed.";
                }
            }
        }
    }

    /**
     * Edit the user's name, provided in the editing form
     */
    public function editUserFullname($user_fullname, $user_fullname_old, $user_name)
    {
        // prevent database flooding
        $user_fullname = substr(trim($user_fullname), 0, 40);
        $user_name = substr(trim($user_name), 0, 64);

        if (!empty($user_fullname) && $user_fullname == $user_fullname_old) {
            $this->errors[] = "Your old and new full names are the same.";
        } else {
        	if ($this->databaseConnection()) {
                // write user's new data into database
                $query_edit_user_name = $this->db_connection->prepare('UPDATE web_users SET user_fullname = :user_fullname WHERE user_name = :user_name');
                $query_edit_user_name->bindValue(':user_fullname', $user_fullname, PDO::PARAM_INT);
                $query_edit_user_name->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_edit_user_name->execute();

                if ($query_edit_user_name->rowCount()) {
						$_SESSION['user_fullname'] = $user_fullname;
                    $this->messages[] = "Full names changed succesfully: " . $user_fullname;
                } else {
                    $this->errors[] = "Full names change failed.";
                }
			}
        }
    }

    /**
     * Edit the user's phone, provided in the editing form
     */
    public function editUserPhone($user_phone, $user_phone_old, $user_name)
    {
        // prevent database flooding
        $user_phone = substr(trim($user_phone), 0, 10);
        $user_name = substr(trim($user_name), 0, 64);

        if (!empty($user_phone) && $user_phone == $user_phone_old) {
            $this->errors[] = "The old and new phone numbers are the same.";
        } else {
        	if ($this->databaseConnection()) {
                // write user's new data into database
                $query_edit_user_phone = $this->db_connection->prepare('UPDATE web_users SET user_phone = :user_phone WHERE user_name = :user_name');
                $query_edit_user_phone->bindValue(':user_phone', $user_phone, PDO::PARAM_INT);
                $query_edit_user_phone->bindValue(':user_name', $user_name, PDO::PARAM_STR);
                $query_edit_user_phone->execute();

                if ($query_edit_user_phone->rowCount()) {
						$_SESSION['user_phone'] = $user_phone;
                    $this->messages[] = "The phone number changed successfully: " . $user_phone;
                } else {
                    $this->errors[] = "Phone number change failed.";
                }
			}
        }
    }

    /**
     * Edit the user's email, provided in the editing form
     */
    public function editUserEmail($user_email, $user_email_old)
    {
        // prevent database flooding
        $user_email = substr(trim($user_email), 0, 64);
        $user_email_old = substr(trim($user_email_old), 0, 64);

        if (!empty($user_email) && $user_email == $user_email_old) {
            $this->errors[] = "The old and new email addresses are the same.";
        // user mail cannot be empty and must be in email format
        } elseif (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email address";

        } else if ($this->databaseConnection()) {
            // check if new email already exists
            $query_user = $this->db_connection->prepare('SELECT * FROM web_users WHERE user_email = :user_email');
            $query_user->bindValue(':user_email', $user_email, PDO::PARAM_STR);
            $query_user->execute();
            // get result row (as an object)
            $result_row = $query_user->fetchObject();

            // if this email exists
            if (isset($result_row->user_id)) {
                $this->errors[] = "The email address already exist";
            } else {
                // write users new data into database
                $query_edit_user_email = $this->db_connection->prepare('UPDATE web_users SET user_email = :user_email WHERE user_email = :user_email_old');
                $query_edit_user_email->bindValue(':user_email', $user_email, PDO::PARAM_STR);
                $query_edit_user_email->bindValue(':user_email_old', $user_email_old, PDO::PARAM_INT);
                $query_edit_user_email->execute();

                if ($query_edit_user_email->rowCount()) {
					if(strcmp($user_email_old, $_SESSION['user_email']) == 0)
						$_SESSION['user_email'] = $user_email;
                    $this->messages[] = "Email changed successfully!" . $user_email;
                } else {
                    $this->errors[] = "Email change failed.";
                }
            }
        }
    }


    /**
     * Edit the user's password, provided in the editing form
     */
    public function editUserPassword($user_password_old, $user_password_new, $user_password_repeat)
    {
        if (empty($user_password_new) || empty($user_password_repeat) || empty($user_password_old)) {
            $this->errors[] = "Password change fileds can't be empty";
        // is the repeat password identical to password
        } elseif ($user_password_new !== $user_password_repeat) {
            $this->errors[] = "Repeat the correct password.";
        // password need to have a minimum length of 6 characters
        } elseif (strlen($user_password_new) < 6) {
            $this->errors[] = "The password must be longer than 6 characters";

        // all the above tests are ok
        } else {
            // database query, getting hash of currently logged in user (to check with just provided password)
            $result_row = $this->getUserData($_SESSION['user_name']);

            // if this user exists
            if (isset($result_row->user_password_hash)) {

                // using PHP 5.5's password_verify() function to check if the provided passwords fits to the hash of that user's password
                if (password_verify($user_password_old, $result_row->user_password_hash)) {

                    // now it gets a little bit crazy: check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                    // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                    $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                    // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character hash string
                    // the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using PHP 5.3/5.4, by the password hashing
                    // compatibility library. the third parameter looks a little bit shitty, but that's how those PHP 5.5 functions
                    // want the parameter: as an array with, currently only used with 'cost' => XX.
                    $user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));

                    // write users new hash into database
                    $query_update = $this->db_connection->prepare('UPDATE web_users SET user_password_hash = :user_password_hash WHERE user_id = :user_id');
                    $query_update->bindValue(':user_password_hash', $user_password_hash, PDO::PARAM_STR);
                    $query_update->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $query_update->execute();

                    // check if exactly one row was successfully changed:
                    if ($query_update->rowCount()) {
                        $this->messages[] = "Password changed successfully!";
                    } else {
                        $this->errors[] = "Password change failed";
                    }
                } else {
                    $this->errors[] = "The old password is incorrect";
                }
            } else {
                $this->errors[] = "Please recheck and try again";
            }
        }
    }

    /**
     * Gets the username
     * @return string username
     */
    public function getUsername()
    {
        return $this->user_name;
    }

     /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     * Gravatar is the #1 (free) provider for email address based global avatar hosting.
     * The URL (or image) returns always a .jpg file !
     * For deeper info on the different parameter possibilities:
     * @see http://de.gravatar.com/site/implement/images/
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 50px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public function getGravatarImageUrl($email, $s = 50, $d = 'mm', $r = 'g', $atts = array() )
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        //$url .= "?s=$s&d=$d&r=$r&f=y";
        $url .= "?s=32";

        // the image url (on gravatarr servers), will return in something like
        // http://www.gravatar.com/avatar/205e460b479e2e5b48aec07710c08d50?s=80&d=mm&r=g
        // note: the url does NOT have something like .jpg
        $this->user_gravatar_image_url = $url;

        // build img tag around
        //$url = '<img src="' . $url . '"';
        //foreach ($atts as $key => $val)
            //$url .= ' ' . $key . '="' . $val . '"';
        //$url .= ' />';

        // build circular css style around the image with shadow :D
        $url = '<img style="
            border-radius: 150px;
            -webkit-border-radius: 150px;
            -moz-border-radius: 150px;
            box-shadow: 0 0 8px rgba(0, 0, 0, .8);
            -webkit-box-shadow: 0 0 8px rgba(0, 0, 0, .8);
            -moz-box-shadow: 0 0 8px rgba(0, 0, 0, .8);
            " src="' . $url .'" alt=""/>';

        // the image url like above but with an additional <img src .. /> around
        $this->user_gravatar_image_tag = $url;
    }
}
