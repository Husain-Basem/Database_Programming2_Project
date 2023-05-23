<?php

include_once '../prelude.php';

class Comment
{
    /// @var int
    public $commentId;
    /// @var string
    public $comment;
    /// @var string
    public $rating;
    /// @var int
    public $reviewBy;
    /// @var string
    public $date;
    /// @var int
    public $articleId;

    public function __construct(
        int $commentId,
        string $comment,
        string $rating,
        int $reviewBy,
        string $date,
        int $articleId
    ) {
        $this->commentId = $commentId;
        $this->comment = $comment;
        $this->rating = $rating;
        $this->reviewBy = $reviewBy;
        $this->date = $date;
        $this->articleId = $articleId;
    }

    public function is_valid(): bool
    {
        // TODO: validate comment
        return true;
    }

    public function insert_comment(): ?int
    {
        if ($this->is_valid()) {
            $date = date('Y-m-d\TH:i:s');
            $db = Database::getInstance();
            return $db->pquery_insert(
                'insert into Comments values (NULL,?,?,?,?,?)',
                'ssisi',
                $this->comment,
                $this->rating,
                $this->reviewBy,
                $date,
                $this->articleId
            );
        }
    }

    public function delete_comment(): bool
    {
        $db = Database::getInstance();
        return $db->pquery('delete from Comments where commentId = ?', 'i', $this->commentId);
    }

    public function update_comment(): bool
    {
        $date = date('Y-m-d\TH:i:s');
        $db = Database::getInstance();
        return $db->pquery(
            'update Comments set comment = ?, rating = ?, reviewBy = ?,
                    date = ?, articleId = ?) where commentId = ?',
            'ssisii',
            $this->comment,
            $this->rating,
            $this->reviewBy,
            $this->date,
            $this->articleId,
            $this->commentId
        );
    }
}
