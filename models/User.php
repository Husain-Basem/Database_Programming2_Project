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
    public $userName;
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

    private static function __set_state(array $state): User
    {
        return new User(
            $state['userId'],
            $state['firstName'],
            $state['lastName'],
            $state['userName'],
            $state['password'],
            $state['email'],
            $state['type'],
            $state['description'],
            $state['date'],
            $state['country']
        );
    }

    private function __construct(
        int $userId,
        string $firstName,
        string $lastName,
        string $userName,
        string $password,
        string $email,
        string $type,
        ?string $description,
        string $date,
        string $country
    ) {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->userName = $userName;
        $this->password = $password;
        $this->email = $email;
        $this->type = $type;
        $this->description = $description;
        $this->date = $date;
        $this->country = $country;
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
                'insert into Users (userId, firstName, lastName, userName, password, email, type, 
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
            ->query('select * from `Users` where username = \'' . $db->escape($username) . '\'');

        $row = $result->fetch_assoc();
        if ($row != null) {
            return User::__set_state($row);
        } else {
            return null;
        }
    }

    public static function from_userId(int $userId): ?User
    {
        $db = Database::getInstance();
        $result = $db->query('select * from `Users` where userId = ' . $userId);
        $row = $result->fetch_assoc();
        if ($row != null) {
            return User::__set_state($row);
        } else {
            return null;
        }
    }

    public static function from_email(string $email): ?User
    {
        $db = Database::getInstance();
        $result = $db->query('select * from `Users` where email = \'' . $db->escape($email) . '\'');
        $row = $result->fetch_assoc();
        if ($row != null) {
            return User::__set_state($row);
        } else {
            return null;
        }
    }

    public function is_viewer(): bool
    {
        return $this->type == 'VIEWER';
    }
    public function is_author(): bool
    {
        return $this->type == 'AUTHOR';
    }
    public function is_admin(): bool
    {
        return $this->type == 'ADMIN';
    }

    public function is_valid(): bool
    {
        // TODO: validate user
        return true;
    }

    public function delete_user(): bool
    {
        $db = Database::getInstance();
        return $db->pquery('delete from Users where userId = ?', 'i', $this->userId);
    }

    public function update_user(): bool
    {
        $db = Database::getInstance();
        return $db->pquery('update Users set email = ?, description = ? where userId = ?', 'ssi', $this->email, $this->description, $this->userId);
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