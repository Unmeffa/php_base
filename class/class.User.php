<?php
class User
{
    private $login;
    private $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function authenticate()
    {
        $pdo = DB::getInstance();
        $tablePrefix = DB::getTablePrefix();

        $query = "SELECT password FROM " . $tablePrefix . "_users where login = :login";
        $request = $pdo->prepare($query);
        $request->execute(['login' => $this->login]);

        $user = $request->fetch(PDO::FETCH_ASSOC);
        return $user && password_verify($this->password, $user['password']);
    }

    public function startSession()
    {
        if ($this->authenticate()) {
            session_start();
            $_SESSION["user"] = [
                'login' => $this->login,
                'is_logged_in' => true
            ];
            return true;
        }
        return false;
    }

    public static function checkSession()
    {
        session_start();
        return isset($_SESSION["user"]) && $_SESSION["user"]["is_logged_in"];
    }

    public static function endSession()
    {
        session_start();
        session_unset();
        session_destroy();
    }
}
