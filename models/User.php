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
    /// @var string $password Hashed password
    public $password;
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

    public function __construct(
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

    public static function from_userId(int $userId): ?User
    {
        $result = Database::getInstance()
            ->query('select firstName,lastName,username,password,
                            email,type,description,date,country 
                     from `Users` where userId = ' . $userId);

        if ($result) {
            $row = $result->fetch_assoc();
            return new User(
                $userId,
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

    public function insert_user(): void
    {
        if ($this->is_valid()) {
            // TODO: insert user
        }
    }

    public function delete_user(): void
    {
        // TODO: delete user
    }

    public function update_user(): void
    {
        // TODO: update user
    }

    public static function check_credentials(string $username, string $password): bool
    {
        // TODO: validate hashed password
    }


}
