<?php

include_once '../prelude.php';

class Rating
{
    /// @var int
    public $ratingId;
    /// @var int
    public $articleId;
    /// @var bool
    public $like;
    /// @var int
    public $reviewBy;
    /// @var string
    public $ipAddress;
    /// @var string
    public $date;

    public function __construct(
        int $ratingId,
        int $articleId,
        bool $like,
        int $reviewBy,
        string $ipAddress = $_SERVER['REMOTE_ADDR'],
        string $date = date('Y-m-d\TH:i:s'), )
    {
        $this->ratingId = $ratingId;
        $this->articleId = $articleId;
        $this->like = $like;
        $this->reviewBy = $reviewBy;
        $this->ipAddress = $ipAddress;
        $this->date = $date;
    }

    public function insert_rating(): ?int
    {
        $db = Database::getInstance();
        $id = $db->pquery_insert(
            'insert into Ratings values (NULL,?,?,?,?,?)',
            'iiiss',
            $this->articleId,
            $this->like,
            $this->reviewBy,
            $this->ipAddress,
            $this->date
        );
        return $id;
    }

    /**
     * @return array `array('likes' => int, 'dislkes' => int)`
     */
    public static function get_article_ratings(int $articleId): ?array
    {
        $db = Database::getInstance();
        $result =
            $db->query("SELECT COUNT(CASE `like` when 1 then 1 else null end) as likes,
                               COUNT(CASE `like` when 0 then 1 else null end) as dislikes
                        FROM `Ratings` WHERE articleId = $articleId;");
        if ($result) {
            $row = $result->fetch_array();
            return array('likes' => (int) $row[0], 'dislikes' => (int) $row[1]);
        } else
            return null;
    }

}