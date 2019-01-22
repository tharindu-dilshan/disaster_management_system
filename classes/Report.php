<?php
class Report
{
    private $db_connection      = null;
    public  $submit_successful  = false;
    public  $errors             = array();
    public  $messages           = array();
    public  $image_error        = false;
    public  $images             = array();

    public function __construct()
    {
        // if we have such a POST request, call the registerNewUser() method
        if (isset($_POST["newreport"])) {
            $this->submitNewReport($_POST['title'], $_POST['description'], $_POST['latitude'], $_POST['longitude']);
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
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
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
    private function submitNewReport($title, $description, $latitude, $longitude)
    {
        $this->imagecheck();
        // check provided data validity
        if (empty($title)) {
            $this->errors[] = "The title field can't be empty.";
        } elseif (!isset($_POST['category'])) {
            $this->errors[] = "Select a category.";
        } elseif (strlen($description) < 5) {
            $this->errors[] = "The description must be at least 5 characters long.";
        } elseif ($this->image_error == true) {
             $this->errors[] = "One or more of the files are invalid.";
        // finally if all the above checks are ok
        } else if ($this->databaseConnection()) {

            // 1. Write new report to the database
            $sql1 = "
                INSERT INTO web_reports (time_submitted, status, submitter_id) VALUES(now(), :status, :submitter_id)
                    ";
            $report_insert = $this->db_connection->prepare($sql1);
            $report_insert->bindValue(':status','Submited',PDO::PARAM_STR);
            $report_insert->bindValue(':submitter_id',$_SESSION['user_id'],PDO::PARAM_STR);
            $report_insert->execute();

            // 2. Write new report's details to the database
            $report_id = $this->db_connection->lastInsertId();

            $sql2 = "
                INSERT INTO web_report_details ( report_id, title, description, latitude, longitude, category_id )
                                        VALUES (?,?,?,?,?,?)
                    ";
					
            $report_details_insert = $this->db_connection->prepare($sql2);
            $report_details_insert->bindValue( 1,   $report_id,         PDO::PARAM_STR);
            $report_details_insert->bindValue( 2,   $title,             PDO::PARAM_STR);
            $report_details_insert->bindValue( 3,   $description,       PDO::PARAM_STR);
            $report_details_insert->bindValue( 4,   $latitude,          PDO::PARAM_STR);
            $report_details_insert->bindValue( 5,   $longitude,         PDO::PARAM_STR);
            $report_details_insert->bindValue( 6,   $_POST['category'], PDO::PARAM_STR);
            $report_details_insert->execute();

            // 3. Write new report's images  to the database
            foreach ($this->images as $image) {
                $temp = explode(".", $image["name"]);
                $extension = end($temp);
                $enc_name = md5($image["name"]);
                $name = $enc_name . "." .$extension;
                $dbPath = "views/images/" . $name;
                $path = getcwd() . '/' . $dbPath;
                $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);

                move_uploaded_file($image["tmp_name"], $path);

                $sql3 = "
                    INSERT INTO web_report_images (path, report_id) VALUES(?,?)
                        ";
                $image_insert = $this->db_connection->prepare($sql3);
                $image_insert->bindValue(1,    $dbPath,      PDO::PARAM_STR);
                $image_insert->bindValue(2,    $report_id, PDO::PARAM_STR);
                $image_insert->execute();
            }

            $this->messages[] = "New report added successfully!";
            $this->submit_successful = true;
        }
    }

    public function imagecheck() {
        $allowedExts = array("jpeg", "jpg", "png", "gif");

        if(!empty($_FILES["images"]['name'][0])) {
            $this->images = reArrayFiles($_FILES["images"]);

            foreach ($this->images as $image) {
                $temp = explode(".", $image["name"]);
                $extension = end($temp);

                if ((($image["type"] == "image/jpeg")
                        || ($image["type"] == "image/jpg")
                        || ($image["type"] == "image/pjpeg")
                        || ($image["type"] == "image/x-png")
                        || ($image["type"] == "image/gif")
                        || ($image["type"] == "image/png"))
                        && ($image["size"] < 5000000)
                        && in_array($extension, $allowedExts)
                        && ($image["error"] <= 0 )) {
                            $this->image_error = false;
                        }else{
                            $this->image_error = true;
                        }
            }
        }
    }

}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}
