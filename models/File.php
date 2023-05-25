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
    /// @var string $fileLocation    absolute file path where the root is this project's root. e.g. /uploads/file1.txt
    private $fileLocation;
    /// @var bool
    public $downloadable;
    /// @var int
    public $articleId;
    /// @var int
    public $userId;

    private function __construct(
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

    /// gets the absolute file path. e.g. /home/u202001264/public_html/DBProj/uploads/file1.txt
    public function get_fileLocation(): string
    {
        return PROJECT_ROOT . $this->fileLocation;
    }

    public function is_valid(): bool
    {
        // TODO: validate file
        return true;
    }
    /**
     * @return array<File>
     */
    public static function get_files(int $articleId): array
    {
        $db = Database::getInstance();
        $files = $db->query("select * from Files where articleId = $articleId")->fetch_all(MYSQLI_ASSOC);
        $files = array_map(function ($file) {
            return new File(
                $file['fileId'],
                $file['fileName'],
                $file['fileType'],
                $file['fileLocation'],
                $file['downloadable'],
                $file['articleId'],
                $file['userId']
            );
        }, $files);
        return $files;

    }

    /**
     * Upload file and insert into database
     *
     * @return File|null
     */
    public static function upload_file(string $formname, bool $attachment, int $articleId, int $userId): ?File
    {
        if (isset($_FILES[$formname])) {
            $fileName = $_FILES[$formname]['name'];
            $location = PROJECT_ROOT . "/uploads/$fileName";
            if (file_exists($location)) {
                $fileName = random_int(1, 10000) . $_FILES[$formname]['name'];
                $location = PROJECT_ROOT . '/uploads/' . $fileName;
            }
            if (move_uploaded_file($_FILES[$formname]['tmp_name'], $location)) {
                $fileLocation = $location;
                $fileType = $_FILES[$formname]['type'];
                $fileObj = new File(
                    null,
                    $fileName,
                    $fileType,
                    '/uploads/' . $fileName,
                    $attachment,
                    $articleId,
                    $userId
                );
                $fileObj->insert_file();
                return $fileObj;
            }
        }

        return null;

    }

    public function insert_file(): ?int
    {
        if ($this->is_valid()) {
            $date = date('Y-m-d\TH:i:s');
            $db = Database::getInstance();
            $id = $db->pquery_insert(
                'insert into Files values (NULL,?,?,?,?,?,?)',
                'sssiii',
                $this->fileName,
                $this->fileType,
                $this->fileLocation,
                $this->downloadable,
                $this->articleId,
                $this->userId
            );
            $this->fileId = $id;
            return $id;
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

    public function get_url(): string
    {
        return "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}" . BASE_URL . $this->fileLocation;
    }
}