<?php

include_once '../prelude.php';

class Comment
{
    /// @var ?int
    public $commentId;
    /// @var string
    public $comment;
    /// @var int
    public $reviewBy;
    /// @var string
    public $date;
    /// @var int
    public $articleId;
    /// @var bool
    public $removed;

    public function __construct(
        ?int $commentId,
        string $comment,
        int $reviewBy,
        string $date,
        int $articleId,
        bool $removed = false
    ) {
        $this->commentId = $commentId;
        $this->comment = $comment;
        $this->reviewBy = $reviewBy;
        $this->date = $date;
        $this->articleId = $articleId;
        $this->removed = $removed;
    }

    public static function from_commentId(int $commentId): ?Comment
    {
        $db = Database::getInstance();
        $result = $db->query("select * from Comments where commentId = $commentId");
        if ($result != null) {
            $row = $result->fetch_assoc();
            return new Comment(
                (int) $row['commentId'],
                $row['comment'],
                (int) $row['reviewBy'],
                $row['date'],
                (int) $row['articleId'],
                (bool) $row['removed']
            );
        } else
            return null;
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
                'insert into Comments values (NULL,?,?,?,?,0)',
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
                    date = ?, articleId = ?, removed = ? where commentId = ?',
            'sisiii',
            $this->comment,
            $this->reviewBy,
            $this->date,
            $this->articleId,
            $this->removed,
            $this->commentId
        );
    }
}