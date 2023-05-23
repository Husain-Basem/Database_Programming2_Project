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
        ?int $fileId,
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

    /**
     * Upload file and insert into database
     *
     * Usage:
     *
     * ```php
     * // last three arguments are the important
     * $file = new File(null,'','','',false,1,2);
     * $fileLocation = $file->upload_file('pic');
     * ```
     *
     * @return string|null
     */
    public function upload_file(string $formname): ?string
    {
        if (isset($_FILES[$formname])) {
            $location = PROJECT_ROOT . "/uploads/{$_FILES[$formname]['name']}";
            if (file_exists($location)) {
                $location = PROJECT_ROOT . '/uploads/' . random_int() . $_FILES[$formname]['name'];
            }
            if (move_uploaded_file($_FILES[$formname]['tmp_name'], $location)) {
                $this->fileName = $_FILES[$formname]['name'];
                $this->fileLocation = $location;
                $this->fileType = $_FILES[$formname]['type'];
                $this->insert_file();
                return $location;
            }
        }

        return null;

    }

    public function insert_file(): ?int
    {
        if ($this->is_valid()) {
            $date = date('Y-m-d\TH:i:s');
            $db = Database::getInstance();
            return $db->pquery_insert(
                'insert into Files values (NULL,?,?,?,?,?,?)',
                'ssssii',
                $this->fileName,
                $this->fileType,
                $this->fileLocation,
                $this->downloadable,
                $this->articleId,
                $this->userId
            );
        }
    }

    public function delete_file(): bool
    {
        $db = Database::getInstance();
        return $db->pquery('delete from Files where fileId = ?', 'i', $this->fileId);
    }

    public function update_file(): bool
    {
        $date = date('Y-m-d\TH:i:s');
        $db = Database::getInstance();
        return $db->pquery(
            'update Files set fileName = ?, fileType = ?,
             fileLocation = ?, downloadable = ?, 
              articleId = ?, userId = ? where fileId = ?',
            'ssssiii',
            $this->fileName,
            $this->fileType,
            $this->fileLocation,
            $this->downloadable,
            $this->articleId,
            $this->userId,
            $this->fileId
        );
    }
}
