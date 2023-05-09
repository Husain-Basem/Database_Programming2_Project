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

    public function insert_user(): void
    {
        if ($this->is_valid()) {
            // TODO: insert comment
        }
    }

    public function delete_user(): void
    {
        // TODO: delete comment
    }

    public function update_user(): void
    {
        // TODO: update comment
    }
}
