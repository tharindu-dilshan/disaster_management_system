<?php
class adminControls
{
    private $db_connection      = null;
    public  $submit_successful  = false;
    public  $errors             = array();
    public  $messages           = array();
    public  $categories			= array();
    public  $users				= array();
    public  $showpage           = true;

    public function __construct()
    {
		//populate categories table
		$this->fetchCategories();

		//populate users table
		$this->fetchUsers();

        // if we have such a POST request, call the markReportClosed() method
        if (isset($_POST["markClosed"])) {
            $this->markReportClosed($_POST['report_id'], $_POST['comment']);
        }
        
        // if we have such a POST request, call the markReportOpened() method
        if (isset($_POST["markOpen"])) {
            $this->markReportOpened($_POST['report_id']);
        }

		// handler for the add category
		if (isset($_POST['categorySubmit']) && isset($_POST['categoryName'])) {
			$this->addCategory($_POST['categoryName']);
			// in order to return in ajax post only the required span and not the whole dashboard
			$this->showpage = false;
		}

		// handler for the edit category name
		if (isset($_POST['newName'])) {
			$this->changeCategoryName($_POST['category_id'], $_POST['newName']);
		}

		// handler for the delete category
		if (isset($_POST['del_cat_id'])) {
			$this->deleteCategory($_POST['del_cat_id']);
		}

		// handler for the delete user
		if (isset($_POST['del_user_id'])) {
			$this->deleteUser($_POST['del_user_id']);
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
                $this->errors[] = "Database Error!";
                return false;
            }
        }
    }

    private function markReportClosed($id, $comment)
    {
        if ($this->databaseConnection()) {
            // 1. Mark report as closed in web_reports table
            $sql1 = "
                UPDATE web_reports SET status=:stat, time_closed=NOW(), closer_id=:cid, comment=:comment WHERE id=:report_id
                ";
            $report_close = $this->db_connection->prepare($sql1);
            $report_close->bindValue(':stat',   "Closed",   PDO::PARAM_STR);
            $report_close->bindValue(':cid', $_SESSION["user_id"],   PDO::PARAM_STR);
            $report_close->bindValue(':comment', $comment,   PDO::PARAM_STR);
            $report_close->bindValue(':report_id',   $id,   PDO::PARAM_STR);
            $report_close->execute();

            // 2. Add comment in web_report_details
            //$sql2 = "
            //UPDATE web_report_details SET comment=:comment WHERE report_id=:report_id
            //";
            //$report_comment = $this->db_connection->prepare($sql2);
            //$report_comment->bindValue(':comment',   $comment,   PDO::PARAM_STR);
            //$report_comment->bindValue(':report_id',   $id,   PDO::PARAM_STR);
            //$report_comment->execute();

            $this->messages[] = "Reference Number " . $id . " Archived.";
            $this->submit_successful = true;

        }
    }
    
    
    private function markReportOpened($id)
    {
        if ($this->databaseConnection()) {
            // 1. Mark report as Open in web_reports table
            $sql1 = "
                UPDATE web_reports SET status=:stat WHERE id=:report_id
                ";
            $report_open = $this->db_connection->prepare($sql1);
            $report_open->bindValue(':stat',   "Open",   PDO::PARAM_STR);
            $report_open->bindValue(':report_id',   $id,   PDO::PARAM_STR);
            $report_open->execute();

            $this->messages[] = "Reference Number " . $id . " Opened.";
            $this->submit_successful = true;
        }
    }

	private function addCategory($categoryName)
	{
		if ($this->databaseConnection()) {
			$pdo = $this->db_connection;
			$catStmt = $pdo->prepare('SELECT count(*) FROM web_categories WHERE name=:catName');
			$catStmt->bindParam('catName', $categoryName);
			if ($catStmt->execute()) {
				$row = $catStmt->fetch(PDO::FETCH_NUM);
				$nrows = $row[0];
			}

			// only if category doesn't exist, add it
			if ($nrows == 0) {
				$addStmt = $pdo->prepare('INSERT INTO web_categories (name) VALUES (:catName)');
				$addStmt->bindParam('catName', $categoryName);
				if (!$addStmt->execute()) {
					die('Error!');
				}

				$fetchStmt = $pdo->prepare('SELECT * FROM web_categories WHERE name=:catName');
				$fetchStmt->bindParam('catName', $categoryName);
				if ($fetchStmt->execute()) {
					$row = $fetchStmt->fetch();
				    echo '
					<div class="btn-group btn-group-sm">
					<button id="' .$row['id']. '" type="button" class="category btn btn-default">
                                        <span class="editable">
					' .$row['name']. '</span></button>
					<button type="button" class="catRemove btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button>
					</div>';
				}
			}
		}
	}

	private function changeCategoryName($category_id, $newName)
	{
            if ($this->databaseConnection()) {
                $pdo = $this->db_connection;
                $catStmt = $pdo->prepare('SELECT count(*) FROM web_categories WHERE name=:catName');
                $catStmt->bindParam('catName', $newName);
                if ($catStmt->execute()) {
                    $row = $catStmt->fetch(PDO::FETCH_NUM);
                    $nrows = $row[0];
                }

                if ($nrows === 0) {
                    $catStmt = $pdo->prepare('UPDATE web_categories SET name=:catName WHERE id=:cat_id');
                    $catStmt->bindParam('catName', $newName);
                    $catStmt->bindParam('cat_id', $category_id);
                    if (!$catStmt->execute()) {
                        die('Error!');
                    }
                }
            }
	}

	private function deleteCategory($category_id)
	{
		if ($this->databaseConnection()) {
			$pdo = $this->db_connection;

			//reports with this category must be set as Uncategorised
			$repStmt = $pdo->prepare('UPDATE web_report_details SET category_id=1 WHERE category_id=:cat_id');
			$repStmt->bindParam('cat_id', $category_id);
			if (!$repStmt->execute()) {
				die('Error!');
			}

			$catStmt = $pdo->prepare('DELETE FROM web_categories WHERE id=:cat_id');
			$catStmt->bindParam('cat_id', $category_id);
			if (!$catStmt->execute()) {
				die('Error!');
			}
		}
	}

	private function deleteUser($user_id)
	{
		if ($this->databaseConnection()) {
			$pdo = $this->db_connection;
			$checkStmt = $pdo->prepare('SELECT user_type FROM web_users WHERE user_id=:user_id');
			$checkStmt->bindParam('user_id', $user_id);
			if ($checkStmt->execute()) {
				$row = $checkStmt->fetch();

				if ($row['user_type'] == 0) {
                                        $userStmt = $pdo->prepare('DELETE FROM web_users WHERE user_id=:user_id');
                                        $userStmt->bindParam('user_id', $user_id);
                                        if (!$userStmt->execute()) {
                                                die('Error!');
                                        }
					$this->messages[] = "User ID " . $user_id . " Successfully Deleted";
				}else{
					$this->errors[] = "Oops! You can't delete the Administrator";
				}
			}
		}
	}

	private function fetchCategories()
	{
		if ($this->databaseConnection()) {
			$pdo = $this->db_connection;
			//fetch the first 100000000 categories excluded the uncategorized category
			$catStmt = $pdo->prepare('SELECT * FROM web_categories LIMIT 1,1000000');
			if ($catStmt->execute()) {
				$this->categories = $catStmt->fetchAll();
			}

		}

	}

	private function fetchUsers()
	{
		if ($this->databaseConnection()) {
			$pdo = $this->db_connection;
			$catStmt = $pdo->prepare('SELECT * FROM web_users');
			if ($catStmt->execute()) {
				$this->users = $catStmt->fetchAll();
			}

		}

	}


}
