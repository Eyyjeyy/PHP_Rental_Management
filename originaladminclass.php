<?php
Class Admin {
    public $conn; // Declare the connection variable
    // public $loggedIn = 3; // Define the loggedIn property
    public $session_uname;
    public $session_pword;
    public $session_id;
    public $session_role;

    public function __construct()
    {
      
      session_start();
      include 'db_connect.php';
      $this->conn = $conn; // Assign the connection to the class property
      $this->handleRedirect();
    }
  
    public function isLoggedIn() {
      // You can still implement a more secure isLoggedIn logic here if needed
      // However, for the current functionality, we can directly check the session
      if(isset($_POST['login'])) {
        $_SESSION["username"] = htmlspecialchars($_POST['username']);
        $_SESSION["password"] = htmlspecialchars($_POST['password']);
      }
      if(isset($_SESSION["username"]) && $_SESSION["username"]) {
        // echo $_SESSION["username"];
        $this->session_uname = $_SESSION["username"];
        $this->session_pword = $_SESSION["password"];
        // $sql = "SELECT * FROM users WHERE username = '$this->session_uname' AND password = '$this->session_pword'";
        // $query = $this->conn->query($sql);

        // Using prepared statements to prevent SQL injection
        $sql = "SELECT * FROM users WHERE BINARY username = ? AND BINARY password = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $this->session_uname, $this->session_pword);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
          $row = $result->fetch_array();
          // print_r($row);
          $this->session_id = $row['id'];
          $this->session_role = $row['role']; // Fetch and store the role
          $_SESSION['role'] = $row['role']; // Store role in session
          // return $row['id'];
          return true;
        } else {
          return false;
        }

        return true;
      } else {
        return false;
      }
      return isset($_SESSION['logged_in']); // Check if 'logged_in' session variable exists
    }
  
    public function handleRedirect()
    {
      if (session_status() === PHP_SESSION_ACTIVE) {
        return; // Skip redirection if session is active
      }
    
      if (!$this->isLoggedIn()) {
        header("Location: login.php"); // Redirect to login.php if not logged in
        exit();
      }
      if ($this->isLoggedIn()) {

        if($this->session_role == 'admin') {
          header("Location: admin/admindashboard.php"); // Redirect to admin dashboard
        } else {
          header("Location: index.php"); // Redirect to user index page
        }
        exit();
      }
    }

    // Function to delete a user by ID
    public function deleteUser($user_id) {
      $sql = "DELETE FROM users WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Deletion successful
      } else {
          return false; // Deletion failed
      }
    }
    
    // Function to update a user's username, password, and role
    public function updateUser($user_id, $username, $password, $role) {
      $sql = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("sssi", $username, $password, $role, $user_id);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Update successful
      } else {
          return false; // Update failed
      }
    }

    // Function to add a new user
    public function addUser($username, $password, $role) {
      $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("sss", $username, $password, $role);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Insertion successful
      } else {
          return false; // Insertion failed
      }
    }

    // Function to delete a house by ID
    public function deleteHouse($house_id) {
      $sql = "DELETE FROM houses WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("i", $house_id);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Deletion successful
      } else {
          return false; // Deletion failed
      }
    }

    public function updateHouse($house_id, $housenumber, $price, $category) {
      $sql = "UPDATE houses SET house_number = ?, price = ?, category_id = ? WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("idii", $housenumber, $price, $category, $house_id);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Update successful
      } else {
          return false; // Update failed
      }
    }

    // Function to add a new house
    public function addHouse($housenumber, $price, $category) {
      $sql = "INSERT INTO houses (house_number, price, category_id) VALUES (?, ?, ?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("idi", $housenumber, $price, $category);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Insertion successful
      } else {
          return false; // Insertion failed
      }
    }

    public function deleteCategory($categoryid) {
      $sql = "DELETE FROM categories WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("i", $categoryid);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Deletion successful
      } else {
          return false; // Deletion failed
      }
    }

    public function updateCategory($categoryid, $categoryname) {
      $sql = "UPDATE categories SET name = ? WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("si", $categoryname, $categoryid);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Update successful
      } else {
          return false; // Update failed
      }
    }

    public function addCategory($categoryname) {
      $sql = "INSERT INTO categories (name) VALUES (?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("s", $categoryname);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Insertion successful
      } else {
          return false; // Insertion failed
      }
    }

    // public function addTenant($firstname, $middlename, $lastname, $contactno, $houseid, $registerdate) {
    //   $sql = "INSERT INTO tenants (fname, mname, lname, contact, house_id, date_start) VALUES (?, ?, ?, ?, ?, ?)";
    //   $stmt = $this->conn->prepare($sql);
    //   $stmt->bind_param("ssssis", $firstname, $middlename, $lastname, $contactno, $houseid, $registerdate);
    //   $stmt->execute();
    //   if ($stmt->affected_rows > 0) {
    //       return true; // Insertion successful
    //   } else {
    //       return false; // Insertion failed
    //   }
    // }

    public function deleteTenant($tenantid) {
      $sql = "DELETE FROM tenants WHERE id = ?";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("i", $tenantid);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Deletion successful
      } else {
          return false; // Deletion failed
      }
    }

    public function addTenant($firstname, $middlename, $lastname, $contactno, $houseid, $housename, $registerdate) {
      $sql = "INSERT INTO tenants (fname, mname, lname, contact, house_id, house_category, date_start) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $stmt = $this->conn->prepare($sql);
      $stmt->bind_param("sssssss", $firstname, $middlename, $lastname, $contactno, $houseid, $housename, $registerdate);
      $stmt->execute();
      if ($stmt->affected_rows > 0) {
          return true; // Insertion successful
      } else {
          return false; // Insertion failed
      }
    }


}