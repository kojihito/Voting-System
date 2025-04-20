<?php

require_once '../Models/admin.php';
require_once '../Models/voter.php';
require_once '../Controllers/DBController.php';

class AuthController
{
    protected $db;

    // _______________________________________ Methods _______________________________________
    public function Login(Voter $voter)
{
    $this->db = new DBController;
    if ($this->db->OpenConnection()) {
        $getIdEmail = $voter->getVoterEmail();
        $getPassword = $voter->getVoterPassword();
        
        // Kiểm tra bảng admins trước
        $queryAdmin = "SELECT * FROM admins WHERE Email = '$getIdEmail' AND password = '$getPassword'";
        $resultAdmin = $this->db->Select($queryAdmin);
        if ($resultAdmin && count($resultAdmin) > 0) {
            session_start();
            $_SESSION["userEmail"] = $getIdEmail;
            $_SESSION["userRole"] = $resultAdmin[0]["roleId"]; // roleId = 1 cho admin
            $_SESSION["userId"] = $resultAdmin[0]["ID"];
            $this->db->CloseConnection();
            return true;
        }
        
        // Nếu không phải admin, kiểm tra bảng voter
        $queryVoter = "SELECT * FROM voter WHERE Email = '$getIdEmail' AND password = '$getPassword'";
        $resultVoter = $this->db->Select($queryVoter);
        if ($resultVoter && count($resultVoter) > 0) {
            session_start();
            $_SESSION["userEmail"] = $getIdEmail;
            $_SESSION["userRole"] = $resultVoter[0]["roleId"]; // roleId = 2 cho voter
            $_SESSION["userId"] = $resultVoter[0]["ID"];
            $_SESSION["voterId"] = $resultVoter[0]["ID"];
            $_SESSION["voter_idproof"] = $resultVoter[0]["id_proof"];
            $this->db->CloseConnection();
            return true;
        } else {
            $_SESSION["errMsg"] = "Wrong email or password";
            $this->db->CloseConnection();
            return false;
        }
    } else {
        echo "Error in Database Connection";
        return false;
    }
}

    public function register(Voter $voter)
    {
        $this->db = new DBController;
        if($this->db->OpenConnection())
        {
            $getName = $voter->getVoterName();
            $getId = $voter->getVoterIdProof();
            $getPasseord = $voter->getVoterPassword();
            $getEmail = $voter->getVoterEmail();
            $query = "insert into voter (name, id_proof, Password, Email, roleId) VALUES ('$getName', '$getId', '$getPasseord', '$getEmail', 2);";
            // $result = $this->db->insert($query);
            if($this->db->insert($query))
            {
                return true;
            }
            else
            {
                $_SESSION["errMsg"] = "Something went wrong";
                return false;
            }
            
        }
        else
        {
            echo "Connection Error";
            return false;
        }
        
    }




}
  
?>