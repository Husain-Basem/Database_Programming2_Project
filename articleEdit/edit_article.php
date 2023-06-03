<?php
declare(strict_types=1);
include_once '../prelude.php';

if (isset($_GET['articleId'])) {
  $article = Article::from_articleId((int) $_GET['articleId']);
}

if (empty($_SESSION['username'])) {
  // redirect to login page that returns to this page
  header('Location: ' . BASE_URL . '/user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$user = User::from_username($_SESSION['username']);

// only allow admins and the author of the article
if (!($user->is_admin() || ($user->is_author() && $article->writtenBy == $user->userId))) {
  $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Unauthorized request');
  session_write_close();
  header('Location: ' . BASE_URL . '/index.php');
}

if ($article->approved && (!$user->is_admin())) {
  $_SESSION['toasts'][] = array('type' => 'danger', 'msg' => 'Cannot Edit a published article');
  session_write_close();
  header('Location: ' . BASE_URL . '/articleEdit/author_panel.php');
}


if (isset($_GET['returnUrl'])) {
  $returnUrl = $_GET['returnUrl'];
  $returnName = $_GET['returnName'];
} else {
  $returnUrl = 'author_panel.php';
  $returnName = 'Author Panel';
}
$pageTitle = 'Article Edit';
$headerIncludes = '
    <link rel="stylesheet" href="' . BASE_URL . '/css/quill.snow.css" />
    <link rel="stylesheet" href="' . BASE_URL . '/css/quill.imageUploader.min.css" />
    <script src="' . BASE_URL . '/js/quill.min.js"></script>
    <script src="' . BASE_URL . '/js/quill.imageUploader.min.js"></script>
    <script src="' . BASE_URL . '/js/quill-blot-formatter.min.js"></script>
';
include PROJECT_ROOT . '/header.html';

?>


<div class="container">
  <?php
  if ($article->removed)
    echo '
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>This article was removed by an administrator.</strong><span class="ms-2">It will be hidden from viewers</span>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
  ';
  ?>

  <div class="row align-items-center mb-3">
    <div class="col-6 hstack gap-2">
      <button id="returnBtn" class="btn btn-link p-0" href="<?= $returnUrl ?>" aria-label="Back to <?= $returnName ?>"
        title="Back to <?= $returnName ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left"
          viewBox="0 0 16 16">
          <path fill-rule="evenodd"
            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
        </svg>
      </button>
      <input type="text" name="title" id="title" class="form-control" required value="<?= $article->title ?>">
      <label for="category" class="form-label">Category:</label>
      <select class="form-select w-50" name="category" id="category">
        <option <?= $article->category == 'local' ? 'selected' : '' ?> value="local">Local News</option>
        <option <?= $article->category == 'international' ? 'selected' : '' ?> value="international">International News
        </option>
        <option <?= $article->category == 'economy' ? 'selected' : '' ?> value="economy">Economy News</option>
        <option <?= $article->category == 'tourism' ? 'selected' : '' ?> value="tourism">Tourism</option>
      </select>
    </div>
    <div class="col hstack gap-3">
      <button id="saveBtn" class="btn btn-primary">Save</button>
      <button id="previewBtn" class="btn btn-outline-primary">
        <?= $user->is_admin() ? 'Review' : 'Preview' ?>
      </button>
      <button id="deleteBtn" class="btn btn-danger ms-auto" data-bs-toggle="modal"
        data-bs-target="#deleteModal">Delete</button>
      <button id="publishBtn" class="btn btn-primary" data-bs-toggle="modal"
        data-bs-target="#publishModal">Publish</button>
    </div>
  </div>

  <div class="d-flex flex-column" style="max-height: calc(100vh - 9rem)">
    <div id="editor" class="h-100 overflow-auto">
    </div>
    <pre id="template" class="visually-hidden"><?= $article->content ?></pre>
  </div>

  <div class="d-flex flex-row my-4">
    <h2 class="mb-0">Attachments</h2>
    <label class="btn btn-primary ms-auto" id="uploadBtn" role="button">
      Upload Attachment
      <input type="file" name="attachment" id="attachment" class="visually-hidden">
    </label>
  </div>

  <div id="attachments" class="list-group mb-5 col-12 col-md-6 mx-auto">
    <?php
    $attachments = File::get_files($article->articleId, true);
    foreach ($attachments as $a) {
      echo '
    <div class="list-group-item d-flex align-items-center">
      <p class="m-0">' . $a->fileName . '</p>
      <small class="ms-3 text-muted">' . $a->fileSize . '</small>
      <div class="ms-auto btn-group" role="group" aria-label="Attachment Actions">
        <a class="upload-btn btn btn-outline-primary" href="' . $a->get_url() . '" downlod="' . $a->fileName . '">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download"
            viewBox="0 0 16 16">
            <path
              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
            <path
              d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
          </svg>
        </a>
        <button type="button" class="delete-btn btn btn-outline-danger" data-file-id="' . $a->fileId . '" onclick="deleteAttachmentOnClick(this)">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill"
            viewBox="0 0 16 16">
            <path
              d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z" />
          </svg>
        </button>
      </div>
    </div>
      ';
    }
    ?>
  </div>


  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitleId">Delete confirmation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this article? This action is not reversible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

          <form action="<?= BASE_URL . '/articleEdit/delete_article.php' ?>" method="post">
            <button type="submit" class="btn btn-danger">Delete</button>
            <input type="hidden" name="articleId" value="<?= $article->articleId ?>">
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Publish Modal -->
  <div class="modal fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="pmodalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pmodalTitleId">Publish Article</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to publish this article? Once an article is published, it can no longer be edited.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

          <form action="<?= BASE_URL . '/articleEdit/publish_article.php' ?>" method="post">
            <button id="publishSubmit" type="submit" class="btn btn-primary">Publish</button>
            <input type="hidden" name="articleId" value="<?= $article->articleId ?>">
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  const WORDS_PER_MIN = 250;
  const IMAGE_READ_TIME = 12;

  $(() => {
    Quill.register('modules/imageUploader', ImageUploader);
    Quill.register('modules/blotFormatter', QuillBlotFormatter.default);

    var toolbarOptions = [
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      ['bold', 'italic', 'underline', 'strike'], // toggled buttons
      ['blockquote', 'code-block'],
      [{ 'header': 1 }, { 'header': 2 }], // custom button values
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'script': 'sub' }, { 'script': 'super' }], // superscript/subscript
      [{ 'indent': '-1' }, { 'indent': '+1' }], // outdent/indent
      [{ 'direction': 'rtl' }], // text direction
      [{ 'size': ['small', false, 'large', 'huge'] }], // custom dropdown
      [{ 'color': [] }, { 'background': [] }], // dropdown with defaults from theme
      [{ 'font': [] }],
      [{ 'align': [] }],
      ['link', 'image', 'video'],
      ['clean'] // remove formatting button
    ];
    // QuillJS editor
    var quill = new Quill('#editor', {
      modules: {
        toolbar: toolbarOptions,
        imageUploader: { upload: uploader },
        blotFormatter: {}
      },
      placeholder: 'Write an article...',
      theme: 'snow',
    });
    console.log(quill);
    quill.root.innerHTML = $('#template').html();
    $('#template').remove();

    function uploader(file, action = '<?= BASE_URL ?>/articleEdit/ajax_upload_image.php', rel = '..') {
      return new Promise((resolve, reject) => {
        let formData = new FormData();
        formData.append('myFile', file);
        formData.append('userId', <?= $_SESSION['userId'] ?>);
        formData.append('articleId', <?= $_GET['articleId'] ?>);
        $.ajax({
          type: 'POST',
          url: action,
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'text',
          statusCode: {
            200: (result) => { resolve(rel + result); },
            201: (msg) => {
              reject();
              $('.toast-container').append(`
                    <div id="uploadToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                      <div class="d-flex">
                        <div class="toast-body">Upload Failed: ${msg}</div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                      </div>
                    </div>
                  `);
              $('#uploadToast').on('hidden.bs.toast', function () { $(this).remove(); });
              const toast = new bootstrap.Toast($('#uploadToast'), { delay: 3000 });
              toast.show();
            }
          }
        });
      });
    }


    // save article using AJAX with JQuery
    $('#saveBtn').on('click', function (_event, extraCallback = null) {
      $.post('<?= BASE_URL . '/articleEdit/ajax_save_article.php' ?>', {
        articleId: <?= $article->articleId ?>,
        title: $('#title').val(),
        category: $('#category').val(),
        content: quill.root.innerHTML,
        readTime: calculateReadTime(),
        thumbnail: $('.ql-editor img').attr('src') || null
      })
        .done(() => {
          $('.toast-container').append(`
              <div id="saveToast" class="toast text-bg-success" role="alert" aria-live="polite" aria-atomic="true">
                <div class="d-flex">
                  <div class="toast-body">Successfully Saved</div>
                  <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
              </div>
            `);
          if (extraCallback != null) extraCallback();
        }).fail(() => {
          $('.toast-container').append(`
              <div id="saveToast" class="toast text-bg-danger" role="alert" aria-live="polite" aria-atomic="true">
                <div class="d-flex">
                  <div class="toast-body">Couldn't Save</div>
                  <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
              </div>
            `);
        }).always(() => {
          $('#saveToast').on('hidden.bs.toast', function () { $(this).remove(); });
          const toast = new bootstrap.Toast($('#saveToast'), { delay: 1000 });
          toast.show();
        });
    });

    $('#publishBtn').on('click', () => { $('#saveBtn').trigger('click') });
    $('#previewBtn').on('click', () => {
      $('#saveBtn').trigger('click', [() => {
        location.href = '<?= BASE_URL ?>/articleEdit/preview.php?articleId=<?= $article->articleId ?>'
      }])
    });
    $('#returnBtn').on('click', () => {
      $('#saveBtn').trigger('click', [() => {
        location.href = $('#returnBtn').attr('href');
      }])
    });

    function formatSize(size) {
      if (size < 1000) return size + ' Bytes';
      else if (size < 1_000_000) return (size / 1000).toFixed(2) + ' kB';
      else return (size / 1_000_000).toFixed(2) + ' MB';
    }

    $('#attachment').on('change', () => {
      const file = $('#attachment')[0].files[0];
      uploader(file, '<?= BASE_URL ?>/articleEdit/ajax_upload_attachment.php', '').then(uploadJSON => {
        const upload = JSON.parse(uploadJSON);
        let fileSizeString = formatSize(file.size);
        $('#attachments').append(`
    <div class="list-group-item d-flex align-items-center">
      <p class="m-0">${upload.fileName}</p>
      <small class="ms-3 text-muted">${fileSizeString}</small>
      <div class="ms-auto btn-group" role="group" aria-label="Attachment Actions">
        <a class="dload-btn btn btn-outline-primary" href="${upload.fileUrl}" download="${upload.fileName}">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download"
            viewBox="0 0 16 16">
            <path
              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
            <path
              d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
          </svg>
        </a>
        <button type="button" class="delete-btn btn btn-outline-danger" data-file-id="${upload.fileId}" onclick="deleteAttachmentOnClick(this)">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill"
            viewBox="0 0 16 16">
            <path
              d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z" />
          </svg>
        </button>
      </div>
    </div>
        `);

      });
    });

    $.each($('#attachments small'), function () {
      $(this).text(formatSize($(this).text()));
    });

    function calculateReadTime() {
      const words = quill.root.innerText.trim().split(/\s+/).length;
      const images = $('.ql-editor img').length;
      const imageReadTimeSecs =
        images > 10
          ? 10 * (IMAGE_READ_TIME + 3) / 2 + 3 * (images - 10)
          : images * (2 * IMAGE_READ_TIME + 1 - images) / 2;
      return Math.round(words / WORDS_PER_MIN + imageReadTimeSecs / 60)
    }

  });

  function deleteAttachmentOnClick(thisEl) {
    const item = $(thisEl).parents('.list-group-item');
    $.post('<?= BASE_URL ?>/articleEdit/ajax_delete_attachment.php', { fileId: $(thisEl).data('fileId') })
      .done(() => {
        console.log("deleted");
        item.addClass('fade');
        setTimeout(() => item.remove(), 150);
      })
      .fail(() => { console.log("didnt delete " + $(thisEl).data('fileId')); })

  }
</script>

<?php
include 'footer.html';
?>