<?php

include_once '../prelude.php';

class File
{
    /// @var int
    public $fileId;
    /// @var string
    public $fileName;
    /// @var string
    public $fileType;
    /// @var string
    public $fileLocation;
    /// @var bool
    public $downloadable;
    /// @var int
    public $articleId;
    /// @var int
    public $userId;

    public function __construct(
        int $fileId,
        string $fileName,
        string $fileType,
        string $fileLocation,
        bool $downloadable,
        int $articleId,
        int $userId
    ) {
        $this->fileId = $fileId;
        $this->fileName = $fileName;
        $this->fileType = $fileType;
        $this->fileLocation = $fileLocation;
        $this->downloadable = $downloadable;
        $this->articleId = $articleId;
        $this->userId = $userId;
    }
    public function is_valid(): bool
    {
        // TODO: validate file
        return true;
    }

    public function insert_user(): void
    {
        if ($this->is_valid()) {
            // TODO: insert file
        }
    }

    public function delete_user(): void
    {
        // TODO: delete file
    }

    public function update_user(): void
    {
        // TODO: update file
    }
}
