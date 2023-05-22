<?php

include_once '../prelude.php';

class User
{
    /// @var int $userId
    public $userId;
    /// @var string
    public $firstName;
    /// @var string
    public $lastName;
    /// @var string
    public $username;
    /// @var string password Hashed password
    private $password;
    /// @var string
    public $email;
    /// @var string
    public $type;
    /// @var ?string $description User description for author types
    public $description;
    /// @var string $date Date of registration
    public $date;
    /// @var string
    public $country;

    private function __construct(
        int    $userId,
        string $firstName,
        string $lastName,
        string $username,
        string $password,
        string $email,
        string $type,
        ?string $description,
        string $date,
        string $country
    ) {
        $this->userId=$userId;
        $this->firstName=$firstName;
        $this->lastName=$lastName;
        $this->username=$username;
        $this->password=$password;
        $this->email=$email;
        $this->type=$type;
        $this->description=$description;
        $this->date=$date;
        $this->country=$country;
    }

    public static function register_user(
        string $firstName,
        string $lastName,
        string $username,
        string $plain_password,
        string $email,
        string $type,
        ?string $description,
        string $country
    ): bool {
        if (User::username_exists($username)) {
            return false;
        } else {
            $password = password_hash($plain_password, PASSWORD_DEFAULT);
            $date = date('Y-m-d\TH:i:s');

            return Database::getInstance()->pquery(
                'insert into Users (userId, firstName, lastName, username, password, email, type, 
                                description, date, country)
             values (?,?,?,?,?,?,?,?,?,?);',
                'isssssssss',
                null,
                $firstName,
                $lastName,
                $username,
                $password,
                $email,
                $type,
                $description,
                $date,
                $country
            );
        }
    }

    public static function username_exists(string $username): bool
    {
        $db = Database::getInstance();
        return !empty($db
          ->query("select username from Users where username = '" . $db->mysqli->real_escape_string($username) . "'")
          ->fetch_row());
    }

    public static function from_username(string $username): ?User
    {
        $db = Database::getInstance();

        $result = $db
            ->query('select userId,firstName,lastName,username,password,
                            email,type,description,date,country 
                     from `Users` where username = \'' . $db->mysqli->real_escape_string($username) . '\'');

        $row = $result->fetch_assoc();
        if ($row != null) {
            return new User(
                $row['userId'],
                $row['firstName'],
                $row['lastName'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['type'],
                $row['description'],
                $row['date'],
                $row['country']
            );
        } else {
            return null;
        }

    }

    public function is_valid(): bool
    {
        // TODO: validate user
        return true;
    }

    public function delete_user(): void
    {
        // TODO: delete user
    }

    public function update_user(): void
    {
        // TODO: update user
    }


    public function set_password(string $plaintext): void
    {
        $this->password = password_hash($plaintext, PASSWORD_DEFAULT);
    }

    public static function check_credentials(string $username, string $password): bool
    {
        $user = User::from_username($username);
        if ($user == null) {
            return false;
        }
        return password_verify($password, $user->password);
    }


}
