<?php

include_once '../prelude.php';

class Rating
{
    /// @var int
    public $articleId;
    /// @var bool
    public $like;
    /// @var ?int
    public $reviewBy;
    /// @var string
    public $ipAddress;
    /// @var string
    public $date;

    public function __construct(
        int $articleId,
        bool $like,
        ?int $reviewBy,
        ?string $ipAddress = null,
        ?string $date = null
    ) {
        $this->articleId = $articleId;
        $this->like = $like;
        if (isset($reviewBy))
            $this->reviewBy = $reviewBy;
        else
            $this->reviewBy = -1;
        if (isset($ipAddress))
            $this->ipAddress = $ipAddress;
        else
            $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        if (isset($date))
            $this->date = $date;
        else
            $this->date = date('Y-m-d\TH:i:s');
    }

    public function upsert_rating(): bool
    {
        $db = Database::getInstance();
        $result = $db->pquery(
            'insert into Ratings values (?,?,?,?,?)
             on duplicate key update `like` = ?, date = ?',
            'iiissis',
            $this->articleId,
            (int) $this->like,
            $this->reviewBy,
            $this->ipAddress,
            $this->date,
            (int) $this->like,
            $this->date
        );
        return $result;
    }

    /**
     * @return array `array('likes' => int, 'dislkes' => int)`
     */
    public static function get_article_ratings(int $articleId): array
    {
        $db = Database::getInstance();
        $result =
            $db->query("SELECT COUNT(CASE `like` when 1 then 1 else null end) as likes,
                               COUNT(CASE `like` when 0 then 1 else null end) as dislikes
                        FROM `Ratings` WHERE articleId = $articleId;");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_array();
            return array('likes' => (int) $row[0], 'dislikes' => (int) $row[1]);
        } else
            return array('likes' => 0, 'dislikes' => 0);
    }

    public static function get_user_rating(int $articleId): ?bool
    {
        $db = Database::getInstance();

        $sql = "SELECT `like` FROM `Ratings` WHERE articleId = $articleId";

        if (isset($_SESSION['userId']))
            $sql .= " and reviewBy = {$_SESSION['userId']}";
        else
            $sql .= " and ipAddress = '{$db->escape($_SERVER['REMOTE_ADDR'])}'";

        $result = $db->query($sql);
        if ($result && $result->num_rows > 0) {
            return (bool) $result->fetch_array()[0];
        } else
            return null;
    }

}