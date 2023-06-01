<?php

include_once '../prelude.php';

class Comment
{
    /// @var int
    public $commentId;
    /// @var string
    public $comment;
   /// @var int
    public $reviewBy;
    /// @var string
    public $date;
    /// @var int
    public $articleId;

    public function __construct(
        int $commentId,
        string $comment,
        int $reviewBy,
        string $date,
        int $articleId
    ) {
        $this->commentId = $commentId;
        $this->comment = $comment;
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
            $id = $db->pquery_insert(
                'insert into Comments values (NULL,?,?,?,?)',
                'sisi',
                $this->comment,
                $this->reviewBy,
                $date,
                $this->articleId
            );
            return $id;
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
            'update Comments set comment = ?, reviewBy = ?,
                    date = ?, articleId = ?) where commentId = ?',
            'sisii',
            $this->comment,
            $this->reviewBy,
            $this->date,
            $this->articleId,
            $this->commentId
        );
    }
}
